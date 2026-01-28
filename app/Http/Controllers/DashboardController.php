<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => [
                    'name' => $request->attributes->get('user_name', 'ゲスト'),
                    'role' => $request->attributes->get('role', 'user'),
                ]
            ]
        ]);
    }
}
