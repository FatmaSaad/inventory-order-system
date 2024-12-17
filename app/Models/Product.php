<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Define the relationship between the Product and Ingredient models.
     * 
     * A product can have many ingredients. The pivot table includes the 'quantity' column.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)->withPivot('quantity');
    }

    /**
     * Define the relationship between the Product and Order models.
     * 
     * A product can belong to many orders. The pivot table includes the 'quantity' column.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity');
    }
}
