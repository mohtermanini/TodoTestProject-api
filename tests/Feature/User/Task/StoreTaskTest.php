<?php

namespace Tests\Feature\User\Task;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Task;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTaskTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanStoreTask(): void
    {
        Sanctum::actingAs($this->alice);
        $todo_list = $this->alice->todo_lists()->get()[1];
        $task_data = [
            'title' => 'Example Task',
            'due_date' => Carbon::now()->toDateTimeString()
        ];
        $tasks_count = $todo_list->tasks()->count();

        $response = $this->postJson(
            route(
                'todolists.tasks.store',
                ['todolist' => $todo_list->id]
            ),
            $task_data
        );

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'completed',
                    'due_date'
                ]
            ])
            ->assertJson([
                'data' => [
                    'title' => $task_data['title'],
                    'due_date' => $task_data['due_date'],
                    'completed' => false
                ]
            ])
        ;
        $this->assertDatabaseHas(Task::class, [
            'title' => $task_data['title'],
            'due_date' => $task_data['due_date'],
            'completed' => false
        ]);
        $this->assertEquals($tasks_count + 1, $todo_list->tasks()->count());
    }
}