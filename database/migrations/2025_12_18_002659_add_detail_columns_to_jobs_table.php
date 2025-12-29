<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('location')->nullable()->after('title');
            $table->string('employment_type')->nullable();
            $table->string('salary')->nullable();
            $table->string('working_hours')->nullable();

            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'location',
                'employment_type',
                'salary',
                'working_hours',
                'requirements',
                'benefits',
                'notes',
            ]);
        });
    }
};
