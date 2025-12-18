<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = Auth::user()->company;

        return view('recruiter.company.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $company = Auth::user()->company;

        $request->validate([
            'name'     => 'required|string|max:255',
            'domain'   => 'required|string|max:255',
            'address'  => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'website'  => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:255',
        ]);

        $company->update($request->only([
            'name', 'domain', 'address', 'phone', 'website', 'industry'
        ]));

        return redirect()->route('recruiter.mypage')
                         ->with('status', '会社情報を更新しました');
    }
}
