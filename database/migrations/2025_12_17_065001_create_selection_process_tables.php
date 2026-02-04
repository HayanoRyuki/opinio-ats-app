<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 選考プロセス管理テーブル
 *
 * - SelectionStep: 求人ごとの選考ステップマスタ
 * - ApplicationStep: 応募ごとの選考進捗
 */
return new class extends Migration
{
    public function up(): void
    {
        // 選考ステップマスタ（求人ごと）
        Schema::create('selection_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('recruitment_jobs')->cascadeOnDelete();
            $table->string('name'); // 書類選考、一次面接、二次面接、最終面接、内定
            $table->integer('order')->default(0);
            $table->text('description')->nullable();
            $table->integer('duration_days')->nullable(); // 想定所要日数
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['job_id', 'order']);
        });

        // 応募の選考進捗
        Schema::create('application_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('selection_step_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, in_progress, passed, failed, skipped
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('scheduled_at')->nullable(); // 面接日時など
            $table->text('notes')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->jsonb('evaluation')->nullable(); // 評価データ
            $table->timestamps();

            $table->unique(['application_id', 'selection_step_id']);
            $table->index(['application_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_steps');
        Schema::dropIfExists('selection_steps');
    }
};
