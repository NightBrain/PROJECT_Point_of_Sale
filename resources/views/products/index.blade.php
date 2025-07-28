@extends('components.layouts.app')
@section('title', 'Manage products')

@section('content')
    {{-- ✅ โหลด CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="p-6 mt-6" x-data="editModal()" x-cloak>

        {{-- SweetAlert2 แจ้งเตือนสถานะ --}}
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: "{{ session('success') }}",
                    customClass: {
                        popup: 'bg-white rounded-3xl shadow-2xl p-6'
                    },
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: "{{ session('error') }}",
                    customClass: {
                        popup: 'bg-white rounded-3xl shadow-2xl p-6'
                    },
                    timer: 10000,
                    timerProgressBar: true,
                    showConfirmButton: true
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อมูลไม่ถูกต้อง',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    customClass: {
                        popup: 'bg-white rounded-3xl shadow-2xl p-6'
                    },
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: true,
                });
            </script>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">

            <div
                class="col-span-2 bg-gradient-to-r from-green-400 to-green-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fas fa-box-open mr-4"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">จำนวนสินค้าทั้งหมด</div>
                    <div class="text-2xl font-bold">{{ number_format($totalRevenue) }} ชิ้น</div>
                </div>
            </div>

            <div
                class="col-span-2 bg-gradient-to-r from-yellow-400 to-yellow-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">สินค้าในสต๊อกต่ำ</div>
                    <div class="text-2xl font-bold">{{ number_format($profit) }} ชิ้น</div>
                </div>
            </div>

            <div
                class="col-span-2 bg-gradient-to-r from-red-400 to-red-600 text-white rounded-lg shadow-lg p-5 flex items-center space-x-4">
                <div class="text-4xl">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold">สินค้าสต๊อกหมด</div>
                    <div class="text-2xl font-bold">{{ number_format($loss) }} ชิ้น</div>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- ตารางสินค้า --}}
            <div class="p-6 md:col-span-2 bg-white rounded-xl shadow-xl border border-gray-100 overflow-x-auto">
                <div class="flex justify-between">
                    <h1 class="text-4xl font-extrabold text-gray-800 mb-8 flex items-center gap-3 drop-shadow">
                        <i class="fa-solid fa-boxes-stacked text-blue-600 bg-white p-2 rounded-full shadow"></i>
                        จัดการสินค้า
                    </h1>
                    {{-- ปุ่มลบหลายรายการ --}}
                    <button id="delete-selected" class="px-3 py-3 bg-red-600 text-white rounded-full hover:bg-red-700 h-12">
                        🗑️ ลบที่เลือก
                    </button>
                </div>
                <table id="productsTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-blue-100 to-blue-50">
                        <tr>
                            <th class="px-3 py-3 text-center">
                                <input type="checkbox" id="select-all" />
                            </th>
                            <th class="px-5 py-3 text-center text-sm font-bold text-gray-700 uppercase">รหัสสินค้า</th>
                            <th class="px-5 py-3 text-center text-sm font-bold text-gray-700 uppercase">รูปสินค้า</th>
                            <th class="px-5 py-3 text-center text-sm font-bold text-gray-700 uppercase">ชื่อสินค้า</th>
                            <th class="px-5 py-3 text-center text-sm font-bold text-gray-700 uppercase">หมวดหมู่</th>
                            <th class="px-5 py-3 text-center text-sm font-bold text-gray-700 uppercase">ราคา</th>
                            <th class="px-5 py-3 text-center text-sm font-bold text-gray-700 uppercase">สต๊อก</th>
                            <th class="px-5 py-3 text-center text-sm font-bold text-gray-700 uppercase">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100 transition">
                                <td class="px-3 py-3 text-center">
                                    <input type="checkbox" class="sale-checkbox" value="{{ $product->id }}" />
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-700 text-center">{{ $product->product_code }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700 text-center">
                                    <img src="{{ $product->image }}" alt="Image" width="50px" height="50px"
                                        class="rounded-full shadow-md border">
                                </td>
                                <td class="px-5 py-3 text-sm font-semibold text-gray-900 text-center">
                                    {{ $product->product_name }}</td>
                                <td class="px-5 py-3 text-sm text-gray-600 text-center">{{ $product->category ?? '-' }}</td>
                                <td class="px-5 py-3 text-sm text-right text-gray-700">
                                    {{ number_format($product->price, 2) }}</td>
                                <td class="px-5 py-3 text-sm text-center">
                                    @if ($product->stock < 5)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            <i class="fa-solid fa-exclamation-triangle mr-1"></i> {{ $product->stock }}
                                        </span>
                                    @else
                                        {{ $product->stock }}
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center text-sm space-x-3">
                                    {{-- ปุ่มแก้ไข (เปิด Modal) --}}
                                    <button
                                        @click="openEdit({{ $product->id }}, '{{ $product->product_code }}', '{{ $product->product_name }}', '{{ $product->category }}', {{ $product->price }}, {{ $product->cost ?? 0 }}, {{ $product->stock }}, '{{ $product->barcode }}')"
                                        class="text-blue-600 hover:text-blue-800 transition" title="แก้ไขสินค้า">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    {{-- ปุ่มลบ (SweetAlert2 Confirm) --}}
                                    <button type="button" @click="deleteProduct({{ $product->id }})"
                                        class="text-red-600 hover:text-red-800 transition" title="ลบสินค้า">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-500 font-medium">ไม่มีข้อมูลสินค้า</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ฟอร์มเพิ่มสินค้า --}}
            <div class="bg-gradient-to-br from-white to-green-50 rounded-xl shadow-xl p-6 border border-gray-100">
                <h2 class="text-xl font-semibold mb-4 text-gray-700 flex items-center gap-3">
                    <i class="fa-solid fa-circle-plus text-green-600"></i> เพิ่มสินค้าใหม่
                </h2>
                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @csrf
                    @php
                        $inputClass =
                            'w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-400 focus:outline-none transition';
                    @endphp

                    <div class="flex gap-4">
                        <div class="mb-4 w-1/2">
                            <label class="block mb-1 text-gray-600 font-medium">รหัสสินค้า</label>
                            <input type="text" name="product_code" class="{{ $inputClass }}" required>
                        </div>
                        <div class="mb-4 w-1/2">
                            <label class="block mb-1 text-gray-600 font-medium">หมวดหมู่</label>
                            <input type="text" name="category" class="{{ $inputClass }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-gray-600 font-medium">ชื่อสินค้า</label>
                        <input type="text" name="product_name" class="{{ $inputClass }}" required>
                    </div>

                    <div class="flex gap-4">
                        <div class="mb-4 w-1/3">
                            <label class="block mb-1 text-gray-600 font-medium">ราคา</label>
                            <input type="number" name="price" step="0.01" min="0"
                                class="{{ $inputClass }}" required>
                        </div>
                        <div class="mb-4 w-1/3">
                            <label class="block mb-1 text-gray-600 font-medium">ต้นทุน</label>
                            <input type="number" name="cost" step="0.01" min="0"
                                class="{{ $inputClass }}">
                        </div>
                        <div class="mb-4 w-1/3">
                            <label class="block mb-1 text-gray-600 font-medium">สต๊อก</label>
                            <input type="number" name="stock" min="0" class="{{ $inputClass }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-gray-600 font-medium">รูปสินค้า</label>

                        <!-- ปุ่มเลือกไฟล์ -->
                        <label for="image"
                            class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 
               text-white font-medium rounded-lg cursor-pointer hover:from-green-600 hover:to-green-700 
               transition shadow-md gap-2">
                            <i class="fa-solid fa-upload"></i> เลือกรูปสินค้า
                        </label>
                        <input type="file" name="image" id="image" accept="image/*" required class="hidden">

                        <!-- Preview รูป -->
                        <div class="mt-3 flex justify-center">
                            <img id="preview_image" src="#" alt="Preview"
                                class="hidden w-32 h-32 object-cover rounded-lg shadow-md border">
                        </div>
                    </div>

                    <script>
                        // ✅ แสดง Preview รูปเมื่อเลือกไฟล์
                        document.getElementById('image').addEventListener('change', function(e) {
                            const file = e.target.files[0];
                            const preview = document.getElementById('preview_image');

                            if (file) {
                                preview.src = URL.createObjectURL(file);
                                preview.classList.remove('hidden');
                            } else {
                                preview.src = '#';
                                preview.classList.add('hidden');
                            }
                        });
                    </script>


                    <div class="mb-6">
                        <label class="block mb-1 text-gray-600 font-medium">Barcode</label>
                        <input type="text" name="barcode" class="{{ $inputClass }}">
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition">
                        <i class="fa-solid fa-plus mr-2"></i> เพิ่มสินค้า
                    </button>
                </form>

                {{-- ✅ ฟอร์ม Import CSV --}}
                <div class="p-6 border rounded-xl mt-6">
                    <h2 class="text-xl font-semibold mb-6 text-gray-700 flex justify-center gap-3">
                        <i class="fa-solid fa-file-import text-green-600"></i> นำเข้าสินค้าจากไฟล์ CSV
                    </h2>

                    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="csv_file"
                                class="flex items-center justify-center w-full px-4 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition shadow-md gap-2">
                                <i class="fa-solid fa-file-csv"></i> เลือกไฟล์ CSV
                            </label>
                            <input type="file" name="csv_file" id="csv_file" accept=".csv,text/csv" required
                                class="hidden">
                            <p id="file_name" class="mt-2 text-sm text-gray-600 text-center">ยังไม่ได้เลือกไฟล์</p>
                        </div>

                        <script>
                            // ✅ แสดงชื่อไฟล์เมื่อเลือก
                            document.getElementById('csv_file').addEventListener('change', function(e) {
                                const fileName = e.target.files[0] ? e.target.files[0].name : "ยังไม่ได้เลือกไฟล์";
                                document.getElementById('file_name').textContent = fileName;
                            });
                        </script>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition">
                            <i class="fa-solid fa-upload mr-2"></i> นำเข้า CSV
                        </button>
                    </form>
                </div>

            </div>
        </div>

        {{-- Modal แก้ไขสินค้า --}}
        <div x-show="show" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition">
            <div class="bg-white w-full max-w-lg rounded-xl shadow-xl p-6 relative animate__animated animate__fadeIn"
                @click.away="close()">
                <h2 class="text-xl font-bold mb-4 text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-blue-600"></i> แก้ไขสินค้า
                </h2>

                <form :action="`/admin/products/${id}`" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="block text-gray-600 text-sm">รหัสสินค้า</label>
                        <input type="text" name="product_code" x-model="product_code"
                            class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-600 text-sm">ชื่อสินค้า</label>
                        <input type="text" name="product_name" x-model="product_name"
                            class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-3">
                        <label class="block text-gray-600 text-sm">หมวดหมู่</label>
                        <input type="text" name="category" x-model="category"
                            class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="flex gap-3 mb-3">
                        <div class="w-1/3">
                            <label class="block text-gray-600 text-sm">ราคา</label>
                            <input type="number" step="0.01" name="price" x-model="price"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="w-1/3">
                            <label class="block text-gray-600 text-sm">ต้นทุน</label>
                            <input type="number" step="0.01" name="cost" x-model="cost"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="w-1/3">
                            <label class="block text-gray-600 text-sm">สต๊อก</label>
                            <input type="number" name="stock" x-model="stock"
                                class="w-full border rounded px-3 py-2">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block mb-1 text-gray-600 font-medium">รูปสินค้า</label>

                        <!-- ปุ่มเลือกไฟล์ -->
                        <label for="image_edit"
                            class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 
                        text-white font-medium rounded-lg cursor-pointer hover:from-green-600 hover:to-green-700 
                        transition shadow-md gap-2">
                            <i class="fa-solid fa-upload"></i> เลือกรูปสินค้า
                        </label>
                        <input type="file" name="image" id="image_edit" accept="image/*" class="hidden">

                        <!-- Preview รูป -->
                        <div class="mt-3 flex justify-center">
                            <img id="preview_image_edit" :src="image" alt="Preview"
                                class="w-32 h-32 object-cover rounded-lg shadow-md border" x-show="image">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-600 text-sm">Barcode</label>
                        <input type="text" name="barcode" x-model="barcode" class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="close()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                            ยกเลิก
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Alpine.js --}}
        <script src="https://unpkg.com/alpinejs" defer></script>
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#productsTable').DataTable({});
            });

            function editModal() {
                return {
                    show: false,
                    id: null,
                    product_code: '',
                    product_name: '',
                    category: '',
                    price: 0,
                    cost: 0,
                    stock: 0,
                    barcode: '',
                    image: '', // ✅ ใช้สำหรับเก็บ URL รูป

                    openEdit(id, code, name, category, price, cost, stock, barcode, image) {
                        this.show = true;
                        this.id = id;
                        this.product_code = code;
                        this.product_name = name;
                        this.category = category;
                        this.price = price;
                        this.cost = cost;
                        this.stock = stock;
                        this.barcode = barcode;
                        this.image = image || '';
                    },
                    close() {
                        this.show = false;
                        this.image = '';
                    }
                }
            }

            // ✅ Preview รูป (สำหรับ Modal แก้ไขสินค้า)
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('image_edit').addEventListener('change', function(e) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('preview_image_edit');
                        preview.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                });
            });

            // ✅ SweetAlert2 Confirm ลบสินค้า
            function deleteProduct(id) {
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: "คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก',
                    customClass: {
                        popup: 'bg-white rounded-3xl shadow-2xl p-6'
                    },
                    timer: 5000,
                    timerProgressBar: true,
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/products/${id}`;

                        // ✅ ใส่ CSRF Token & Method แบบ input hidden
                        form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                <input type="hidden" name="_method" value="DELETE">
            `;

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

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
                                url: "{{ route('products.deleteSelected') }}",
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
