<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Observers\StockObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StockObserverTest extends TestCase
{
    use RefreshDatabase;

    protected $merchant;


    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock merchant 
        $this->merchant = User::factory()->create([
            'name' => 'Merchant',
            'email' => 'merchant@example.com',
        ]);
    }

    /**
     * Test that a notification is sent when the ingredient's stock falls below the threshold.
     */
    public function test_notification_is_sent_when_stock_below_threshold()
    {
        // Arrange
        Notification::fake(); // Prevent actual notifications from being sent during tests

        // Create an ingredient with stock below threshold
        $ingredient = Ingredient::factory()->create([
            'stock' => 5,
            'threshold' => 10,
            'notification_sent' => false,
        ]);

        // Create the StockObserver instance
        $observer = new StockObserver();


        $observer->updated($ingredient);


        Notification::assertSentTo(
            [$this->merchant],
            LowStockNotification::class,
            function ($notification) use ($ingredient) {
                // Ensure the correct ingredient is passed within the notification
                return $notification->getIngredient()->is($ingredient);
            }
        );

        // Assert that the ingredient's 'notification_sent' attribute was updated to true
        $this->assertTrue($ingredient->fresh()->notification_sent);
    }

    /**
     * Test that no notification is sent if it has already been sent.
     */
    public function test_no_duplicate_notification_sent_if_already_sent()
    {
        Notification::fake();

        $ingredient = Ingredient::factory()->create([
            'stock' => 5,
            'threshold' => 10,
            'notification_sent' => true,
        ]);

        $observer = new StockObserver();


        $observer->updated($ingredient);


        Notification::assertNothingSent();
    }
}
