<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gmail_connections', function (Blueprint $table) {
            $table->id();
            $table->uuid('company_id');
            $table->unsignedBigInteger('user_id');
            $table->string('gmail_address');
            $table->text('access_token');   // encrypted via model cast
            $table->text('refresh_token');  // encrypted via model cast
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->string('last_synced_history_id')->nullable(); // Gmail History API 差分同期用
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['company_id', 'is_active']);
        });

        // application_intakes に email_message_id カラム追加（Gmail重複チェック用）
        Schema::table('application_intakes', function (Blueprint $table) {
            $table->string('email_message_id')->nullable()->after('source_id');
            $table->index('email_message_id');
        });
    }

    public function down(): void
    {
        Schema::table('application_intakes', function (Blueprint $table) {
            $table->dropIndex(['email_message_id']);
            $table->dropColumn('email_message_id');
        });

        Schema::dropIfExists('gmail_connections');
    }
};
