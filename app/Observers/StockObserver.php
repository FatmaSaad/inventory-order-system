<?php

namespace App\Observers;

use App\Models\Ingredient;
use App\Services\LowStockNotificationService;
use App\Utils\Constants;

class StockObserver
{
    protected $notificationService;

    // Inject the notification service into the observer
    public function __construct(LowStockNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the "updated" event for the Ingredient model.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @return void
     */
    public function updated(Ingredient $ingredient)
    {
        $threshold = $this->getLowStockThreshold($ingredient);

        if ($this->isLowStock($ingredient, $threshold)) {
            $this->notificationService->sendLowStockNotification($ingredient);
        }
    }

    private function getLowStockThreshold(Ingredient $ingredient)
    {
        return $ingredient->threshold ?? $ingredient->stock * (Constants::INGREDIENT_LOW_LEVEL_PERCENTAGE / 100);
    }

    private function isLowStock(Ingredient $ingredient, $threshold)
    {
        return $ingredient->stock <= $threshold && !$ingredient->notification_sent;
    }
}
