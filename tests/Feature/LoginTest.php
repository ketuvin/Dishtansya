<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    public function testLoginUser() {
        $user = User::create([
            'email' => 'backend@multisyscorp.com',
            'password' => bcrypt('test123')
        ]);

        $loginData = ['email' => 'backend@multisyscorp.com', 'password' => 'test123'];

        $this->json('POST', 'v1/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "access_token"
            ]);

        $this->assertAuthenticated();
    }

    public function testLoginInvalid() {
        $user = User::create([
            'email' => 'backend@multisyscorp.com',
            'password' => bcrypt('test123')
        ]);

        $loginData = ['email' => 'backend123123@multisyscorp.com', 'password' => 'test121231233'];

        $this->json('POST', 'v1/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                "message" => "Invalid credentials"
            ]);
    }
}
