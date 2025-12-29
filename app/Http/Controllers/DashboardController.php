<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 面接官は閲覧不可（×）
        if ($user->role === 'interviewer') {
            abort(403, 'アクセス権限がありません。');
        }

        return view('dashboard.index');
    }
}
