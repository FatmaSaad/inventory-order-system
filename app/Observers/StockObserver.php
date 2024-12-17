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
     * This checks if the ingredient's stock has reached a low level
     * and sends a notification to the merchant if not already sent.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @return void
     */
    public function updated(Ingredient $ingredient)
    {
        // Calculate the threshold stock level for low stock notification
        $threshold = $this->getLowStockThreshold($ingredient);

        // Check if the ingredient's stock is below or equal to the threshold
        // and if the notification hasn't been sent yet
        if ($this->isLowStock($ingredient, $threshold)) {
            $this->sendLowStockNotification($ingredient);
        }
    }

    /**
     * Calculate the threshold stock level for low stock notification.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @return float
     */
    private function getLowStockThreshold(Ingredient $ingredient)
    {
        // Use the threshold value from the ingredient or calculate it
        return $ingredient->threshold ?? $ingredient->stock * (Constants::INGREDIENT_LOW_LEVEL_PERCENTAGE / 100);
    }

    /**
     * Check if the ingredient's stock is low and notification needs to be sent.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @param  float  $threshold
     * @return bool
     */
    private function isLowStock(Ingredient $ingredient, $threshold)
    {
        // Check if stock is below or equal to the threshold and notification has not been sent
        return $ingredient->stock <= $threshold && !$ingredient->notification_sent;
    }

    /**
     * Send a low stock notification to the merchant.
     *
     * @param  \App\Models\Ingredient  $ingredient
     * @return void
     */
    private function sendLowStockNotification(Ingredient $ingredient)
    {
        try {
            // Retrieve the merchant (using a hardcoded ID for now)
            $merchant = User::findOrFail(1);

            // Send the low stock notification
            $merchant->notify(new LowStockNotification($ingredient));

            // Mark the notification as sent to avoid sending it multiple times
            $ingredient->update(['notification_sent' => true]);
        } catch (\Exception $e) {
            // Log any errors that occur while sending the notification
            Log::error('Error sending low stock notification: ' . $e->getMessage());
        }
    }
}
