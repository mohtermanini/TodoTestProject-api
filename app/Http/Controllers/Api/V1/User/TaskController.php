<?php

namespace App\Http\Controllers\Api\V1\User;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Http\Request;
use App\Services\TodoListService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Requests\User\Task\StoreTaskRequest;
use App\Http\Requests\User\Task\UpdateTaskRequest;

class TaskController extends Controller
{
    public function __construct(TodoListService $todoListService)
    {
        $this->middleware(function ($request, $next) use ($todoListService) {
            try {
                $todoListService->checkIfTodoListBelongsToCurrentUser(request()->todolist);
            } catch (\Exception $e) {
                abort(404, $e->getMessage());
            }
            return $next($request);
        });
    }
    public function index($todolist_id)
    {
        $sort_col = request()->query('sort_col', 'due_date');
        $sort_order = request()->query('sort_order', 'desc');
        $search = request()->query('search');
        $tasks_per_page = request()->query('per_page', 10);

        $tasks = Task::where('todo_list_id', $todolist_id)
            ->when($search, function ($query) use ($search) {
                return $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            })
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
            'tasks' => $groupedTasks,
            'totalPages' => $tasks->lastPage()
        ]);
    }

    public function show($todolist_id, $task_id)
    {
        $task = Task::containedList($todolist_id)->firstOrFail();

        return $this->responseOk($task);
    }

    public function store(StoreTaskRequest $storeTaskRequest)
    {
        $task = Task::create($storeTaskRequest->validated());

        return $this->responseCreated(new TaskResource($task));
    }

    public function update(UpdateTaskRequest $updateTaskRequest, $todolist_id, $task_id)
    {
        $task = Task::ContainedList($todolist_id)->findOrFail($task_id);
        $task->update($updateTaskRequest->validated());

        return $this->responseOk(new TaskResource($task));
    }

    public function destroy($todolist_id, Task $task)
    {
        $task->delete();

        return $this->responseDeleted();
    }
}