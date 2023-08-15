<?php

namespace Tests;

use App\Models\Task;
use App\Models\User;
use App\Models\TodoList;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Carbon;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $seed = true;

    protected $alice;
    protected function setUp(): void
    {
        parent::setUp();

        $this->alice = $this->createMemberUser(email: 'alice@mail.com');
        $this->createTodoList($this->alice, 0);
        $alice_todolist_2 = $this->createTodoList($this->alice, 3);
        $this->createTask(todo_list_id: $alice_todolist_2, completed: true, due_date: Carbon::now()->subDay());
        $this->createTask(todo_list_id: $alice_todolist_2, completed: false, due_date: Carbon::now()->subDay());
        $this->createTask(todo_list_id: $alice_todolist_2, completed: true, due_date: Carbon::now()->subDays(2));

        $this->bob = $this->createMemberUser(email: 'bob@mail.com');
        $bob_todolist_1 = $this->createTodoList($this->bob, 0);
        $this->createTask(todo_list_id: $bob_todolist_1, completed: true);
        $this->createTask(todo_list_id: $bob_todolist_1, title: "Example First Title1", description: "Example First Description1", completed: true);
        $this->createTask(todo_list_id: $bob_todolist_1, title: "Example Second Title2", description: "Example Second Description2", completed: true, due_date: Carbon::now()->subDay());
        $this->createTask(todo_list_id: $bob_todolist_1, title: "Example Third Title3", description: "Example Third Description3", completed: false, due_date: Carbon::now()->subDay());
        $this->createTask(todo_list_id: $bob_todolist_1, title: "Example Fourth Title4", description: "Example Fourth Description4", completed: true, due_date: Carbon::now()->subDays(2));
    }

    protected function createMemberUser($email)
    {
        return User::factory()->state(['email' => $email])->member()->create();
    }

    protected function createTodoList($user_id, $tasks_count)
    {
        return TodoList::factory()
            ->has(Task::factory()->count($tasks_count))
            ->state(['user_id' => $user_id])
            ->create();
    }

    protected function createTask($todo_list_id, $title = null, $description = null, $completed = null, $due_date = null)
    {
        $stateArray = ['todo_list_id' => $todo_list_id];
        if ($title !== null)
            $stateArray['title'] = $title;
        if ($description !== null)
            $stateArray['description'] = $description;
        if ($completed !== null)
            $stateArray['completed'] = $completed;
        if ($due_date !== null)
            $stateArray['due_date'] = $due_date;
        return Task::factory()->state($stateArray)->create();
    }
}