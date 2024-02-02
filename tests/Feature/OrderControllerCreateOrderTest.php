<?php

namespace Tests\Feature;

use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class OrderControllerCreateOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh --seed');
    }

    public function testCreateOrder()
    {
        $products = [
            ['id' => 1, 'quantity' => 2, 'shipping_cost' => 5.00],
            ['id' => 2, 'quantity' => 1, 'shipping_cost' => 3.50],
        ];

        $requestData = [
            'products' => $products,
            'receive_at_home' => true,
        ];

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $orderService = $this->mock(OrderService::class);

        $orderService->shouldReceive('placeOrder')
            ->once()
            ->with(
                Auth::user(),
                $requestData['products'],
                $requestData['receive_at_home']
            )
            ->andReturn(\App\Models\Order::factory()->create());

        $this->app->instance(OrderService::class, $orderService);

        $response = $this->json('POST', '/api/orders', $requestData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'order_details' => [
                    'order_id',
                    'total_price',
                    'order_items',
                ],
            ]);
    }
}
