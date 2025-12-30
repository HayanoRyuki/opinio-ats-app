<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        // SSO 実装前のため、認証・role判定は一旦無効化
        return view('applications.index');
    }

    public function show($id)
    {
        // SSO 実装前のため、認証・role判定は一旦無効化
        return view('applications.show', ['id' => $id]);
    }
}
