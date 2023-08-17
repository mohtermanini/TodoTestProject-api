<?php

namespace App\Services;

use App\Models\TodoList;

class TodoListService
{
    public function checkIfTodoListBelongsToCurrentUser($todolistId)
    {
        if (TodoList::where('id', $todolistId)->where('user_id', auth()->id())->first() === null) {
            throw new \Exception("Todo list not found for current user.");
        }
        return true;
    }
}