<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Company;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        // 開発バイパスと同じ dev-company を使用
        $company = Company::firstOrCreate(
            ['slug' => 'dev-company'],
            ['name' => 'Development Company']
        );

        $agents = [
            [
                'organization' => 'リクルートエージェント',
                'name' => '田中 健太',
                'email' => 'tanaka@recruit-agent.example.com',
                'phone' => '03-1234-5678',
                'agent_type' => 'human',
                'notes' => 'IT・Web系に強い。レスポンスが早い。',
            ],
            [
                'organization' => 'JACリクルートメント',
                'name' => '佐藤 美穂',
                'email' => 'sato@jac-recruitment.example.com',
                'phone' => '03-2345-6789',
                'agent_type' => 'human',
                'notes' => 'マネージャー・幹部クラスが得意。',
            ],
            [
                'organization' => 'doda',
                'name' => '鈴木 大輔',
                'email' => 'suzuki@doda.example.com',
                'phone' => '03-3456-7890',
                'agent_type' => 'human',
                'notes' => '20代〜30代前半の若手に強い。',
            ],
            [
                'organization' => 'AIマッチング株式会社',
                'name' => 'AI Recommender v2',
                'email' => 'api@ai-matching.example.com',
                'agent_type' => 'ai',
                'notes' => 'API 連携による自動推薦。精度はまだ検証中。',
            ],
        ];

        foreach ($agents as $agentData) {
            Agent::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'organization' => $agentData['organization'],
                    'name' => $agentData['name'],
                ],
                array_merge($agentData, ['company_id' => $company->id])
            );
        }

        $this->command->info('エージェントサンプルデータを作成しました。');
    }
}
