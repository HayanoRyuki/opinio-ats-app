<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();

            // auth 側の user_id（JWT sub）
            $table->string('user_id');

            // ATS 側の company
            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            // admin / recruiter / interviewer
            $table->string('role');

            $table->timestamps();

            // 同一会社に同一ユーザーは1レコードのみ
            $table->unique(['user_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
