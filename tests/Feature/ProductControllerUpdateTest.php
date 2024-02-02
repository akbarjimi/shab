<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_update_product_with_new_images()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['user_id' => $user->id]);

        Storage::fake('public');
        $image = UploadedFile::fake()->image('product_image.jpg');

        $response = $this->json('PUT', "/api/products/{$product->id}", [
            'title' => 'Updated Product',
            'price' => 50,
            'shipping_cost' => 5,
            'photos' => [$image],
        ]);

        $response->assertStatus(200);

        $this->assertEquals('Updated Product', $product->fresh()->title);
        $this->assertEquals(50, $product->fresh()->price);

        $this->assertCount(1, $product->fresh()->getMedia());
    }

    /** @test */
    public function it_can_update_product_without_new_images()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['user_id' => $user->id]);

        $response = $this->json('PUT', "/api/products/{$product->id}", [
            'title' => 'Updated Product',
            'price' => 50,
            'shipping_cost' => 5,
        ]);

        $response->assertStatus(200);

        $this->assertEquals('Updated Product', $product->fresh()->title);
        $this->assertEquals(50, $product->fresh()->price);

        $this->assertCount(0, $product->fresh()->getMedia());
    }
}
