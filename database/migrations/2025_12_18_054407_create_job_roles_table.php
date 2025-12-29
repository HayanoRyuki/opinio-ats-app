<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_roles', function (Blueprint $table) {
            $table->id();

            $table->uuid('company_id');

            // 職種名
            $table->string('internal_name'); // 社内・AI用
            $table->string('display_name');  // 表示用
            $table->text('description')->nullable();

            // 並び順・状態
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_roles');
    }
};
