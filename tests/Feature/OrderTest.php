<?php

namespace Tests\Unit\Feature;

use App\User;
use App\Order;
use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    public function testorderProduct() {
        $user = User::create([
            'email' => 'backend@multisyscorp.com',
            'password' => bcrypt('test123')
        ]);

        $this->actingAs($user, 'api');

        $product = Product::create([
            'name' => 'Chicken Joy',
            'available_stock' => 1000
        ]);

        $orderData = ['product_id' => $product->id, 'quantity' => 555];

        $this->json('POST', 'v1/order', $orderData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJson([
                "message" => "You have successfully ordered this product",
            ]);
    }

    public function testorderProductOutOfStock() {
        $this->withoutExceptionHandling();
        $user = User::create([
            'email' => 'backend@multisyscorp.com',
            'password' => bcrypt('test123')
        ]);

        $this->actingAs($user, 'api');

        $product = Product::create([
            'name' => 'Chicken Joy',
            'available_stock' => 1000
        ]);

        $orderData = ['product_id' => $product->id, 'quantity' => 1001];

        $this->json('POST', 'v1/order', $orderData, ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                "message" => "Failed to order this product due to unavailability of the stock",
            ]);
    }
}
