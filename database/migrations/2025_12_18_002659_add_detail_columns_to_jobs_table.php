<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Skipped: These columns are already added in 2025_12_17_065000_add_job_details_columns
        // to recruitment_jobs table
    }

    public function down(): void
    {
        // No-op
    }
};
