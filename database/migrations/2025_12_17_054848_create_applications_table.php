<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // 関連
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();

            // 応募情報
            $table->string('source')->comment('企業ページ / エージェント / 媒体 / その他');
            $table->string('agent_name')->nullable();

            // 選考ステータス
            $table->string('status')->comment('applied / screening / interview_1 / interview_2 / final / offer / rejected');

            // 理由（内定・お見送り）
            $table->string('decision_category')->nullable();
            $table->string('decision_reason')->nullable();

            // メモ
            $table->text('memo')->nullable();

            $table->timestamps();

            // 同一求人への二重応募防止
            $table->unique(['job_id', 'candidate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
