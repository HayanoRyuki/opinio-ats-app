<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(
            ['slug' => 'opinio'],
            [
                'name' => 'Opinio Inc.',
            ]
        );
    }
}
