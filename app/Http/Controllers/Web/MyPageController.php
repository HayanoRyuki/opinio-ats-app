<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Membership;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MyPageController extends Controller
{
    /**
     * マイページ表示
     */
    public function index(Request $request): Response
    {
        $authUserId = $request->attributes->get('auth_user_id');
        $companyId = $request->attributes->get('company_id');
        $role = $request->attributes->get('role');

        // 会社情報を取得
        $company = null;
        if ($companyId) {
            $company = Company::find($companyId);
        }

        // Membership情報を取得
        $membership = Membership::where('user_id', $authUserId)->first();

        // Auth側のプロフィール編集URLを構築
        $authProfileUrl = config('services.auth_app.url', 'https://auth.opinio.co.jp') . '/mypage';

        return Inertia::render('MyPage/Index', [
            'user' => [
                'id' => $authUserId,
                'role' => $role,
            ],
            'company' => $company ? [
                'id' => $company->id,
                'name' => $company->name,
            ] : null,
            'membership' => $membership,
            'authProfileUrl' => $authProfileUrl,
        ]);
    }
}
