<?php

namespace App\Observers;

use App\Models\ProductCategory;
use App\Services\BuyerProductService;

class ProductCategoryObserver
{
    public function saved(ProductCategory $model): void
    {
        app(BuyerProductService::class)->forgetFilterCache();
    }

    public function deleted(ProductCategory $model): void
    {
        app(BuyerProductService::class)->forgetFilterCache();
    }
}
