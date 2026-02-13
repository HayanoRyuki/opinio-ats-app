<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GmailConnection;
use App\Services\Gmail\GmailApiService;
use App\Services\Gmail\GmailIntakeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GmailConnectionController extends Controller
{
    /**
     * Gmail連携設定画面
     */
    public function index(Request $request)
    {
        $companyId = $request->attributes->get('company_id');

        $connections = GmailConnection::where('company_id', $companyId)
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($conn) => [
                'id' => $conn->id,
                'gmail_address' => $conn->gmail_address,
                'is_active' => $conn->is_active,
                'last_sync_at' => $conn->last_sync_at?->format('Y-m-d H:i'),
                'created_at' => $conn->created_at->format('Y-m-d H:i'),
                'connected_by' => $conn->user->name ?? '不明',
            ]);

        return Inertia::render('Settings/GmailSync', [
            'connections' => $connections,
        ]);
    }

    /**
     * Google OAuth認証開始
     */
    public function connect()
    {
        $authUrl = GmailApiService::getAuthUrl();

        return redirect()->away($authUrl);
    }

    /**
     * Google OAuthコールバック
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        $error = $request->query('error');

        if ($error) {
            Log::warning('Gmail OAuth denied', ['error' => $error]);
            return redirect()->route('settings.gmail.index')
                ->with('error', 'Gmail連携が拒否されました。');
        }

        if (!$code) {
            return redirect()->route('settings.gmail.index')
                ->with('error', '認証コードが取得できませんでした。');
        }

        try {
            $tokenData = GmailApiService::exchangeCode($code);

            $companyId = $request->attributes->get('company_id');
            $userId = $request->attributes->get('auth_user_id');

            // 同じGmailアドレスの既存接続があれば更新、なければ新規作成
            $connection = GmailConnection::updateOrCreate(
                [
                    'company_id' => $companyId,
                    'gmail_address' => $tokenData['email'],
                ],
                [
                    'user_id' => $userId,
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'],
                    'token_expires_at' => now()->addSeconds($tokenData['expires_in']),
                    'is_active' => true,
                ]
            );

            Log::info('Gmail connected', [
                'connection_id' => $connection->id,
                'gmail_address' => $tokenData['email'],
            ]);

            return redirect()->route('settings.gmail.index')
                ->with('success', $tokenData['email'] . ' を接続しました。');

        } catch (\Throwable $e) {
            Log::error('Gmail OAuth callback error', ['error' => $e->getMessage()]);
            return redirect()->route('settings.gmail.index')
                ->with('error', 'Gmail連携に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * Gmail接続を解除
     */
    public function disconnect(GmailConnection $connection, Request $request)
    {
        $companyId = $request->attributes->get('company_id');

        // 権限チェック（同じ会社の接続のみ）
        if ($connection->company_id !== $companyId) {
            abort(403);
        }

        $connection->deactivate();

        Log::info('Gmail disconnected', [
            'connection_id' => $connection->id,
            'gmail_address' => $connection->gmail_address,
        ]);

        return redirect()->route('settings.gmail.index')
            ->with('success', $connection->gmail_address . ' の接続を解除しました。');
    }

    /**
     * 手動で同期を実行
     */
    public function syncNow(GmailConnection $connection, Request $request, GmailIntakeService $intakeService)
    {
        $companyId = $request->attributes->get('company_id');

        if ($connection->company_id !== $companyId) {
            abort(403);
        }

        if (!$connection->is_active) {
            return redirect()->route('settings.gmail.index')
                ->with('error', 'この接続は無効です。再度接続してください。');
        }

        try {
            $stats = $intakeService->syncConnection($connection);

            $message = sprintf(
                '同期完了: %d件処理, %d件新規取込, %d件スキップ, %dエラー',
                $stats['processed'],
                $stats['created'],
                $stats['skipped'],
                $stats['errors']
            );

            return redirect()->route('settings.gmail.index')
                ->with('success', $message);

        } catch (\Throwable $e) {
            Log::error('Gmail manual sync error', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('settings.gmail.index')
                ->with('error', '同期に失敗しました: ' . $e->getMessage());
        }
    }
}
