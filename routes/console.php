<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automatic RFQ closing (also expires pending quotations)
Schedule::command('rfqs:close-expired')->hourly();

// Schedule quotation expiration (independent of RFQ deadline)
Schedule::command('quotations:expire')->hourly();

// Schedule deadline reminders (every 6 hours)
Schedule::command('rfqs:send-reminders --hours=24')->everySixHours();
Schedule::command('rfqs:send-reminders --hours=6')->everySixHours();

// 📧 Send Abandoned Cart Reminders - Run every 6 hours
Schedule::command('cart:send-abandoned-reminders --type=all')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground();

// 📊 Calculate Supplier Performance Metrics - Run daily at 2 AM
Schedule::command('suppliers:calculate-performance --period=current_month')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground();

// 📊 Calculate Last Month Performance - Run on 1st of each month
Schedule::command('suppliers:calculate-performance --period=last_month')
    ->monthlyOn(1, '03:00')
    ->withoutOverlapping()
    ->runInBackground();
