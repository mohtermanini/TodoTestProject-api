<?php

namespace Tests\Feature\User\Task;

use Tests\TestCase;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteTaskTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanDeleteTask(): void
    {
        Sanctum::actingAs($this->alice);
        $task = $this->alice->todo_lists()->get()[1]->tasks()->first();
        $tasks_count = Task::count();

        $response = $this->deleteJson(
            route(
                'todolists.tasks.destroy',
                ['todolist' => $task->todo_list_id, 'task' => $task->id]
            )
        );

        $response->assertStatus(204);
        $this->assertDatabaseCount(Task::class, $tasks_count - 1)
            ->assertDatabaseMissing(Task::class, [
                'id' => $task->id
            ]);
    }
}