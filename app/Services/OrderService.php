<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderConfirmation;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function placeOrder(User $user, array $productIds, bool $receiveAtHome = false)
    {
        try {
            $products = Product::find($productIds);

            $totalPrice = $this->calculateTotalPrice($products, $receiveAtHome);

            DB::beginTransaction();

            $order = new Order([
                'total_price' => $totalPrice,
            ]);

            $user->orders()->save($order);

            foreach ($products as $product) {
                $orderItem = new OrderItem([
                    'quantity' => 1,
                    'price' => $product->price,
                ]);

                $product->orders()->attach($order, ['quantity' => 1, 'price' => $product->price]);

                $order->orderItems()->save($orderItem);
            }

            DB::commit();

            Cache::tags(['user_orders', 'user_' . $user->id])->forget('orders');

            $this->sendOrderConfirmation($user, $order);

            $this->notifyAdmin($order);

            return $order;
        } catch (Exception $e) {
            DB::rollBack();

            report($e);

            throw $e;
        }
    }

    private function calculateTotalPrice($products, $receiveAtHome)
    {
        $totalPrice = $products->sum('price');

        if ($receiveAtHome) {
            $shippingCosts = $products->sum('shipping_cost');
            $totalPrice += $shippingCosts;
        }

        return $totalPrice;
    }

    private function sendOrderConfirmation(User $user, Order $order)
    {
        $user->notify(new OrderConfirmation($order));
    }

    private function notifyAdmin(Order $order)
    {
        $adminEmail = 'admin@example.com';

        $admin = User::where('email', $adminEmail)->first();

        if ($admin) {
            $admin->notify(new NewOrderNotification($order));
        }
    }
}
