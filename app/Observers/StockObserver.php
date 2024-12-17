<?php

namespace App\Observers;

use App\Models\Ingredient;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Utils\Constants;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class StockObserver
{
    /**
     * Handle the "updated" event for the Ingredient model.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @return void
     */
    public function updated(Ingredient $ingredient)
    {
        // Calculate the threshold for low stock notification
        $threshold = $ingredient->threshold ?? $ingredient->stock * (Constants::INGREDIENT_LOW_LEVEL_PERCENTAGE / 100);

        if ($ingredient->stock <= $threshold && !$ingredient->notification_sent) {

            $merchant = User::findOrFail(1);

            try {
                $merchant->notify(new LowStockNotification($ingredient));
                // Mark the notification as sent to avoid duplicate notifications
                $ingredient->update(['notification_sent' => true]);
            } catch (\Exception $e) {
                // Log error if notification fails
                Log::error('Error sending notification to merchant: ' . $e->getMessage());
            }
        }
    }
}
