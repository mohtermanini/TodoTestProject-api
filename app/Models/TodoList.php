<?php

namespace App\Models;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TodoList extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'user_id'];

    protected static function booted()
    {
        static::creating(function ($todolist) {
            if (auth()->check()) {
                $todolist->user_id = auth()->id();
            }
        });
    }

    public function scopeForAuthUser(Builder $query)
    {
        $query->where('user_id', auth()->id());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}