@section('title', 'POS')
<div class="p-6">

    {{-- ส่วนค้นหาและเลือกหมวดหมู่ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 max-w-8xl mx-auto px-4 md:px-0">

        <div class="flex gap-3 w-full md:w-auto">
            <select wire:model.live="selectedCategory"
                class="border border-gray-300 p-3 rounded-xl w-36 md:w-auto focus:outline-none focus:ring-4 focus:ring-blue-400 transition">
                <option value="">-- ทุกหมวดหมู่ --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>

            <input type="text" wire:model.live="search" placeholder="🔍 ค้นหาสินค้า..."
                class="border border-gray-300 p-3 rounded-xl flex-1 focus:outline-none focus:ring-4 focus:ring-blue-400 transition" />
        </div>

        {{-- เลือกวิธีชำระเงิน --}}
        <div class="w-full md:w-auto">
            <select wire:model="paymentMethod"
                class="border border-gray-300 p-3 rounded-xl w-full md:w-48 focus:outline-none focus:ring-4 focus:ring-green-400 transition">
                <option value="cash">💵 เงินสด</option>
                <option value="qr">📱 QR Code</option>
                <option value="transfer">🏦 โอนผ่านธนาคาร</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-8xl mx-auto px-4 md:px-0">

        {{-- รายการสินค้า --}}
        <div
            class="col-span-1 md:col-span-2 bg-white rounded-2xl shadow-xl p-6 overflow-hidden select-text flex flex-col">
            <div class="flex justify-between">
                <h2 class="text-2xl font-semibold mb-6 flex items-center text-gray-800">
                    <i class="fas fa-box-open text-blue-600 mr-4"></i> สินค้า
                </h2>
                <div>{{-- ✅ แจ้งเตือนสต๊อกต่ำ --}}
                    @if ($lowStockProducts->count())
                        <div class="mb-3 p-3 bg-red-100 text-red-800 rounded-xl">
                            <strong>⚠️ สินค้าสต๊อกต่ำ:</strong>
                            @foreach ($lowStockProducts as $p)
                                <span class="inline-block bg-red-200 text-red-700 px-2 py-1 rounded text-xs mr-1">
                                    {{ $p->product_name }} ({{ $p->stock }})
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if ($products->count())
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach ($products as $product)
                        <div class="border border-gray-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-base truncate">
                                        {{ $product->product_name }}
                                    </h3>
                                    <p class="text-gray-500 text-xs mb-2">รหัส: {{ $product->product_code }}</p>
                                    <p class="text-green-600 font-bold text-lg">{{ number_format($product->price, 2) }}
                                        ฿
                                    </p>
                                    <p class="text-xs text-gray-600 mt-1">คงเหลือ: {{ $product->stock }}</p>
                                </div>
                                <div>
                                    <img src="{{ $product->image }}" alt="Image" width="100px" height="100px"
                                        class="rounded-full shadow-md border">
                                </div>
                            </div>
                            <button wire:click="addToCart({{ $product->id }})"
                                class="w-full mt-4 bg-green-600 text-white py-2 rounded-full hover:bg-green-700 transition font-semibold text-sm shadow-sm">
                                ➕ เพิ่ม
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center text-gray-400 p-10 font-medium text-lg">
                    ❌ ไม่พบสินค้า
                </div>
            @endif
        </div>

        {{-- ตะกร้าสินค้า --}}
        <div class="bg-gray-50 rounded-3xl shadow-xl p-6 flex flex-col select-text">
            <h2 class="text-2xl font-semibold mb-6 flex items-center text-gray-800">
                <i class="fas fa-shopping-cart text-orange-500 mr-4"></i> ตะกร้าสินค้า
            </h2>
            <div>
                {{-- ข้อความแจ้งเตือนแบบง่าย --}}
                @if (session()->has('message'))
                    <div
                        class="mb-5 p-4 bg-green-100 text-green-800 rounded-xl shadow-md max-w-xl mx-auto text-center font-medium select-text">
                        {{ session('message') }}
                    </div>
                @endif
            </div>
            <div>
                {{-- แสดงรายการสินค้าในตะกร้า --}}
                <table class="w-full border border-gray-300 rounded-xl mb-8 overflow-hidden">
                    <thead class="bg-gray-100 text-sm font-semibold">
                        <tr>
                            <th class="border-b p-3 text-left">สินค้า</th>
                            <th class="border-b p-3 text-right">จำนวน</th>
                            <th class="border-b p-3 text-right">ราคา</th>
                            <th class="border-b p-3 text-center">ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cart as $item)
                            <tr class="hover:bg-gray-200 transition text-sm text-gray-900">
                                <td class="border-b p-3">{{ $item['name'] }}</td>
                                <td class="border-b p-3 text-right">{{ $item['quantity'] }}</td>
                                <td class="border-b p-3 text-right">
                                    {{ number_format($item['price'] * $item['quantity'], 2) }}
                                </td>
                                <td class="border-b p-3 text-center">
                                    <button wire:click="removeFromCart({{ $item['id'] }})"
                                        class="text-red-600 hover:text-red-800 rounded-full p-1 transition shadow-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-400 py-10 font-medium">
                                    ไม่มีสินค้าในตะกร้า
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="flex justify-between items-center mb-8">
                    <button wire:click="clearCart"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-full shadow-md transition font-semibold disabled:opacity-50"
                        @if (empty($cart)) disabled @endif>
                        🗑️ ล้างตะกร้าทั้งหมด
                    </button>
                    <p class="text-xl font-extrabold text-gray-800 select-none">
                        รวม: {{ number_format($total, 2) }} ฿
                    </p>
                </div>
                @if ($paymentMethod === 'cash')
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">💵 รับเงินมา</label>
                        <input type="number" wire:model.live="receivedAmount"
                            class="border border-gray-300 rounded-xl w-full p-4 focus:outline-none focus:ring-4 focus:ring-green-300 transition text-lg"
                            placeholder="เช่น 1000" />
                        @if ($receivedAmount !== '' && is_numeric($receivedAmount))
                            @if ($receivedAmount < $total)
                                <div class="mt-3 text-center font-semibold text-red-600 select-none text-2xl">
                                    เงินไม่พอกับราคาสินค้า
                                </div>
                            @else
                                <div class="mt-3 text-center font-extrabold text-green-700 text-2xl select-none">
                                    เงินทอน: {{ number_format($change, 2) }} ฿
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
                <button wire:click="checkout"
                    class="bg-blue-600 hover:bg-blue-700 text-white w-full py-4 rounded-3xl shadow-lg transition font-bold text-xl disabled:opacity-50"
                    @if (empty($cart) || ($paymentMethod === 'cash' && $receivedAmount < $total)) disabled @endif>
                    ✅ ชำระเงิน
                </button>
                {{-- ✅ ปุ่มพิมพ์ใบเสร็จ --}}
                @if ($lastSaleId)
                    <a href="{{ route('sales.print', $lastSaleId) }}" target="_blank"
                        class="bg-purple-600 text-white block text-center w-full py-4 rounded-3xl shadow-lg hover:bg-purple-700 font-bold text-xl transition mt-2">
                        <i class="fa fa-print"></i> พิมพ์ใบเสร็จ
                    </a>
                @endif

            </div>

        </div>

    </div>
</div>
