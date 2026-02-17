<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('linked_at');
            $table->timestamps();

            $table->unique(['recommendation_id', 'candidate_id']);
            $table->index('candidate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_links');
    }
};
