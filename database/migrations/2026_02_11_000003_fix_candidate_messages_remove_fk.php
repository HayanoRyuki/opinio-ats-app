<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_messages', function (Blueprint $table) {
            // FK制約を削除（本番ではusersテーブルにレコードがないため）
            $table->dropForeign(['user_id']);

            // 送信者名を直接保存（JWTユーザーの名前を保持）
            $table->string('sender_name')->after('user_id')->default('');
        });
    }

    public function down(): void
    {
        Schema::table('candidate_messages', function (Blueprint $table) {
            $table->dropColumn('sender_name');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
