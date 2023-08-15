<?php

namespace Tests\Feature\User\Task;

use Tests\TestCase;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTaskTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanUpdateTaskTitle(): void
    {
        Sanctum::actingAs($this->alice);
        $task = $this->alice->todo_lists()->get()[1]->tasks()->first();
        $update_data = ['title' => 'New Task Title'];

        $response = $this->patchJson(
            route(
                'todolists.tasks.update',
                ['todolist' => $task->todo_list_id, 'task' => $task->id]
            ),
            $update_data
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas(Task::class, [
            'id' => $task->id,
            'title' => $update_data['title']
        ]);
    }

    public function testUserCanUpdateTaskDescription(): void
    {
        Sanctum::actingAs($this->alice);
        $task = $this->alice->todo_lists()->get()[1]->tasks()->first();
        $update_data = ['description' => 'New Task Description'];

        $response = $this->patchJson(
            route(
                'todolists.tasks.update',
                ['todolist' => $task->todo_list_id, 'task' => $task->id]
            ),
            $update_data
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas(Task::class, [
            'id' => $task->id,
            'description' => $update_data['description']
        ]);
    }

    public function testUserCanUpdateTaskCompletion(): void
    {
        Sanctum::actingAs($this->alice);
        $task = $this->alice->todo_lists()->get()[1]->tasks()->first();
        $update_data = ['completed' => !$task->completed];

        $response = $this->patchJson(
            route(
                'todolists.tasks.update',
                ['todolist' => $task->todo_list_id, 'task' => $task->id]
            ),
            $update_data
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas(Task::class, [
            'id' => $task->id,
            'completed' => !$task->completed
        ]);
    }
}