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
            return;
        }

        Candidate::firstOrCreate(
            [
                'company_id' => $company->id,
                'email' => 'taro.tanaka@example.com',
            ],
            [
                'name' => '田中 太郎',
                'phone' => '090-1234-5678',
                'memo' => 'デモ用候補者',
            ]
        );
    }
}
