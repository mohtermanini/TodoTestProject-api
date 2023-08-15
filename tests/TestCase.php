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
        $this->createTask($alice_todolist_2, true, Carbon::now()->subDay());
        $this->createTask($alice_todolist_2, false, Carbon::now()->subDay());
        $this->createTask($alice_todolist_2, true, Carbon::now()->subDays(2));

        $this->bob = $this->createMemberUser(email: 'bob@mail.com');
        $bob_todolist_1 = $this->createTodoList($this->bob, 2);
        $this->createTask($bob_todolist_1, true);
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

    protected function createTask($todo_list_id, $completed, $due_date = null)
    {
        $task = Task::factory()
            ->state([
                'todo_list_id' => $todo_list_id,
                'completed' => $completed,
                'due_date' => $due_date ? $due_date->toDateTimeString() : Carbon::now()->toDateTimeString()
            ]);
        return $task->create();
    }
}