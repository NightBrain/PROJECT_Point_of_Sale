@section('title', 'Dashboard')
<div class="p-6 space-y-6">

    <h1 class="text-3xl font-extrabold text-gray-700 mb-6 drop-shadow">
        📊 Dashboard
    </h1>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

            <div
                class="bg-gradient-to-r from-blue-400 to-blue-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">รายได้ทั้งหมด</div>
                    <div class="text-2xl font-bold">{{ number_format($totalRevenue, 2) }} ฿</div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-green-400 to-green-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">ต้นทุน</div>
                    <div class="text-2xl font-bold">{{ number_format($tax, 2) }} ฿</div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">กำไร</div>
                    <div class="text-2xl font-bold">{{ number_format($profit, 2) }} ฿</div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-red-400 to-red-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">ขาดทุน</div>
                    <div class="text-2xl font-bold">{{ number_format($loss, 2) }} ฿</div>
                </div>
            </div>

        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

            <div
                class="bg-gradient-to-r from-purple-400 to-purple-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">ยอดขายวันนี้</div>
                    <div class="text-2xl font-bold">{{ number_format($todaySales, 2) }} ฿</div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-green-400 to-green-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fas fa-box-open mr-4"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">จำนวนสินค้าทั้งหมด</div>
                    <div class="text-2xl font-bold">{{ $totalRevenue1 }} ชิ้น</div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">สินค้าในสต๊อกต่ำ</div>
                    <div class="text-2xl font-bold">{{ $profit2 }} ชิ้น</div>
                </div>
            </div>

            <div
                class="bg-gradient-to-r from-red-400 to-red-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">สินค้าสต๊อกหมด</div>
                    <div class="text-2xl font-bold">{{ $loss3 }} ชิ้น</div>
                </div>
            </div>

        </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- ✅ ฝั่งซ้าย: ยอดขายวันนี้ + ยอดขาย 5 เดือน + สินค้าขายดี --}}
        <div class="space-y-6">

            {{-- ✅ กราฟยอดขาย 5 เดือนล่าสุด --}}
            <div
                class="p-6 rounded-xl shadow-lg bg-gradient-to-br from-green-100 via-green-50 to-white justify-center grid content-center">
                <h1 class="text-2xl font-semibold text-green-700 mb-4">ยอดขาย 5 เดือนล่าสุด</h1>
                <canvas id="salesChart" class="w-96 h-64"></canvas>
            </div>

            {{-- ✅ กราฟสินค้าขายดี 5 อันดับ --}}
            <div
                class="p-6 rounded-xl shadow-lg bg-gradient-to-br from-green-100 via-green-50 to-white justify-center grid content-center">
                <h2 class="text-lg font-semibold text-purple-700 mb-4">🏆 สินค้าขายดี 5 อันดับ</h2>
                <canvas id="topProductsChart" class="w-64 h-64"></canvas>
            </div>
        </div>

        {{-- ✅ ฝั่งขวา: สินค้าสต๊อกต่ำ --}}
        <div class="p-6 rounded-xl shadow-lg bg-gradient-to-br from-red-100 via-red-50 to-white">
            <div class="flex items-center mb-4">
                <h2 class="text-xl font-extrabold text-red-700 ml-3 drop-shadow">
                    กรุณาสต๊อกสินค้าด่วน
                </h2>
            </div>

            @forelse($lowStockProducts as $product)
                <div
                    class="flex items-center justify-between bg-white rounded-lg p-4 mb-2 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center space-x-4">
                        <div
                            class="bg-red-500 text-white rounded-full w-12 h-12 shadow text-center grid content-center text-3xl">
                            <i class='bx bxs-error bx-tada' style='color:#fdcc00'></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-800">{{ $product->product_name }}</span>
                            <span class="text-gray-500 text-sm">รหัสสินค้า: {{ $product->product_code ?? '-' }}</span>
                        </div>
                    </div>
                    <span class="bg-red-100 text-red-700 font-bold px-3 py-1 rounded-full shadow text-sm">
                        เหลือ {{ $product->stock }}
                    </span>
                </div>
            @empty
                <div class="bg-gray-50 text-gray-500 p-4 rounded-lg text-center">
                    ✅ ไม่มีสินค้าใกล้หมด
                </div>
            @endforelse
        </div>

    </div>
</div>

{{-- โหลด Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ✅ กราฟยอดขาย 5 เดือนล่าสุด
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($monthlySales as $month => $amount)
                        '{{ $month }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'ยอดขาย (บาท)',
                    data: [
                        @foreach ($monthlySales as $amount)
                            {{ $amount }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(34,197,94,0.7)',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // ✅ กราฟสินค้าขายดี 5 อันดับ
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(topProductsCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach ($topProducts as $item)
                        '{{ addslashes($item->product->product_name ?? 'ไม่ทราบ') }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'จำนวนขาย',
                    data: [
                        @foreach ($topProducts as $item)
                            {{ $item->total_sold }},
                        @endforeach
                    ],
                    backgroundColor: ['#A78BFA', '#F472B6', '#FBBF24', '#34D399', '#60A5FA'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
