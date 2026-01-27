<?php

namespace App\Console\Commands;

use App\Services\BuyerAlertService;
use Illuminate\Console\Command;

class CheckPriceAlerts extends Command
{
    protected $signature = 'alerts:check-price';
    protected $description = 'Check and trigger price alerts (Phase 3)';

    public function handle(BuyerAlertService $service): int
    {
        $triggered = $service->checkPriceAlerts();
        $this->info("Triggered {$triggered} price alerts.");
        return self::SUCCESS;
    }
}
