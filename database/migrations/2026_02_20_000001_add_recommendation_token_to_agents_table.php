<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->string('recommendation_token', 64)->nullable()->unique()->after('is_active');
            $table->timestamp('token_expires_at')->nullable()->after('recommendation_token');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn(['recommendation_token', 'token_expires_at']);
        });
    }
};
