<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createOrder(CreateOrderRequest $request)
    {
        try {
            $this->authorize('create', Order::class);

            $order = $this->orderService->placeOrder(
                Auth::user(),
                $request->input('products'),
                $request->input('receive_at_home')
            );

            return response()->json([
                'message' => 'Order placed successfully',
                'order_details' => [
                    'order_id' => $order->id,
                    'total_price' => $order->total_price,
                    'order_items' => $order->items,
                ],
            ], ResponseAlias::HTTP_OK);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'error' => 'Failed to create order',
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieve orders for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrieveOrders()
    {
        $this->authorize('retrieve', Order::class);

        $orders = Cache::tags(['user_orders', 'user_' . Auth::id()])->remember('orders', now()->addMinutes(10), function () {
            return Auth::user()->orders()->with('items')->latest()->get();
        });

        return response()->json($orders);
    }
}
