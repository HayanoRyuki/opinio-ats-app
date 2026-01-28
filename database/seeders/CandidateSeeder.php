<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;
use App\Models\Company;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company) {
            $this->command->warn('Company not found. Skip CandidateSeeder.');
            return;
        }

        $candidates = [
            [
                'name' => '田中 太郎',
                'email' => 'taro.tanaka@example.com',
                'phone' => '090-1234-5678',
                'memo' => 'サーバーサイド経験5年。Laravel/PHP得意。',
            ],
            [
                'name' => '鈴木 花子',
                'email' => 'hanako.suzuki@example.com',
                'phone' => '090-2345-6789',
                'memo' => 'フロントエンド専門。Vue.js/React経験あり。',
            ],
            [
                'name' => '佐藤 健一',
                'email' => 'kenichi.sato@example.com',
                'phone' => '090-3456-7890',
                'memo' => 'PdM経験7年。HR Tech領域に強い関心。',
            ],
            [
                'name' => '山田 美咲',
                'email' => 'misaki.yamada@example.com',
                'phone' => '090-4567-8901',
                'memo' => 'フルスタックエンジニア。スタートアップ経験豊富。',
            ],
            [
                'name' => '高橋 翔太',
                'email' => 'shota.takahashi@example.com',
                'phone' => '090-5678-9012',
                'memo' => 'インフラ/DevOps経験あり。AWS認定資格保有。',
            ],
            [
                'name' => '伊藤 さくら',
                'email' => 'sakura.ito@example.com',
                'phone' => '090-6789-0123',
                'memo' => 'UI/UXデザイナー出身のPdM。ユーザー視点に強み。',
            ],
            [
                'name' => '渡辺 大輔',
                'email' => 'daisuke.watanabe@example.com',
                'phone' => '090-7890-1234',
                'memo' => 'バックエンド＋データベース設計に強み。Go/Python経験。',
            ],
            [
                'name' => '中村 優子',
                'email' => 'yuko.nakamura@example.com',
                'phone' => '090-8901-2345',
                'memo' => 'プロジェクトマネージャー経験あり。チームビルディングが得意。',
            ],
        ];

        foreach ($candidates as $data) {
            Candidate::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'email' => $data['email'],
                ],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'memo' => $data['memo'],
                ]
            );
        }

        $this->command->info('CandidateSeeder completed: ' . count($candidates) . ' candidates.');
    }
}
