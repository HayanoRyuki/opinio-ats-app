<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'key_hash',
        'key_prefix',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ===========================
    // Relations
    // ===========================

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // ===========================
    // Static helpers
    // ===========================

    /**
     * 新しいAPIキーを生成。
     * 戻り値に平文キーを含む（1度しか表示できない）。
     */
    public static function generate(int $companyId, string $name): array
    {
        $plainKey = 'opn_' . Str::random(40);
        $prefix = substr($plainKey, 0, 8);

        $apiKey = static::create([
            'company_id' => $companyId,
            'name' => $name,
            'key_hash' => hash('sha256', $plainKey),
            'key_prefix' => $prefix,
        ]);

        return [
            'api_key' => $apiKey,
            'plain_key' => $plainKey, // ⚠️ 一度だけ表示
        ];
    }

    /**
     * 平文キーからApiKeyレコードを検索。
     */
    public static function findByPlainKey(string $plainKey): ?self
    {
        $hash = hash('sha256', $plainKey);

        return static::where('key_hash', $hash)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->first();
    }
}
