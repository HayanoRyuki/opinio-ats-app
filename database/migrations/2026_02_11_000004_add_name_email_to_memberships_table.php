<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->string('name')->nullable()->after('role');
            $table->string('email')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn(['name', 'email']);
        });
    }
};
