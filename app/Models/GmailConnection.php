<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * GmailConnection - Gmail OAuth接続管理
 *
 * ユーザーのGmailアカウントとのOAuth接続を管理し、
 * ビズリーチ等の通知メール取得に使用する。
 */
class GmailConnection extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'gmail_address',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'last_sync_at',
        'last_synced_history_id',
        'is_active',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * トークンの有効期限が切れているか
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return true;
        }

        // 5分の余裕を持って判定
        return $this->token_expires_at->subMinutes(5)->isPast();
    }

    /**
     * 同期完了をマーク
     */
    public function markSynced(?string $historyId = null): void
    {
        $data = ['last_sync_at' => now()];

        if ($historyId) {
            $data['last_synced_history_id'] = $historyId;
        }

        $this->update($data);
    }

    /**
     * トークン情報を更新
     */
    public function updateTokens(string $accessToken, ?string $refreshToken = null, ?int $expiresIn = null): void
    {
        $data = [
            'access_token' => $accessToken,
        ];

        if ($refreshToken) {
            $data['refresh_token'] = $refreshToken;
        }

        if ($expiresIn) {
            $data['token_expires_at'] = now()->addSeconds($expiresIn);
        }

        $this->update($data);
    }

    /**
     * 接続を無効化
     */
    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
            'access_token' => '',
            'refresh_token' => '',
        ]);
    }

    // ========================================
    // Scopes
    // ========================================

    /**
     * アクティブな接続のみ
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * 同期が必要な接続（指定分数以上経過）
     */
    public function scopeDueForSync(Builder $query, int $minutes = 15): Builder
    {
        return $query->active()
            ->where(function ($q) use ($minutes) {
                $q->whereNull('last_sync_at')
                  ->orWhere('last_sync_at', '<=', now()->subMinutes($minutes));
            });
    }

    // ========================================
    // Relations
    // ========================================

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
