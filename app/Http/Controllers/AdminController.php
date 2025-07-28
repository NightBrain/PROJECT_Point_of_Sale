<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function login()
    {
        if (Auth::id()) {
            $usertype = Auth::user()->role;
            if ($usertype === 'admin') {
                return redirect()->route('admin');
            } elseif ($usertype === 'staff') {
                // ✅ ทำงานเฉพาะ staff
                return redirect()->route('pos');
            } else {
                abort(403, 'Unauthorized');
            }
        }

    }
}
