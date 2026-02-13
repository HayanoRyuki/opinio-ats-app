<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Gmail同期: 15分ごとにビズリーチ通知メールを取得
Schedule::command('gmail:sync')
    ->everyFifteenMinutes()
    ->withoutOverlapping(5)
    ->appendOutputTo(storage_path('logs/gmail-sync.log'));
