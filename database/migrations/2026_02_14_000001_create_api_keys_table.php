<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');                // キーの名前（例: "Chrome拡張用"）
            $table->string('key_hash');             // SHA-256ハッシュ
            $table->string('key_prefix', 8);        // 表示用プレフィックス（例: "opn_xxxx"）
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('key_hash');
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
