<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recruitment_jobs', function (Blueprint $table) {
            $table->text('benefits')->nullable()->after('requirements');
            $table->string('employment_type')->default('full_time')->after('benefits');
            $table->string('location')->nullable()->after('employment_type');
            $table->integer('salary_min')->nullable()->after('location');
            $table->integer('salary_max')->nullable()->after('salary_min');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_jobs', function (Blueprint $table) {
            $table->dropColumn(['benefits', 'employment_type', 'location', 'salary_min', 'salary_max']);
        });
    }
};
