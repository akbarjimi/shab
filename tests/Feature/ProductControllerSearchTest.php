<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerSearchTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    /** @test */
    public function it_can_search_products_with_valid_parameters()
    {
        Product::factory()->create(['title' => 'Product A', 'price' => 20, 'shipping_cost' => 5]);
        Product::factory()->create(['title' => 'Product B', 'price' => 30, 'shipping_cost' => 8]);

        $response = $this->json('GET', '/api/products/search', [
            'title' => 'Product',
            'maxPrice' => 40,
            'sortBy' => 'price',
            'receiveAtHome' => true,
        ]);

        $response->assertStatus(200);

        $response->assertJsonCount(1);

        $response->assertJsonStructure(['products' => [['id', 'title', 'price', 'shipping_cost', 'total_price']]]);
    }

    /** @test */
    public function it_returns_validation_error_for_invalid_parameters()
    {
        $response = $this->json('GET', '/api/products/search', [
            'title' => 'Product',
            'maxPrice' => 'invalid',
            'sortBy' => 'invalid',
            'receiveAtHome' => 'invalid',
        ]);

        $response->assertStatus(422);

        $response->assertJsonStructure(['message', 'errors' => ['maxPrice', 'sortBy', 'receiveAtHome']]);
    }
}
