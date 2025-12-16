<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => ['required', 'string', 'exists:questions,id'],
            'answer' => ['required', 'string', Rule::in(['A', 'B', 'C', 'D', 'E'])],
            'time_seconds' => ['nullable', 'integer', 'min:0'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'question_id.required' => 'Question ID is required',
            'question_id.exists' => 'Question not found',
            'answer.required' => 'Answer is required',
            'answer.in' => 'Answer must be A, B, C, D, or E',
            'time_seconds.integer' => 'Time must be an integer',
            'time_seconds.min' => 'Time cannot be negative',
        ];
    }
}
