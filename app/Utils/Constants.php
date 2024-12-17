<?php
namespace App\Utils;

class Constants
{
    /**
     * Default user information.
     */
    public const USER = [
        'name'  => 'USER',
        'email' => 'user@example.com',
    ];

    /**
     * Merchant contact information.
     */
    public const MERCHANT = [
        'name'  => 'Merchant',
        'email' => 'merchant@example.com',
    ];

    /**
     * The percentage threshold for low stock levels.
     */
    public const INGREDIENT_LOW_LEVEL_PERCENTAGE = 50;
}
