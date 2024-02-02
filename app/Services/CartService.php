<?php

namespace App\Services;


use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function addToCart(User $user, Product $product, $quantity = 1)
    {
        $cart = $user->carts()->where('status', 'active')->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => 'active',
            ]);
        }

        $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
            ]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return $cart;
    }

    public function removeFromCart(User $user, Product $product)
    {
        $cart = $user->carts()->where('status', 'active')->first();

        if ($cart) {
            $cart->cartItems()->where('product_id', $product->id)->delete();
        }

        return $cart;
    }

    public function updateQuantity(User $user, Product $product, $quantity)
    {
        $cart = $user->carts()->where('status', 'active')->first();

        if ($cart) {
            $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $quantity,
                ]);
            }
        }

        return $cart;
    }

    public function convertToOrder(User $user)
    {
        return DB::transaction(function () use ($user) {
            $cart = $user->carts()->where('status', 'active')->first();

            if (!$cart) {
                return null;
            }

            $cart->update([
                'status' => 'ordered',
            ]);

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $cart->cartItems()->sum(DB::raw('quantity * unit_price')),
            ]);

            $cartItems = $cart->cartItems;

            foreach ($cartItems as $cartItem) {
                $order->orderItems()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->product->price,
                ]);
            }

            return $order;
        });
    }

    public function checkout(User $user)
    {
        $order = $this->convertToOrder($user);

        if ($order) {
            $this->clearCart($user);

            return $order;
        }

        return null;
    }

    private function clearCart(User $user)
    {
        $user->carts()->where('status', 'active')->delete();
    }

    public function getActiveCart(User $user)
    {
        $cart = $user->carts()->where('status', 'active')->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => 'active',
            ]);
        }

        return $cart;
    }

    public function addProductToCart(Cart $cart, Product $product, $quantity)
    {
        $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
            ]);
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return $cartItem;
    }
}
