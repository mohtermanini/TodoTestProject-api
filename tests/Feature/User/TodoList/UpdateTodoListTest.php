<?php

namespace Tests\Feature\User\TodoList;

use Tests\TestCase;
use App\Models\TodoList;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTodoListTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateTodoListRouteIsOnlyForAuthenticatedUsers()
    {
        $todo_list = $this->alice->todo_lists()->first();
        $update_data = ['title' => 'New Todo List Title'];

        $response = $this->patchJson(
            route('todolists.update', ['todolist' => $todo_list->id]),
            $update_data
        );

        $response->assertStatus(401);
    }
    public function testUserCanUpdateTodoListTitle(): void
    {
        Sanctum::actingAs($this->alice);
        $todo_list = $this->alice->todo_lists()->first();
        $update_data = ['title' => 'New Todo List Title'];

        $response = $this->patchJson(
            route('todolists.update', ['todolist' => $todo_list->id]),
            $update_data
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas(
            TodoList::class,
            [
                'id' => $todo_list->id,
                'title' => $update_data['title']
            ]
        );
    }

    public function testUserCanNotUpdateTodoListTitleWithSameTitleOfExistingOne(): void
    {
        Sanctum::actingAs($this->alice);
        $todo_list = $this->alice->todo_lists()->first();
        $update_data = ['title' => $todo_list->title];

        $response = $this->patchJson(
            route('todolists.update', ['todolist' => $todo_list->id]),
            $update_data
        );

        $response->assertStatus(422);
    }
}