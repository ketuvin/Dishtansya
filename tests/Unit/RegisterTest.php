<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    public function testRegisterUser() {
        $data = [
            'email' => 'fuenteskevin2015@gmail.com',
            'password' => bcrypt('adminadmin')
        ];

        $this->json('POST', 'api.dishtansya.com/v1/register', $data, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "message"
            ]);
    }

    public function testEmailTaken() {
        $user = User::create([
            'email' => 'fuenteskevin2015@gmail.com',
            'password' => bcrypt('adminadmin')
        ]);

        $data = [
            'email' => 'fuenteskevin2015@gmail.com',
            'password' => bcrypt('adminadmin')
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
