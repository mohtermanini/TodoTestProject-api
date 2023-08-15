<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoListResource;
use App\Http\Requests\User\TodoList\StoreTodoListRequest;
use App\Http\Requests\User\TodoList\UpdateTodoListRequest;

class TodoListController extends Controller
{
    public function index()
    {
        $todolists = TodoList::withCount([
            'tasks as completed_tasks' => function ($query) {
                return $query->where('completed', true);
            }
        ])->forAuthUser()->get();

        return $this->responseOk([
            'todolists' => TodoListResource::collection($todolists)
        ]);
    }

    public function store(StoreTodoListRequest $storeTodoListRequest)
    {
        $todolist = TodoList::create($storeTodoListRequest->validated());

        return $this->responseCreated(new TodoListResource($todolist));
    }

    public function update(UpdateTodoListRequest $updateTodoListRequest, TodoList $todolist)
    {
        $todolist->update($updateTodoListRequest->validated());

        return $this->responseOk(new TodoListResource($todolist));
    }

    public function destroy(TodoList $todolist)
    {
        $todolist->tasks()->delete();
        $todolist->delete();

        return $this->responseDeleted();
    }


}