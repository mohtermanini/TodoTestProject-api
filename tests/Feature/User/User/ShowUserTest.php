<?php

namespace Tests\Feature\User\User;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowUserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetHisInfo(): void
    {
        Sanctum::actingAs($this->alice);

        $response = $this->getJson(route('users.show'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'firstName',
                    'lastName',
                    'email'
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $this->alice->id,
                    'firstName' => $this->alice->first_name,
                    'lastName' => $this->alice->last_name,
                    'email' => $this->alice->email,
                ]
            ]);
    }
}