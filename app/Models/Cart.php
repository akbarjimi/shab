<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the cart.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items associated with the cart.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Check if the cart is expired based on a given expiration time.
     *
     * @param int $expirationTimeInMinutes
     * @return bool
     */
    public function isExpired($expirationTimeInMinutes)
    {
        return $this->updated_at->diffInMinutes(now()) > $expirationTimeInMinutes;
    }
}
