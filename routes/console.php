<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule auto-checkout for expired guests every day at 00:01
Schedule::command('guests:auto-checkout')
    ->dailyAt('00:01')
    ->runInBackground();
