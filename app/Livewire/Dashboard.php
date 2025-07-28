<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $products;
    public $lowStockProducts;
    public $todaySales;
    public $monthSales;
    public $topProducts;
    public $monthlySales = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->products = Product::all();

        // สินค้าสต๊อกต่ำกว่า 5
        $this->lowStockProducts = Product::where('stock', '<', 5)->get();

        $this->todaySales = Sale::whereDate('created_at', Carbon::today())->sum('total');
        $this->monthSales = Sale::whereMonth('created_at', Carbon::now()->month)->sum('total');

        $this->topProducts = SaleItem::selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(6)
            ->get();

        // ดึงยอดขาย 5 เดือนล่าสุด (เดือนนี้และย้อนหลัง 4 เดือน)
        $monthlySalesRaw = Sale::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(total) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(4)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // เตรียม array ให้ครบ 5 เดือน (กรณีบางเดือนยังไม่มีข้อมูล)
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->format('Y-m');
            $months[$m] = 0;
        }
        foreach ($monthlySalesRaw as $row) {
            $months[$row->month] = $row->total;
        }
        $this->monthlySales = $months;
    }

    public function render()
    {
        $this->loadData();
        $financials = $this->financialSummary();

        $data = [
            'products' => $this->products,
            'lowStockProducts' => $this->lowStockProducts,
            'todaySales' => $this->todaySales,
            'monthSales' => $this->monthSales,
            'topProducts' => $this->topProducts,
            'monthlySales' => $this->monthlySales,
        ];

        return view('livewire.dashboard', array_merge($data, $financials));
    }
    public function financialSummary()
    {
        // ✅ เดือนนี้
        $sales = Sale::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->get();

        // ✅ 1. รายได้ที่ขายได้ทั้งหมด (ยอดขาย)
        $totalRevenue = Sale::sum('total');

        // ✅ 2. กำไรที่ขายได้ (รายได้ - ต้นทุน)
        $totalCost = Product::sum('cost');
        $to = $totalRevenue - $totalCost;
        $profit = $to < 0 ? 0 : $to;

        // ✅ 3. ต้นทุน
        $tax = Product::sum('cost');

        // ✅ 4. ขาดทุน (เฉพาะเดือนนี้)
        $lo = $totalCost - $totalRevenue;
        $loss = $lo < 0 ? 0 : $lo;

        $totalRevenue1 = Product::where('stock', '>', 0)->count();

        // ✅ 2. กำไรที่ขายได้ (รายได้ - ต้นทุน)

        $profit2 = Product::whereBetween('stock', [1, 5])->count();

        // ✅ 4. ขาดทุน (เฉพาะเดือนนี้)

        $loss3 = Product::where('stock', '=', 0)->count();

        return compact('totalRevenue', 'profit', 'tax', 'loss', 'totalRevenue1', 'profit2', 'loss3');
    }
}
