<?php

namespace Tests\Feature\Auth\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreAuthTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogin(): void
    {
        $login_data = ['email' => $this->alice->email, 'password' => 'Password1'];

        $response = $this->postJson(route('auth.store'), $login_data);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.id', $this->alice->id)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'firstName',
                        'lastName',
                        'email',
                    ],
                    'token'
                ]
            ]);
    }

    public function testUserCanNotLoginWithInvalidPassword()
    {
        $login_data = ['email' => $this->alice->email, 'password' => 'NotPassword1'];

        $response = $this->postJson(route('auth.store'), $login_data);

        $response->assertStatus(422);
    }

    public function testUserCanNotLoginWithInvalidEmail()
    {
        $login_data = ['email' => 'fake email', 'password' => 'Password1'];

        $response = $this->postJson(route('auth.store'), $login_data);

        $response->assertStatus(422);
    }
}