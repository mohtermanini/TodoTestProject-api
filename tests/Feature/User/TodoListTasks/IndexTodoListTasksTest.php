<?php

namespace Tests\Feature\User\TodoListTasks;

use Carbon\Carbon;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTodoListTasksTest extends TestCase
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
}