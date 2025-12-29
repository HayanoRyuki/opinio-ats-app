<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('selection_steps', function (Blueprint $table) {
            $table->id();

            // 企業ごとの選考フロー
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // ステップ定義
            $table->string('key')->comment('screening / interview_1 / interview_2 / final / offer');
            $table->string('label')->comment('表示名：書類選考、1次面接など');
            $table->unsignedInteger('order')->comment('表示順');

            // 設定
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // 企業内での順序・キー重複防止
            $table->unique(['company_id', 'key']);
            $table->unique(['company_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selection_steps');
    }
};
