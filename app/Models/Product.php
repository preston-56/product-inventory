<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Define the fillable attributes for mass assignment
    protected $fillable = [
        'name',      // Product name
        'quantity',  // Quantity in stock
        'price',     // Price per item
    ];

    // Optional: Define custom accessors
    public function getTotalValueAttribute(): float
    {
        return $this->calculateTotalValue();
    }

    // Calculate the total value of the product in stock
    public function calculateTotalValue(): float
    {
        return $this->quantity * $this->price;
    }

    // Optional: Define scopes for more readable queries
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeExpensiveThan($query, $price)
    {
        return $query->where('price', '>', $price);
    }
}
