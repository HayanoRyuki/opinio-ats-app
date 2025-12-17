<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();

            // 基本情報
            $table->string('last_name');
            $table->string('first_name');
            $table->string('last_name_kana')->nullable();
            $table->string('first_name_kana')->nullable();

            // 連絡先
            $table->string('email')->index();
            $table->string('phone')->nullable();

            // 書類
            $table->string('resume_path')->nullable();
            $table->string('cv_path')->nullable();

            // メモ
            $table->text('memo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
