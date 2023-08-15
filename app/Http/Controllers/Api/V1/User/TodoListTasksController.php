<?php

namespace App\Http\Controllers\Api\V1\User;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TodoListResource;

class TodoListTasksController extends Controller
{
    public function index($todolist_id)
    {
        $sort_col = request()->query('sort_col', 'due_date');
        $sort_order = request()->query('sort_order', 'desc');
        $tasks_per_page =  request()->query('per_page', 10);

        $tasks = Task::where('todo_list_id', $todolist_id)
            ->orderBy($sort_col, $sort_order)
            ->paginate($tasks_per_page);

        $groupedTasks = [];
        foreach ($tasks as $task) {
            if ($sort_col === 'due_date') {
                $parsed_due_date = Carbon::parse($task->due_date);
                $label = $parsed_due_date->toFormattedDateString();
                if ($parsed_due_date->isToday()) {
                    $label = 'Today';
                }
                if ($parsed_due_date->isYesterday()) {
                    $label = 'Yesterday';
                }
            } else if ($sort_col === "completed") {
                $label = 'Completed';
                if (!$task->completed) {
                    $label = 'Todo';
                }
            }
            if (!array_key_exists($label, $groupedTasks)) {
                $groupedTasks[$label] = [];
            }
            array_push($groupedTasks[$label], $task);
        }

        return $this->responseOk([
            'tasks' => $groupedTasks
        ]);
    }
}