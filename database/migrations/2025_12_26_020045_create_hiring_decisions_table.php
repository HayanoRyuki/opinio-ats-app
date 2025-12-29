<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hiring_decisions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('decided_by');

            $table->string('decision', 20);
            $table->text('reason')->nullable();
            $table->timestamp('decided_at');

            $table->timestamps();

            // 1 application = 1 decision（updateOrCreate 前提）
            $table->unique('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hiring_decisions');
    }
};
