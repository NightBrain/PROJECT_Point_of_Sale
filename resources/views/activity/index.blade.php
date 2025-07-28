@extends('components.layouts.app')
@section('title', 'Activity Log')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css" />
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-clipboard-list text-blue-600"></i> Activity Log
        </h1>

        <div class="bg-white shadow-md rounded-xl p-6">
            <table id="activity-table" class="min-w-full text-left border-collapse border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                    <tr>
                        <th class="border px-4 py-2">ผู้ใช้</th>
                        <th class="border px-4 py-2">กิจกรรม</th>
                        <th class="border px-4 py-2">รายละเอียด</th>
                        <th class="border px-4 py-2">เวลา</th>
                        <th class="border px-4 py-2 text-center">
                            <input type="checkbox" id="select-all" class="form-checkbox h-4 w-4 text-blue-600">
                        </th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border px-4 py-2">{{ $log->user->name ?? 'System' }}</td>
                            <td class="border px-4 py-2">{{ $log->action }}</td>
                            <td class="border px-4 py-2 text-sm">
                                @php
                                    $details = json_decode($log->details, true);
                                @endphp
                                @if (is_array($details))
                                    @foreach ($details as $key => $value)
                                        <div><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</div>
                                    @endforeach
                                @else
                                    <div>-</div>
                                @endif
                            </td>
                            <td class="border px-4 py-2 text-sm text-gray-500">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                            <td class="border px-4 py-2 text-center">
                                <input type="checkbox" class="log-checkbox h-4 w-4 text-blue-600"
                                    value="{{ $log->id }}">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">ไม่มีข้อมูลกิจกรรม</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4 flex justify-end items-center">
                <button id="delete-selected"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow disabled:opacity-50" disabled>
                    <i class="fa-solid fa-trash"></i> ลบที่เลือก
                </button>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    {{-- ✅ SweetAlert2 Script --}}
    <script>
        $(document).ready(function() {
            $('#activity-table').DataTable();

            // toggle child row event
            $('#activity-table tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = $('#activity-table').DataTable().row(tr);

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
        document.addEventListener("DOMContentLoaded", function() {
            const selectAll = document.getElementById("select-all");
            const checkboxes = document.querySelectorAll(".log-checkbox");
            const deleteBtn = document.getElementById("delete-selected");

            function toggleDeleteButton() {
                deleteBtn.disabled = ![...checkboxes].some(chk => chk.checked);
            }

            selectAll.addEventListener("change", function() {
                checkboxes.forEach(chk => chk.checked = this.checked);
                toggleDeleteButton();
            });

            checkboxes.forEach(chk => chk.addEventListener("change", toggleDeleteButton));

            deleteBtn.addEventListener("click", function() {
                const selected = [...checkboxes].filter(chk => chk.checked).map(chk => chk.value);

                if (selected.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'แจ้งเตือน',
                        text: 'กรุณาเลือกอย่างน้อย 1 รายการ',
                        customClass: {
                            popup: 'bg-white rounded-3xl shadow-2xl p-6'
                        }
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
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement("form");
                        form.method = "POST";
                        form.action = "{{ route('activityLogs.deleteSelected') }}";

                        const csrf = document.createElement("input");
                        csrf.type = "hidden";
                        csrf.name = "_token";
                        csrf.value = "{{ csrf_token() }}";
                        form.appendChild(csrf);

                        selected.forEach(id => {
                            const input = document.createElement("input");
                            input.type = "hidden";
                            input.name = "ids[]";
                            input.value = id;
                            form.appendChild(input);
                        });

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
