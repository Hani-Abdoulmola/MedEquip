<?php

namespace App\Observers;

use App\Models\Manufacturer;
use App\Services\BuyerProductService;

class ManufacturerObserver
{
    public function saved(Manufacturer $model): void
    {
        app(BuyerProductService::class)->forgetFilterCache();
    }

    public function deleted(Manufacturer $model): void
    {
        app(BuyerProductService::class)->forgetFilterCache();
    }
}
