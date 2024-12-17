<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class LowStockNotification extends Notification implements ShouldQueue

{
    use Queueable;

    protected $ingredient;
    protected $merchant;


    public function __construct($ingredient, )
    {
        $this->ingredient = $ingredient;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }
    public function getIngredient()
    {
        return $this->ingredient;
    }


    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->ingredient->name)
            ->greeting(greeting: "Hello,")
            ->line('The stock for ' . $this->ingredient->name . ' is running low.')
            ->line('Current stock: ' . $this->ingredient->stock . 'g')
            ->line('Please restock as soon as possible.')
            ->line('Thank you for using our system!');
    }


    public function toArray($notifiable)
    {
        return [
            'ingredient_id' => $this->ingredient->id,
            'name' => $this->ingredient->name,
            'stock' => $this->ingredient->stock,
        ];
    }
    public function failed(\Exception $exception)
    {
        Log::error('Queue job failed: ' . $exception->getMessage(), [
            'job' => 'LowStockNotification',
            'ingredient_id' => $this->ingredient->id ?? null,
        ]);
    }
}
