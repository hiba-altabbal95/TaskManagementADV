<?php

namespace App\Http\Requests\CommentRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Services\ApiResponseService;

class StoreCommentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content'   => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ];
    }

     /**
     * Modify the data before validation runs.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Merge route parameters and authenticated user ID into the request data
        $this->merge([
            'user_id' => auth()->id(), // Current authenticated user's ID
        ]);
    }
     /**
     *  method handles failure of Validation and return message
     * @param \Illuminate\Contracts\Validation\Validator $Validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(ApiResponseService::error('Validation errors', 422, $errors));
    }
}
