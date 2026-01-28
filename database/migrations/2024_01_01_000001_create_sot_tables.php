<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SoT（Single Source of Truth）テーブル
 *
 * - Person: 実在する人物
 * - Candidate: 企業ごとの候補者
 * - Job: 求人票（jobs テーブルはキュー用で別途存在するため recruitment_jobs を使用）
 * - Application: 応募レコード
 */
return new class extends Migration
{
    public function up(): void
    {
        // Person - 人物マスタ（SoT）
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('name');
            $table->string('name_kana')->nullable();
            $table->jsonb('profile')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('email');
            $table->index('phone');
        });

        // Recruitment Jobs - 求人票（SoT）
        Schema::create('recruitment_jobs', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('status')->default('draft'); // draft, open, paused, closed
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->jsonb('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });

        // Candidate - 企業別候補者（SoT）
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('persons')->cascadeOnDelete();
            $table->string('source_channel')->nullable(); // direct, media, agent, referral
            $table->string('source_detail')->nullable();
            $table->jsonb('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'person_id']);
            $table->index(['company_id', 'source_channel']);
        });

        // Application - 応募（SoT）
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('recruitment_jobs')->cascadeOnDelete();
            $table->string('status')->default('active'); // active, offered, hired, rejected, withdrawn
            $table->string('current_step')->nullable();
            $table->timestamp('applied_at');
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['candidate_id', 'job_id']);
            $table->index(['job_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('recruitment_jobs');
        Schema::dropIfExists('persons');
    }
};
