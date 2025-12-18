<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Company;

class RecruiterController extends Controller
{
    /**
     * マイページ（ダッシュボード）
     */
    public function mypage()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // 自分の会社の情報
        $company = Company::findOrFail($companyId);

        // 自分の会社に所属する社員のみ
        $employees = User::where('company_id', $companyId)
            ->where('role', 'employee') // roleカラムで社員判定
            ->orderBy('name')
            ->get();

        /**
         * Onboarding 判定
         * ※ フラグは持たず、常に事実ベースで計算
         */
        $onboarding = [
            // STEP1：会社情報（name が入っていれば OK）
            'step1' => !empty($company->name),

            // STEP2：職種が1件以上
            'step2' => $company->jobRoles()->count() >= 1,

            // STEP3：求人が1件以上
            'step3' => $company->jobs()->count() >= 1,
        ];

        return view('dashboard', [
            'company'    => $company,
            'employees'  => $employees,
            'onboarding' => $onboarding,
        ]);
    }
}
