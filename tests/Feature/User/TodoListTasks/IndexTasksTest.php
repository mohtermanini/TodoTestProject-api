<?php

namespace Tests\Feature\User\Task;

use Carbon\Carbon;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTasksTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetTodoListTasks(): void
    {
        Sanctum::actingAs($this->alice);
        $todo_list = $this->alice->todo_lists()->get()[1];

        $response = $this->getJson(
            route(
                'todolists.tasks.index',
                [
                    'todolist' => $todo_list->id,
                    'per_page' => 10,
                    'sort_col' => 'due_date',
                    'sort_order' => 'desc'
                ]
            )
        );

        $name = Carbon::now()->subDays(2)->toFormattedDateString();
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.tasks.Today')
            ->assertJsonCount(2, 'data.tasks.Yesterday')
            ->assertJsonCount(1, "data.tasks.$name");
    }

    public function testTodayTasksBecomeYesterdayTasksAfterOneDay(): void
    {
        Sanctum::actingAs($this->alice);
        $todo_list = $this->alice->todo_lists()->get()[1];
        $this->travel(1)->day();

        $response = $this->getJson(route('todolists.tasks.index', ['todolist' => $todo_list->id]));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.tasks.Yesterday')
            ->assertJsonMissingPath('data.tasks.Today');
    }

    public function testUserCanSearchTodoListTasksByTitle(): void
    {
        Sanctum::actingAs($this->bob);
        $todo_list = $this->bob->todo_lists()->first();

        $response = $this->getJson(
            route(
                'todolists.tasks.index',
                ['todolist' => $todo_list->id, 'search' => 'First Title']
            )
        );

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.tasks')
            ->assertJsonCount(1, 'data.tasks.Today')
            ->assertJson([
                'data' => [
                    'tasks' => [
                        'Today' => [
                            [
                                'title' => 'Example First Title1'
                            ]
                        ]
                    ]
                ]
            ]);

        $response = $this->getJson(
            route(
                'todolists.tasks.index',
                ['todolist' => $todo_list->id, 'search' => 'd Title']
            )
        );

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.tasks')
            ->assertJsonCount(2, 'data.tasks.Yesterday')
            ->assertJson([
                'data' => [
                    'tasks' => [
                        'Yesterday' => [
                            [
                                'title' => 'Example Second Title2'
                            ],
                            [
                                'title' => 'Example Third Title3'
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function testUserCanSearchTodoListTasksByDescription(): void
    {
        Sanctum::actingAs($this->bob);
        $todo_list = $this->bob->todo_lists()->first();

        $response = $this->getJson(
            route(
                'todolists.tasks.index',
                ['todolist' => $todo_list->id, 'search' => 'ption1']
            )
        );

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.tasks')
            ->assertJsonCount(1, 'data.tasks.Today')
            ->assertJson([
                'data' => [
                    'tasks' => [
                        'Today' => [
                            [
                                'description' => 'Example First Description1'
                            ]
                        ]
                    ]
                ]
            ]);

        $response = $this->getJson(
            route(
                'todolists.tasks.index',
                ['todolist' => $todo_list->id, 'search' => 'd Description']
            )
        );

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.tasks')
            ->assertJsonCount(2, 'data.tasks.Yesterday')
            ->assertJson([
                'data' => [
                    'tasks' => [
                        'Yesterday' => [
                            [
                                'description' => 'Example Second Description2'
                            ],
                            [
                                'description' => 'Example Third Description3'
                            ]
                        ]
                    ]
                ]
            ]);
    }
}