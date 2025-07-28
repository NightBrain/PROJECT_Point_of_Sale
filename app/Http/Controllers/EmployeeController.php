<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ActivityLogger;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::whereIn('role', ['admin', 'staff'])->get();

        $adminCount = User::where('role', 'admin')->count();
        $staffCount = User::where('role', 'staff')->count();

        // เพิ่ม Log
        ActivityLogger::log('เข้าดูรายชื่อพนักงาน', [
            'total_employees' => $employees->count(),
            'admin_count' => $adminCount,
            'staff_count' => $staffCount,
        ]);

        return view('employees.index', compact('employees', 'adminCount', 'staffCount'));
    }

    public function store(Request $request)
    {
        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        ActivityLogger::log('เพิ่มพนักงานใหม่', [
            'name' => $employee->name,
            'role' => $employee->role
        ]);

        return back()->with('success', 'เพิ่มพนักงานสำเร็จ');
    }

    public function updateRole(Request $request, $id)
    {
        $employee = User::findOrFail($id);
        $oldRole = $employee->role;
        $employee->update(['role' => $request->role]);

        ActivityLogger::log('เปลี่ยน Role พนักงาน', [
            'employee' => $employee->name,
            'old_role' => $oldRole,
            'new_role' => $request->role
        ]);

        return back()->with('success', 'อัปเดต Role สำเร็จ');
    }

    public function destroy(User $employee)
    {
        // เพิ่ม Log ก่อนลบ
        ActivityLogger::log('ลบพนักงาน', [
            'name' => $employee->name,
            'role' => $employee->role
        ]);

        $employee->delete();

        return back()->with('success', 'ลบพนักงานเรียบร้อย');
    }
}
