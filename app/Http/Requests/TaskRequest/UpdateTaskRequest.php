<?php

namespace App\Http\Requests\TaskRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{

    // stop validation in the first failure
    protected $stopOnFirstFailure = false;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' =>'nullable|string|in:Bug,Feature,Improvement',
            'status' =>'nullable|string|in:Open,InProgress,Completed,Blocked',
            'priority'    => 'nullable|string|in:low,medium,high',
            'date_due'    => 'nullable|date',
        ];
    }
}
