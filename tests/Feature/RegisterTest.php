<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    public function testRegisterUser() {
        $data = [
            'email' => 'backend@multisyscorp.com',
            'password' => bcrypt('test123')
        ];

        $this->json('POST', 'api.dishtansya.com/v1/register', $data, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "message"
            ]);
    }

    public function testEmailTaken() {
        $user = User::create([
            'email' => 'backend@multisyscorp.com',
            'password' => bcrypt('test123')
        ]);

        $data = [
            'email' => 'backend@multisyscorp.com',
            'password' => bcrypt('test123')
        ];

        $this->json('POST', 'api.dishtansya.com/v1/register', $data, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    'email' => ["The email has already been taken."]
                ]
            ]);
    }
}
