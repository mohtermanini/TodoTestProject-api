<?php

namespace Tests\Feature\User\Task;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTaskTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetTask(): void
    {
        Sanctum::actingAs($this->alice);
        $task = $this->alice->todo_lists()->get()[1]->tasks()->first();

        $response = $this->getJson(
            route(
                'todolists.tasks.show',
                ['todolist' => $task->todo_list_id, 'task' => $task->id]
            )
        );

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'completed' => $task->completed,
                    'due_date' => $task->due_date
                ]
            ]);
    }

     public function testUserCanNotGetAnotherUserTask(): void
    {
        Sanctum::actingAs($this->bob);
        $task = $this->alice->todo_lists()->get()[1]->tasks()->first();

        $response = $this->getJson(
            route(
                'todolists.tasks.show',
                ['todolist' => $task->todo_list_id, 'task' => $task->id]
            )
        );

        $response->assertStatus(404);
    }
}