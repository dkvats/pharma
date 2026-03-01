<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule activity log cleanup to run daily
Schedule::command('logs:cleanup')->daily();

// Schedule database backup to run daily at 2 AM
Schedule::command('db:backup')->dailyAt('02:00');
