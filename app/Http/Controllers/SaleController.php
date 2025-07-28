<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger; // ✅ เพิ่มเข้ามา

class SaleController extends Controller
{
    public function print($id)
    {
        $sale = Sale::with('items.product')->findOrFail($id);

        // ✅ เพิ่ม Log เท่านั้น
        ActivityLogger::log('พิมพ์ใบเสร็จ', [
            'sale_code' => $sale->sale_code,
            'total' => $sale->total
        ]);

        return view('sales.print', compact('sale'));
    }

    public function history()
    {
        $sales = Sale::with('items.product')->orderBy('created_at', 'desc')->paginate(10);

        $todaySalesCount = Sale::all()->count();
        $monthSalesTotal = Sale::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $financials = $this->financialSummary();

        // ✅ เพิ่ม Log
        ActivityLogger::log('เข้าดูประวัติการขาย', [
            'sales_count' => $sales->total()
        ]);

        return view(
            'sales.history',
            compact('sales',  'todaySalesCount', 'monthSalesTotal'),
            array_merge($financials)
        );
    }

    public function financialSummary()
    {
        $totalRevenue = Sale::sum('total');
        $totalCost = Product::sum('cost');
        $to = $totalRevenue - $totalCost;
        $profit = $to < 0 ? 0 : $to;
        $tax = Product::sum('cost');
        $lo = $totalCost - $totalRevenue;
        $loss = $lo < 0 ? 0 : $lo;

        return compact('totalRevenue', 'profit', 'tax', 'loss');
    }

    public function export($id)
    {
        $sale = Sale::with('items.product')->findOrFail($id);

        // ✅ เพิ่ม Log
        ActivityLogger::log('Export ใบเสร็จ (CSV)', [
            'sale_code' => $sale->sale_code,
            'total' => $sale->total
        ]);

        $csv = "สินค้า,ราคา,จำนวน,ราคารวม\n";
        foreach ($sale->items as $item) {
            $csv .= "{$item->product->product_name},{$item->price},{$item->quantity},{$item->subtotal}\n";
        }
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"sale_{$sale->sale_code}.csv\"");
    }

    public function share($id)
    {
        // ✅ เพิ่ม Log
        ActivityLogger::log('แชร์ใบเสร็จ', ['sale_id' => $id]);

        return redirect()->route('sales.show', $id);
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->input('ids');

        if (!empty($ids)) {
            DB::transaction(function () use ($ids) {
                $sales = Sale::whereIn('id', $ids)->get(['sale_code', 'total']);

                // ✅ เพิ่ม Log
                foreach ($sales as $sale) {
                    ActivityLogger::log('ลบประวัติการขาย', [
                        'sale_code' => $sale->sale_code,
                        'total' => $sale->total
                    ]);
                }

                SaleItem::whereIn('sale_id', $ids)->delete();
                Sale::whereIn('id', $ids)->delete();
            });

            return response()->json(['message' => 'ลบรายการที่เลือกสำเร็จ']);
        }

        return response()->json(['message' => 'ไม่พบรายการที่เลือก'], 400);
    }
}
