<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductControllerDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_delete_product_and_clear_images()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Product $product */
        $product = Product::factory()->create(['user_id' => $user->id]);

        $image1 = UploadedFile::fake()->image('image1.jpg');
        $image2 = UploadedFile::fake()->image('image2.jpg');

        $product->addMedia($image1)->toMediaCollection();
        $product->addMedia($image2)->toMediaCollection();

        $response = $this->json('DELETE', "/api/products/{$product->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
