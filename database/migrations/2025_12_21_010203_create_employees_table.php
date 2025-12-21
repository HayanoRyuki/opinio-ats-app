<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // ATS candidate との紐づき（1対1）
            $table->unsignedBigInteger('candidate_id')->unique();
            $table->string('company_id');

            // 入社情報（事実）
            $table->date('joined_at')->nullable();

            // 在籍状態（処遇判断ではない）
            $table->string('status')->default('active'); // active / left

            $table->timestamps();

            // 外部キー（SQLiteでもOK）
            $table->foreign('candidate_id')
                  ->references('id')
                  ->on('candidates')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
