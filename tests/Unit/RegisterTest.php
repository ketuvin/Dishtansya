<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    public function test_can_register_user() {
        $data = [
            'email' => 'fuenteskevin2015@gmail.com',
            'password' => 'adminadmin'
        ];

        $this->json('POST', 'api.dishtansya.com/v1/register', $data, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                "message"
            ]);
    }
}
