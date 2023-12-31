<?php

namespace App\Models;

use App\Models\TodoList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'completed', 'due_date', 'todo_list_id'];

    public function scopeContainedList(Builder $query, $todolist_id)
    {
        $query->where('todo_list_id', $todolist_id);
    }

    public function todo_list()
    {
        return $this->belongsTo(TodoList::class);
    }
}