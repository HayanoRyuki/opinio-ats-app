<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            // 紐づき
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 面接官

            // ステップ
            $table->string('step_key')->comment('screening / interview_1 / interview_2 / final');

            // 評価
            $table->unsignedTinyInteger('overall_score')->comment('1-5');
            $table->unsignedTinyInteger('skill_score')->nullable();
            $table->unsignedTinyInteger('communication_score')->nullable();
            $table->unsignedTinyInteger('culture_fit_score')->nullable();
            $table->unsignedTinyInteger('motivation_score')->nullable();

            // 推薦判断
            $table->string('recommendation')->comment('proceed / hold / reject');

            // コメント
            $table->text('comment')->nullable();

            $table->timestamps();

            // 同一ステップ・同一面接官は1評価
            $table->unique(['application_id', 'user_id', 'step_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
