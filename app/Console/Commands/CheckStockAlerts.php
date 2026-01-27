<?php

namespace App\Console\Commands;

use App\Services\BuyerAlertService;
use Illuminate\Console\Command;

class CheckStockAlerts extends Command
{
    protected $signature = 'alerts:check-stock';
    protected $description = 'Check and trigger stock alerts (Phase 3)';

    public function handle(BuyerAlertService $service): int
    {
        $triggered = $service->checkStockAlerts();
        $this->info("Triggered {$triggered} stock alerts.");
        return self::SUCCESS;
    }
}
