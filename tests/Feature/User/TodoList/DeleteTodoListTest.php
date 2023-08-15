<?php

namespace Tests\Feature\User\TodoList;

use Tests\TestCase;
use App\Models\Task;
use App\Models\TodoList;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTodoListTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanDeleteTodoList(): void
    {
        Sanctum::actingAs($this->alice);
        $todo_list = $this->alice->todo_lists()->get()[1];
        $todo_list_tasks = $todo_list->tasks()->count();
        $todo_lists_count = TodoList::count();
        $tasks_count = Task::count();

        $response = $this->deleteJson(route('todolists.destroy', ['todolist' => $todo_list->id]));

        $response->assertStatus(204);
        $this->assertDatabaseCount(TodoList::class, $todo_lists_count - 1)
            ->assertDatabaseCount(Task::class, $tasks_count - $todo_list_tasks)
            ->assertDatabaseMissing(TodoList::class, [
                'id' => $todo_list->id
            ]);
    }
}