<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedBigInteger('selection_step_id')
                  ->nullable()
                  ->after('candidate_id');

            $table->foreign('selection_step_id')
                  ->references('id')
                  ->on('selection_steps')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['selection_step_id']);
            $table->dropColumn('selection_step_id');
        });
    }
};
