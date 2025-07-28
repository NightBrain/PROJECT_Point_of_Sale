<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::all()->sortByDesc('created_at');
        return view('activity.index', compact('logs'));
    }
    public function deleteSelected(Request $request)
    {
        $ids = $request->ids ?? [];
        if (!empty($ids)) {
            ActivityLog::whereIn('id', $ids)->delete();
            return back()->with('success', 'ลบกิจกรรมที่เลือกสำเร็จ');
        }
        return back()->with('error', 'กรุณาเลือกอย่างน้อย 1 รายการ');
    }
}
