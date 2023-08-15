<?php

namespace App\Http\Requests\User\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'due_date' => ['required', 'date'],
            'todo_list_id' => ['required', 'integer', 'exists:todo_lists,id']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['todo_list_id' => (int) $this->todolist]);
    }
}