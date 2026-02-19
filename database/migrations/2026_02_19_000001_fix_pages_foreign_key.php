<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * pages テーブルの foreign key を修正
 * job_id は recruitment_jobs テーブルを参照するべき
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // 既存の不正な FK を削除（存在する場合）
            try {
                $table->dropForeign(['job_id']);
            } catch (\Exception $e) {
                // FK が存在しない場合は無視
            }

            // 正しい FK を追加
            $table->foreign('job_id')
                ->references('id')
                ->on('recruitment_jobs')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
        });
    }
};
