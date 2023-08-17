<?php

namespace Tests\Feature\User\TodoList;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTodoListTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexTodoListRouteIsOnlyForAuthenticatedUsers()
    {
        $response = $this->getJson(route('todolists.index'));

        $response->assertStatus(401);
    }
    public function testUserCanGetAllTheirTodosLists(): void
    {
        Sanctum::actingAs($this->alice);

        $response = $this->getJson(route('todolists.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'todolists' => [
                        '*' => [
                            'id',
                            'title',
                            'remainingTasks'
                        ]
                    ]
                ]
            ])
            ->assertJsonCount(2, 'data.todolists');
    }
}