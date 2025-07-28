<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogger; // ✅ เพิ่มเข้ามา

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        $financials = $this->financialSummary();

        // ✅ เพิ่ม Log
        ActivityLogger::log('เข้าดูรายการสินค้า', [
            'total_products' => $products->count()
        ]);

        return view('products.index', array_merge(
            ['products' => $products],
            $financials
        ));
    }

    public function create()
    {
        // ✅ เพิ่ม Log
        ActivityLogger::log('เข้าหน้าเพิ่มสินค้าใหม่');

        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_code' => 'required|unique:products',
            'product_name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $filename = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('storage/img'), $filename);
            $img = $imagePath['image'] = '/public/storage/img/' . $filename;
        }

        Product::create([
            'product_code' => $request->product_code,
            'product_name' => $request->product_name,
            'category' => $request->category,
            'price' => $request->price,
            'cost' => $request->cost,
            'stock' => $request->stock,
            'barcode' => $request->barcode,
            'image' => $img,
        ]);

        // ✅ เพิ่ม Log
        ActivityLogger::log('เพิ่มสินค้าใหม่', [
            'product_code' => $request->product_code,
            'product_name' => $request->product_name,
            'stock' => $request->stock
        ]);

        return redirect()->back()->with('success', 'เพิ่มสินค้าเรียบร้อย!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $filename = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('storage/img'), $filename);
            $product->image = $imagePath['image'] = '/public/storage/img/' . $filename;
        }

        $product->update([
            'product_name' => $request->product_name,
            'category' => $request->category,
            'price' => $request->price,
            'cost' => $request->cost,
            'stock' => $request->stock,
            'barcode' => $request->barcode,
            'image' => $product->image,
        ]);

        // ✅ เพิ่ม Log
        ActivityLogger::log('แก้ไขสินค้า', [
            'product_code' => $product->product_code,
            'product_name' => $product->product_name,
            'new_stock' => $request->stock
        ]);

        return redirect()->back()->with('success', 'แก้ไขสินค้าเรียบร้อย!');
    }

    public function destroy(Product $product)
    {
        $productCode = $product->product_code;
        $productName = $product->product_name;

        // ตรวจสอบว่ามีการใช้งานใน sale_items อยู่หรือไม่
        if ($product->saleItems()->exists()) {
            return redirect()->route('products.index')
                ->with('error', "❌ ไม่สามารถลบสินค้า '{$productName}' ได้ เพราะมีการใช้งานในประวัติการขาย");
        }

        $product->delete();

        // ✅ เพิ่ม Log
        ActivityLogger::log('ลบสินค้า', [
            'product_code' => $productCode,
            'product_name' => $productName
        ]);

        return redirect()->route('products.index')->with('success', 'ลบสินค้าสำเร็จ');
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->input('ids', []); // ✅ ถ้า null → เป็น []

        if (empty($ids)) {
            return response()->json(['message' => '❌ กรุณาเลือกสินค้าอย่างน้อย 1 รายการ'], 400);
        }

        try {
            Product::whereIn('id', $ids)->delete();

            ActivityLogger::log('ลบสินค้าหลายรายการ', [
                'ids' => $ids
            ]);

            return response()->json(['message' => '✅ ลบสินค้าที่เลือกสำเร็จ']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }




    public function importCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        if (!$request->file('csv_file')->isValid()) {
            return redirect()->route('products.index')->with('error', 'ไฟล์ไม่ถูกต้อง');
        }

        $file = fopen($request->file('csv_file')->getPathname(), 'r');
        $header = fgetcsv($file);
        $importCount = 0;

        while (($row = fgetcsv($file)) !== false) {
            $imageUrl = isset($row[7]) ? trim($row[7]) : null;

            if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                try {
                    $imageContent = file_get_contents($imageUrl);
                    $filename = uniqid('product_') . '.jpg';
                    $savePath = public_path('storage/img/' . $filename);

                    if (!file_exists(public_path('storage/img'))) {
                        mkdir(public_path('storage/img'), 0777, true);
                    }

                    file_put_contents($savePath, $imageContent);
                    $imagePath = '/public/storage/img/' . $filename;
                } catch (\Exception $e) {
                    $imagePath = Product::where('product_code', $row[7])->value('image');
                }
            } else {
                $imagePath = Product::where('product_code', $row[7])->value('image');
            }

            Product::updateOrCreate(
                ['product_code' => $row[0]],
                [
                    'product_name' => $row[1],
                    'category' => $row[2] ?: null,
                    'price' => $row[3],
                    'cost' => $row[4] ?: null,
                    'stock' => $row[5],
                    'barcode' => $row[6] ?: null,
                    'image' => $imagePath,
                ]
            );

            $importCount++;
        }

        fclose($file);

        // ✅ เพิ่ม Log
        ActivityLogger::log('นำเข้าสินค้าจาก CSV', [
            'imported_products' => $importCount
        ]);

        return redirect()->route('products.index')->with('success', 'นำเข้าสินค้าจาก CSV สำเร็จ');
    }

    public function financialSummary()
    {
        $totalRevenue = Product::where('stock', '>', 0)->count();
        $profit = Product::whereBetween('stock', [1, 5])->count();
        $loss = Product::where('stock', '=', 0)->count();

        return compact('totalRevenue', 'profit', 'loss');
    }
}
