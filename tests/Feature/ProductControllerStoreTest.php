<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductControllerStoreTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_create_a_product()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('test.jpeg')->mimeType('image/jpeg');


        $data = [
            'title' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 0, 100),
            'shipping_cost' => $this->faker->randomFloat(2, 0, 50),
            'photos' => [$file],
        ];

        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('products', [
            'title' => $data['title'],
            'price' => $data['price'],
            'shipping_cost' => $data['shipping_cost'],
        ]);

        $product = Product::where('title', $data['title'])->first();
        $this->assertCount(1, $product->getMedia());

    }

    /** @test */
    public function it_validates_required_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'price']);
    }
}
