<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_chat_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->string('source');          // bizreach, wantedly, other
            $table->string('source_label')->nullable(); // その他の場合の名前
            $table->longText('raw_text');       // 貼り付け原文
            $table->text('summary')->nullable(); // AI要約
            $table->string('imported_by');      // user_id
            $table->string('sender_name');      // 取り込み者名
            $table->timestamps();

            $table->index(['candidate_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_chat_imports');
    }
};
