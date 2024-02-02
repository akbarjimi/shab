<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OrderRetrievalFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');

        Artisan::call('db:seed');
    }

    /**
     * Test order retrieval.
     *
     * @return void
     */
    public function testOrderRetrieval()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);

        $response->assertJsonStructure([]);

        $response->assertJson([]);
    }
}
