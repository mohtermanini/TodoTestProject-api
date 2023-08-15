<?php

namespace Tests\Feature\User\User;

use Tests\TestCase;
use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreUserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRegister(): void
    {
        $register_data = [
            'first_name' => 'jack',
            'last_name' => 'smith',
            'email' => 'jack@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ];
        $users_count = User::count();

        $response = $this->postJson(route('users.store'), $register_data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'firstName',
                        'lastName',
                        'email'
                    ],
                    'token'
                ]
            ])
            ->assertJson([
                'data' => [
                    'user' => [
                        'firstName' => $register_data['first_name'],
                        'lastName' => $register_data['last_name'],
                        'email' => $register_data['email'],
                    ]
                ]
            ]);
        $this->assertDatabaseCount(User::class, $users_count + 1)
            ->assertDatabaseHas(User::class, [
                'first_name' => $register_data['first_name'],
                'last_name' => $register_data['last_name'],
                'email' => $register_data['email'],
            ]);
    }

    public function testRolePassedWithRegisterDataIsIgnored()
    {
        $register_data = [
            'first_name' => 'jack',
            'last_name' => 'smith',
            'email' => 'jack@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role_id' => RolesEnum::ADMIN->value
        ];

        $response = $this->postJson(route('users.store'), $register_data);

        $response->assertStatus(201);
        $this->assertDatabaseHas(User::class, [
            'email' => $register_data['email'],
            'role_id' => RolesEnum::MEMBER->value
        ]);
    }

    public function testDefaultTodoListWithOneTaskIsCreatedWhenUserRegister()
    {
        $register_data = [
            'first_name' => 'jack',
            'last_name' => 'smith',
            'email' => 'jack@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ];

        $response = $this->postJson(route('users.store'), $register_data);

        $response->assertStatus(201);
        $user = User::find($response['data']['user']['id']);
        $this->assertEquals(1, $user->todo_lists()->count());
        $this->assertEquals(1, $user->todo_lists()->first()->tasks()->count());
    }
}