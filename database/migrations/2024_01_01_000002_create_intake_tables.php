<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 取り込み関連テーブル
 *
 * 4チャネルからの応募データを一時格納し、
 * 人間の確認後に SoT へ昇格する。
 */
return new class extends Migration
{
    public function up(): void
    {
        // ApplicationIntake - 応募取り込み生データ
        Schema::create('application_intakes', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('recruitment_jobs')->nullOnDelete();
            $table->string('channel'); // direct, media, agent, referral
            $table->string('status')->default('pending'); // pending, draft, confirmed, rejected, duplicate
            $table->jsonb('raw_data');
            $table->jsonb('parsed_data')->nullable();
            $table->string('source_id')->nullable(); // 外部システムの ID
            $table->timestamp('received_at');
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'channel']);
            $table->index('source_id');
        });

        // IntakeCandidateDraft - 候補者ドラフト
        Schema::create('intake_candidate_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_intake_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft'); // draft, confirmed, rejected, duplicate
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->jsonb('extracted_data')->nullable();
            $table->foreignId('matched_person_id')->nullable()->constrained('persons')->nullOnDelete();
            $table->foreignId('matched_candidate_id')->nullable()->constrained('candidates')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });

        // MediaSource - 求人媒体マスタ
        Schema::create('media_sources', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('integration_type')->nullable(); // api, email, csv
            $table->text('credentials')->nullable(); // encrypted
            $table->jsonb('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'is_active']);
        });

        // MediaApplicationIntake - メディア経由応募
        Schema::create('media_application_intakes', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreignId('media_source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('recruitment_jobs')->nullOnDelete();
            $table->foreignId('application_intake_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_application_id')->nullable();
            $table->string('external_job_id')->nullable();
            $table->jsonb('candidate_data');
            $table->string('status')->default('pending');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['media_source_id', 'external_application_id'], 'media_app_source_ext_app_idx');
            $table->index(['company_id', 'status']);
        });

        // Recommendation - エージェント推薦
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('recruitment_jobs')->nullOnDelete();
            $table->foreignId('application_intake_id')->nullable()->constrained()->nullOnDelete();
            $table->string('agent_company_name');
            $table->string('agent_name');
            $table->string('agent_email');
            $table->jsonb('candidate_data');
            $table->text('recommendation_comment')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('received_at');
            $table->timestamps();

            $table->index(['company_id', 'status']);
        });

        // ReferralIntake - リファラル
        Schema::create('referral_intakes', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('recruitment_jobs')->nullOnDelete();
            $table->foreignId('application_intake_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->string('referred_name');
            $table->string('referred_email')->nullable();
            $table->string('referred_phone')->nullable();
            $table->string('relationship')->nullable();
            $table->text('recommendation_reason')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index('referrer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_intakes');
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('media_application_intakes');
        Schema::dropIfExists('media_sources');
        Schema::dropIfExists('intake_candidate_drafts');
        Schema::dropIfExists('application_intakes');
    }
};
