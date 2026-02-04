<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 仮応募フラグの追加と media → scout へのチャネル名変更
 *
 * 変更点：
 * 1. application_intakes に is_preliminary カラムを追加
 * 2. intake_candidate_drafts に is_preliminary カラムを追加
 * 3. channel の値を 'media' → 'scout' に変更
 * 4. candidates の source_channel も 'media' → 'scout' に変更
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. application_intakes に is_preliminary カラムを追加
        Schema::table('application_intakes', function (Blueprint $table) {
            $table->boolean('is_preliminary')->default(false)->after('status')
                ->comment('仮応募フラグ（スカウト反応など、正式応募前の段階）');
        });

        // 2. intake_candidate_drafts に is_preliminary カラムを追加
        Schema::table('intake_candidate_drafts', function (Blueprint $table) {
            $table->boolean('is_preliminary')->default(false)->after('status')
                ->comment('仮応募フラグ（スカウト反応など、正式応募前の段階）');
            $table->timestamp('promoted_at')->nullable()->after('confirmed_at')
                ->comment('仮応募から正式応募に昇格した日時');
        });

        // 3. 既存データの channel を 'media' → 'scout' に変更
        DB::table('application_intakes')
            ->where('channel', 'media')
            ->update(['channel' => 'scout']);

        // 4. candidates の source_channel も 'media' → 'scout' に変更
        DB::table('candidates')
            ->where('source_channel', 'media')
            ->update(['source_channel' => 'scout']);

        // 5. スカウトチャネルの既存データは仮応募として設定
        DB::table('application_intakes')
            ->where('channel', 'scout')
            ->update(['is_preliminary' => true]);

        DB::table('intake_candidate_drafts')
            ->whereIn('application_intake_id', function ($query) {
                $query->select('id')
                    ->from('application_intakes')
                    ->where('channel', 'scout');
            })
            ->update(['is_preliminary' => true]);
    }

    public function down(): void
    {
        // チャネル名を元に戻す
        DB::table('application_intakes')
            ->where('channel', 'scout')
            ->update(['channel' => 'media']);

        DB::table('candidates')
            ->where('source_channel', 'scout')
            ->update(['source_channel' => 'media']);

        // カラムを削除
        Schema::table('intake_candidate_drafts', function (Blueprint $table) {
            $table->dropColumn(['is_preliminary', 'promoted_at']);
        });

        Schema::table('application_intakes', function (Blueprint $table) {
            $table->dropColumn('is_preliminary');
        });
    }
};
