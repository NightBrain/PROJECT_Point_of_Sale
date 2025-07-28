@extends('components.layouts.app')
@section('title', 'Manage Staff')

@section('content')
    <div class="p-6">

        {{-- ✅ SweetAlert2 Success --}}
        @if (session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    Swal.fire({
                        icon: "success",
                        title: "สำเร็จ!",
                        text: "{{ session('success') }}",
                        timer: 2000,
                        customClass: {
                            popup: 'bg-white rounded-3xl shadow-2xl p-6'
                        },
                        timer: 5000,
                        timerProgressBar: true,
                    });
                });
            </script>
        @endif
        {{-- ✅ สถิติพนักงาน --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- ✅ Admin Count --}}
            <div class="bg-gradient-to-r from-blue-100 to-blue-200 shadow-md rounded-xl p-5 flex items-center gap-4">
                <div class="bg-blue-600 text-white p-4 rounded-full text-3xl shadow-md w-20 h-20 text-center grid content-center">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-3xl font-extrabold">จำนวน Admin</p>
                    <h2 class="text-3xl font-extrabold text-gray-500">{{ $adminCount }}</h2>
                </div>
            </div>

            {{-- ✅ Staff Count --}}
            <div class="bg-gradient-to-r from-green-100 to-green-200 shadow-md rounded-xl p-5 flex items-center gap-4">
                <div class="bg-green-600 text-white p-4 rounded-full text-3xl shadow-md w-20 h-20 text-center grid content-center">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <p class="text-gray-600 text-3xl font-extrabold">จำนวน Staff</p>
                    <h2 class="text-3xl font-extrabold text-gray-500">{{ $staffCount }}</h2>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- ✅ เพิ่มพนักงานใหม่ --}}
            <div class="bg-white shadow-md rounded-xl p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-green-600"></i> เพิ่มพนักงานใหม่
                </h2>
                <form action="{{ route('employees.store') }}" method="POST" class="grid grid-cols-1 gap-4">
                    @csrf
                    <div class="flex justify-between gap-4">
                        <input type="text" name="name" placeholder="ชื่อ"
                            class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none w-full"
                            required>

                        <input type="email" name="email" placeholder="อีเมล"
                            class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none w-full"
                            required>
                    </div>

                    <div class="flex justify-between gap-4">
                        {{-- ✅ รหัสผ่าน + Strength Meter --}}
                        <div class="w-full">
                            <input type="password" id="password" name="password" placeholder="รหัสผ่าน"
                                class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                required>
                            <div id="password-strength" class="mt-2 text-sm font-medium"></div>
                        </div>

                        {{-- ✅ ยืนยันรหัสผ่าน --}}
                        <div class="w-full">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                placeholder="ยืนยันรหัสผ่าน"
                                class="border rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-400 focus:outline-none"
                                required>
                            <div id="password-match" class="mt-2 text-sm font-bold"></div>
                        </div>
                    </div>

                    <select name="role"
                        class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
                        <option value="">-- เลือกตำแหน่ง --</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                    </select>

                    <button id="submit-btn" type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                        ✅ เพิ่มพนักงาน
                    </button>
                </form>

            </div>

            {{-- ✅ ตารางพนักงาน (DataTables) --}}
            <div class="bg-white rounded-xl shadow-md p-6 overflow-x-auto">
                <h2 class="text-xl font-bold mb-4 text-gray-700 flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-gray-600"></i> รายชื่อพนักงาน
                </h2>
                <table id="employeesTable"
                    class="min-w-full text-left border-collapse border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                        <tr>
                            <th class="border px-4 py-2">ชื่อ</th>
                            <th class="border px-4 py-2">อีเมล</th>
                            <th class="border px-4 py-2">ตำแหน่ง</th>
                            <th class="border px-4 py-2 text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach ($employees as $emp)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="border px-4 py-2">{{ $emp->name }}</td>
                                <td class="border px-4 py-2">{{ $emp->email }}</td>
                                <td class="border px-4 py-2">
                                    <form method="POST" action="{{ route('employees.updateRole', $emp->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role"
                                            class="border rounded px-2 py-1 text-sm focus:ring focus:ring-blue-300"
                                            onchange="this.form.submit()">
                                            @foreach (['admin', 'staff'] as $role)
                                                <option value="{{ $role }}" @selected($emp->role === $role)>
                                                    {{ ucfirst($role) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <form method="POST" action="{{ route('employees.destroy', $emp->id) }}"
                                        class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm delete-btn">
                                            ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ✅ SweetAlert2 & DataTables --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>

    <script>
        // ✅ DataTables
        $(document).ready(function() {
            $('#employeesTable').DataTable({
                responsive: true,
            });
        });

        // ✅ SweetAlert2 Delete Confirm
        document.querySelectorAll(".delete-btn").forEach(btn => {
            btn.addEventListener("click", function(e) {
                const form = this.closest(".delete-form");
                Swal.fire({
                    title: "คุณแน่ใจหรือไม่?",
                    text: "คุณจะไม่สามารถกู้คืนข้อมูลนี้ได้!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "ใช่, ลบเลย!",
                    cancelButtonText: "ยกเลิก",
                    customClass: {
                        popup: 'bg-white rounded-3xl shadow-2xl p-6'
                    },
                    timer: 5000,
                    timerProgressBar: true,
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        const passwordInput = document.getElementById("password");
        const confirmInput = document.getElementById("password_confirmation");
        const matchText = document.getElementById("password-match");
        const strengthText = document.getElementById("password-strength");
        const submitBtn = document.getElementById("submit-btn");

        function checkStrength(value) {
            let strength = 0;
            if (value.length >= 6) strength++;
            if (/[A-Z]/.test(value)) strength++;
            if (/[0-9]/.test(value)) strength++;
            if (/[^A-Za-z0-9]/.test(value)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    strengthText.textContent = "รหัสผ่านอ่อน (เพิ่มตัวอักษรพิเศษและตัวเลข)";
                    strengthText.className = "mt-2 text-sm font-bold text-red-600";
                    break;
                case 2:
                    strengthText.textContent = "รหัสผ่านปานกลาง";
                    strengthText.className = "mt-2 text-sm font-bold text-yellow-600";
                    break;
                case 3:
                case 4:
                    strengthText.textContent = "รหัสผ่านแข็งแรง ✅";
                    strengthText.className = "mt-2 text-sm font-bold text-green-600";
                    break;
            }
        }

        function checkPasswordMatch() {
            if (confirmInput.value === "") {
                matchText.textContent = "";
                submitBtn.disabled = false;
                return;
            }

            if (passwordInput.value !== confirmInput.value) {
                matchText.textContent = "❌ รหัสผ่านไม่ตรงกัน";
                matchText.className = "mt-2 text-sm font-bold text-red-600";
                submitBtn.disabled = true;
            } else {
                matchText.textContent = "✅ รหัสผ่านตรงกัน";
                matchText.className = "mt-2 text-sm font-bold text-green-600";
                submitBtn.disabled = false;
            }
        }

        passwordInput.addEventListener("input", () => {
            checkStrength(passwordInput.value);
            checkPasswordMatch();
        });

        confirmInput.addEventListener("input", checkPasswordMatch);
    </script>
@endsection
