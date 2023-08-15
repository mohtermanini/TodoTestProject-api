<?php

namespace Tests\Feature\User\TodoList;

use Tests\TestCase;
use App\Models\TodoList;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTodoListTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateTodoListRouteIsOnlyForAuthenticatedUsers()
    {
        $todolist_data = ['title' => 'Example Todo List'];

        $response = $this->postJson(route('todolists.store'), $todolist_data);

        $response->assertStatus(401);
    }

    public function testUserCanCreateTodoList(): void
    {
        Sanctum::actingAs($this->alice);
        $todolist_data = ['title' => 'Example Todo List'];
        $alice_todolists_count = $this->alice->todo_lists()->count();

        $response = $this->postJson(route('todolists.store'), $todolist_data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title'
            ]
        ]);
        $this->assertEquals($alice_todolists_count + 1, $this->alice->todo_lists()->count());
        $this->assertDatabaseHas(TodoList::class, [
            'title' => $todolist_data['title'],
            'user_id' => $this->alice->id
        ]);
    }

     public function testUserCanNotCreateTodoListWithSameTitleOfExistingOne(): void
    {
        Sanctum::actingAs($this->alice);
        $todolist_data = ['title' => 'Example Todo List'];        

        $response = $this->postJson(route('todolists.store'), $todolist_data);
        
        $todolist_data = ['title' => 'Example Todo List'];
        
        $response = $this->postJson(route('todolists.store'), $todolist_data);
        
        $response->assertStatus(422);
    }
}