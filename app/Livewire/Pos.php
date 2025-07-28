<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class Pos extends Component
{
    use WithPagination;
public $lastSaleId = null; // ✅ เพิ่ม property ไว้จำ ID ล่าสุด
    public $cart = [];
    public $total = 0;
    public $search = '';
    public $paymentMethod = 'cash';
    public $selectedCategory = '';
    public $receivedAmount = '';
    public $change = 0;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product)
            return;

        if (!is_array($this->cart)) {
            $this->cart = [];
        }

        $currentQty = $this->cart[$productId]['quantity'] ?? 0;

        if ($currentQty + 1 > $product->stock) {
            session()->flash('message', "❌ สินค้า {$product->product_name} มีสต๊อกเพียง {$product->stock} ชิ้นเท่านั้น");
            return;
        }

        $this->cart[$productId] = [
            'id' => $product->id,
            'name' => $product->product_name,
            'price' => $product->price,
            'quantity' => $currentQty + 1,
        ];

        $this->calculateTotal();
    }

    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
        }
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        if (!is_array($this->cart)) {
            $this->cart = [];
        }

        $this->total = collect($this->cart)
            ->sum(fn($item) => $item['price'] * $item['quantity']);
        $this->calculateChange();
    }

    public function updatedReceivedAmount()
    {
        $this->calculateChange();
    }

    public function updatedPaymentMethod()
    {
        if ($this->paymentMethod !== 'cash') {
            $this->receivedAmount = '';
            $this->change = 0;
        } else {
            $this->calculateChange();
        }
    }

    public function calculateChange()
    {
        if (
            $this->paymentMethod === 'cash' &&
            is_numeric($this->receivedAmount) &&
            $this->receivedAmount >= $this->total
        ) {
            $this->change = $this->receivedAmount - $this->total;
        } else {
            $this->change = 0;
        }
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            return;
        }

        DB::transaction(function () {
            $sale = Sale::create([
                'sale_code' => 'SL' . now()->format('YmdHis'),
                'total' => $this->total,
                'payment_method' => $this->paymentMethod,
            ]);

            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                $product = Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $this->lastSaleId = $sale->id; // ✅ บันทึก ID
        });

        $this->cart = [];
        $this->total = 0;
        $this->paymentMethod = 'cash';

        session()->flash('message', 'ชำระเงินสำเร็จ! ✅');
    }


    public function clearCart()
    {
        $this->cart = [];
        $this->total = 0;
        $this->receivedAmount = '';
        $this->change = 0;

        session()->flash('message', '🗑️ ล้างตะกร้าสินค้าสำเร็จ!');
    }

    public function render()
    {
        $categories = Product::select('category')->distinct()->pluck('category');

        $products = Product::select('id', 'product_code', 'product_name', 'price', 'stock', 'category', 'image')
            ->when($this->selectedCategory, fn($q) => $q->where('category', $this->selectedCategory))
            ->when(strlen($this->search) >= 2, function ($q) {
                $search = $this->search;
                $q->where('product_code', 'like', "%$search%")
                    ->orWhere('product_name', 'like', "%$search%");
            })
            ->paginate(9);
        
        $lowStockProducts = Product::where('stock', '<', 5)->get(); // ✅ เพิ่ม

        return view('livewire.pos', [
            'products' => $products,
            'categories' => $categories,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
