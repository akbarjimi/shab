<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;


class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_adds_product_to_cart()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $product = Product::factory()->create();

        $cartService = $this->mock(CartService::class);
        $cartService->shouldReceive('getActiveCart')
            ->once()
            ->andReturn(Cart::factory()->create(['user_id' => $user->id]));

        $cartService->shouldReceive('addProductToCart')
            ->once()
            ->andReturnNull();

        $this->app->instance(CartService::class, $cartService);

        $response = $this->postJson("/api/cart/{$product->id}/add/2");

        $response->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJson(['message' => 'Product added to cart successfully']);
    }

    /** @test */
    public function it_updates_cart_item()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $product = Product::factory()->create();

        $cartService = $this->mock(CartService::class);
        $cartService->shouldReceive('updateQuantity')
            ->once()
            ->andReturn(Cart::factory()->create(['user_id' => $user->id]));

        $this->app->instance(CartService::class, $cartService);

        $response = $this->patchJson("/api/cart/update", [
            'product' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure(['cart']);
    }
}
