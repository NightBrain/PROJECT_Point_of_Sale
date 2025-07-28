@extends('components.layouts.app')
@section('title', 'Order History')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css" />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="container mx-auto p-6 border-gray-100">

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
        {{-- Summary Boxes --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div
                class="col-span-2 bg-gradient-to-r from-blue-400 to-blue-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">จำนวนรายการขายทั้งหมด</div>
                    <div class="text-2xl font-bold">{{ $todaySalesCount }} รายการ</div>
                </div>
            </div>

            <div
                class="col-span-2 bg-gradient-to-r from-purple-400 to-purple-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">ยอดขายเดือนนี้</div>
                    <div class="text-2xl font-bold">{{ number_format($monthSalesTotal, 2) }} ฿</div>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <button id="delete-selected"
                class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded shadow">
                <i class="fa-solid fa-trash mr-2"></i> ลบที่เลือก
            </button>
        </div {{-- DataTable --}} <div class="overflow-x-auto">
        <table id="sales-table" class="min-w-full border border-gray-300 rounded stripe hover bg-white" style="width:100%">
            <thead class="bg-gradient-to-r from-blue-100 to-blue-50">
                <tr>
                    <th class="border p-2 text-center">
                        <input type="checkbox" id="select-all" class="w-4 h-4">
                    </th>
                    <th class="border p-2 text-center">รหัสสั่งซื้อ</th>
                    <th class="border p-2 text-center">วันที่</th>
                    <th class="border p-2 text-center">ช่องทางชำระเงิน</th>
                    <th class="border p-2 text-center">รวมทั้งหมด (฿)</th>
                    <th class="border p-2 text-center">รายละเอียด</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <td class="border p-2 text-center">
                        <input type="checkbox" class="sale-checkbox w-4 h-4" value="{{ $sale->id }}">
                    </td>
                    <td class="border p-2 text-md">{{ $sale->sale_code }}</td>
                    <td class="border p-2 text-md">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td class="border p-2 text-md capitalize">{{ $sale->payment_method }}</td>
                    <td class="border p-2 text-md text-right">{{ number_format($sale->total, 2) }} ฿</td>
                    <td class="border p-2 text-center">
                        <!-- ปุ่ม Export (ลิงก์ไป controller) -->
                        <a href="{{ route('sales.export', $sale->id) }}"
                            class="bg-gradient-to-r from-green-400 to-green-600 text-white text-xl font-bold px-3 py-1 rounded-l-full"
                            target="_blank">
                            Export
                        </a>

                        <!-- ปุ่ม Print -->
                        <a type="button" onclick="printBill({{ $sale->id }})"
                            class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white text-xl font-bold px-3 py-1 cursor-pointer">
                            Print
                        </a>

                        <!-- ปุ่ม Share -->
                        <a type="button" onclick="shareBill({{ $sale->id }})"
                            class="bg-gradient-to-r from-purple-400 to-purple-600 text-white text-xl font-bold px-3 py-1 rounded-r-full cursor-pointer">
                            Share
                        </a>

                    </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    </div>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

    <script>
        function printBill(saleId) {
            window.open(`/sales/print/${saleId}`, '_blank');
        }

        function shareBill(saleId) {
            const url = `${window.location.origin}/sales/${saleId}/share`;
            if (navigator.share) {
                navigator.share({
                    title: 'แชร์ใบเสร็จ',
                    url: url,
                }).catch(console.error);
            } else {
                prompt('คัดลอก URL สำหรับแชร์:', url);
            }
        }

        $(document).ready(function() {
            $('#sales-table').DataTable();

            // toggle child row event
            $('#sales-table tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = $('#sales-table').DataTable().row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    var childContent = tr.attr('data-child-row');
                    row.child(childContent).show();
                    tr.addClass('shown');
                }
            });
        });

        $(document).ready(function() {
            // ✅ เลือก/ไม่เลือกทั้งหมด
            $('#select-all').on('change', function() {
                $('.sale-checkbox').prop('checked', $(this).prop('checked'));
            });

            // ✅ ปุ่มลบที่เลือก
            $('#delete-selected').on('click', function() {
                let selected = [];
                $('.sale-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });

                if (selected.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'แจ้งเตือน',
                        text: 'กรุณาเลือกอย่างน้อย 1 รายการ',
                        customClass: {
                            popup: 'bg-white rounded-3xl shadow-2xl p-6',
                        },
                    });
                    return;
                }

                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: `คุณต้องการลบ ${selected.length} รายการนี้หรือไม่?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#e3342f',
                    customClass: {
                        popup: 'bg-white rounded-3xl shadow-2xl p-6'
                    },
                    timer: 5000,
                    timerProgressBar: true,
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('sales.deleteSelected') }}",
                            method: "POST",
                            data: {
                                ids: selected,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '✅ ลบสำเร็จ',
                                    text: res.message,
                                    customClass: {
                                        popup: 'bg-white rounded-3xl shadow-2xl p-6',
                                    },
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: '❌ เกิดข้อผิดพลาด',
                                    text: 'ไม่สามารถลบรายการที่เลือกได้',
                                    customClass: {
                                        popup: 'bg-white rounded-3xl shadow-2xl p-6',
                                    },
                                });
                            }
                        });

                    }
                });
            });
        });
    </script>
@endsection
