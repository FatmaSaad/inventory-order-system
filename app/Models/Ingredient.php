<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'stock', 'threshold', 'notification_sent'];
    protected $casts = [
        'notification_sent' => 'boolean',
    ];
    /**
     * Define the relationship between the Ingredient and Product models.
     * 
     * An ingredient can belong to many products. The pivot table includes the 'quantity' column.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }
}
