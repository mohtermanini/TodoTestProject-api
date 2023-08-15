<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\Task;
use App\Models\User;
use App\Models\TodoList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Actions\CreateAuthTokenAction;
use App\Http\Requests\User\User\StoreUserRequest;

class UserController extends Controller
{
    public function store(StoreUserRequest $storeUserRequest)
    {
        $user = User::create($storeUserRequest->validated());
        $token = (new CreateAuthTokenAction)->execute($user, $storeUserRequest->userAgent());
        $todolist = TodoList::create(['title' => 'My first list', 'user_id' => $user->id]);
        Task::create([
            'title' => 'My first task',
            'description' => 'This is my first task description',
            'todo_list_id' => $todolist->id
        ]);

        return $this->responseCreated(['user' => new UserResource($user), 'token' => $token]);
    }

    public function show()
    {
        $user = auth()->user();

        return $this->responseOk(new UserResource($user));
    }
}