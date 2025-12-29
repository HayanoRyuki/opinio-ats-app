<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('company_id');
            $table->uuid('job_id');
            $table->uuid('candidate_id');
            $table->string('status'); // screening / interview_1 / ...
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->unique(['job_id', 'candidate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
