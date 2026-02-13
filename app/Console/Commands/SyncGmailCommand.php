<?php

namespace App\Console\Commands;

use App\Models\GmailConnection;
use App\Services\Gmail\GmailApiService;
use App\Services\Gmail\GmailIntakeService;
use App\Services\Gmail\BizReachEmailParserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Gmail同期コマンド
 *
 * アクティブな全GmailConnectionに対して同期を実行し、
 * ビズリーチの通知メールを取得・パースしてIntakeに登録する。
 *
 * Usage:
 *   php artisan gmail:sync              # 全アクティブ接続を同期
 *   php artisan gmail:sync --connection=1  # 特定の接続のみ同期
 *   php artisan gmail:sync --force       # 同期間隔を無視して強制実行
 */
class SyncGmailCommand extends Command
{
    protected $signature = 'gmail:sync
                            {--connection= : 特定のGmailConnection IDのみ同期}
                            {--force : 同期間隔を無視して強制実行}';

    protected $description = 'Gmail APIからビズリーチ通知メールを同期し、Intakeに登録する';

    public function handle(): int
    {
        $this->info('Gmail同期を開始します...');

        $gmailApi = new GmailApiService();
        $parser = new BizReachEmailParserService();
        $intakeService = new GmailIntakeService($gmailApi, $parser);

        $connectionId = $this->option('connection');
        $force = $this->option('force');
        $intervalMinutes = config('services.gmail_sync.interval_minutes', 15);

        // 対象の接続を取得
        $query = GmailConnection::active();

        if ($connectionId) {
            $query->where('id', $connectionId);
        } elseif (!$force) {
            $query->dueForSync($intervalMinutes);
        }

        $connections = $query->get();

        if ($connections->isEmpty()) {
            $this->info('同期対象のGmail接続がありません。');
            return Command::SUCCESS;
        }

        $this->info(sprintf('%d件のGmail接続を同期します。', $connections->count()));

        $totalStats = [
            'connections_processed' => 0,
            'connections_failed' => 0,
            'total_created' => 0,
            'total_skipped' => 0,
            'total_errors' => 0,
        ];

        foreach ($connections as $connection) {
            $this->line('');
            $this->info(sprintf(
                '  [%d] %s を同期中...',
                $connection->id,
                $connection->gmail_address
            ));

            try {
                $stats = $intakeService->syncConnection($connection);

                $totalStats['connections_processed']++;
                $totalStats['total_created'] += $stats['created'];
                $totalStats['total_skipped'] += $stats['skipped'];
                $totalStats['total_errors'] += $stats['errors'];

                $this->info(sprintf(
                    '    → %d件処理, %d件新規, %d件スキップ, %dエラー',
                    $stats['processed'],
                    $stats['created'],
                    $stats['skipped'],
                    $stats['errors']
                ));

            } catch (\Throwable $e) {
                $totalStats['connections_failed']++;
                $this->error(sprintf('    → エラー: %s', $e->getMessage()));

                Log::error('Gmail sync command: connection failed', [
                    'connection_id' => $connection->id,
                    'error' => $e->getMessage(),
                ]);

                // 個別接続のエラーで全体を停止しない
                continue;
            }
        }

        $this->line('');
        $this->info('=== 同期完了 ===');
        $this->info(sprintf('  接続数: %d成功 / %d失敗', $totalStats['connections_processed'], $totalStats['connections_failed']));
        $this->info(sprintf('  新規取込: %d件', $totalStats['total_created']));
        $this->info(sprintf('  スキップ: %d件', $totalStats['total_skipped']));

        if ($totalStats['total_errors'] > 0) {
            $this->warn(sprintf('  エラー: %d件', $totalStats['total_errors']));
        }

        return $totalStats['connections_failed'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
