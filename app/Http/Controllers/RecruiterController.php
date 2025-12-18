<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;

class RecruiterController extends Controller
{
    public function mypage()
    {
        $companyId = auth()->user()->company_id;

        // 自分の会社の情報
        $company = Company::find($companyId);

        // 自分の会社に所属する社員のみ
        $employees = User::where('company_id', $companyId)
                         ->where('role', 'employee') // roleカラムで社員判定
                         ->orderBy('name')
                         ->get();

        return view('recruiter.mypage', compact('company', 'employees'));
    }
}
