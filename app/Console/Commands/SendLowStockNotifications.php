<?php

namespace App\Console\Commands;

use App\Models\Ingredient;
use App\Services\LowStockNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendLowStockNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-low-stock-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send low stock notifications to the merchant for ingredients that are running low on stock.';

    protected $notificationService;

    // Inject the LowStockNotificationService
    public function __construct(LowStockNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Get all ingredients that are low on stock
        $ingredients = Ingredient::where('stock', '<=', \DB::raw('threshold'))->get();

        // Check if there are any ingredients with low stock
        if ($ingredients->isEmpty()) {
            $this->info('No ingredients are currently low on stock.');
            return; // Exit if no low-stock ingredients
        }

        // Send notifications for each low-stock ingredient
        foreach ($ingredients as $ingredient) {
            try {
                // Use the service to send the low stock notification
                $this->notificationService->sendLowStockNotification($ingredient);
                $this->info('Low stock notification sent for ingredient: ' . $ingredient->name);
            } catch (\Exception $e) {
                // Log any errors during the notification sending process
                Log::error('Error sending notification for ingredient ' . $ingredient->id . ': ' . $e->getMessage());
                $this->error('Failed to send notification for ingredient: ' . $ingredient->name);
            }
        }

        $this->info('Low stock notifications have been processed.');
    }
}
