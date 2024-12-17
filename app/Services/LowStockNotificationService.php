<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Log;

class LowStockNotificationService
{
    /**
     * Send a low stock notification to the merchant.
     *
     * @param \App\Models\Ingredient $ingredient
     * @return void
     */
    public function sendLowStockNotification(Ingredient $ingredient)
    {
        try {
            // Retrieve the merchant (using a hardcoded ID for now)
            $merchant = User::findOrFail(1);

            // Send the low stock notification
            $merchant->notify(new LowStockNotification($ingredient));

            // Mark the notification as sent to avoid sending it multiple times
            $ingredient->update(['notification_sent' => true]);
            log::info('sent');
        } catch (\Exception $e) {
            // Log any errors that occur while sending the notification
            Log::error('Error sending low stock notification: ' . $e->getMessage());
        }
    }
}
