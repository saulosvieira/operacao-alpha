<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
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
        $examId = $this->route('exam')->id;
        $questionId = $this->route('question')->id;

        return [
            'question_number' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('questions')->where(function ($query) use ($examId) {
                    return $query->where('exam_id', $examId);
                })->ignore($questionId),
            ],
            'statement' => 'required|string|max:5000',
            'statement_image' => 'nullable|image|max:2048',
            'remove_statement_image' => 'nullable|boolean',
            'option_a' => 'required|string|max:1000',
            'option_a_image' => 'nullable|image|max:2048',
            'remove_option_a_image' => 'nullable|boolean',
            'option_b' => 'required|string|max:1000',
            'option_b_image' => 'nullable|image|max:2048',
            'remove_option_b_image' => 'nullable|boolean',
            'option_c' => 'required|string|max:1000',
            'option_c_image' => 'nullable|image|max:2048',
            'remove_option_c_image' => 'nullable|boolean',
            'option_d' => 'required|string|max:1000',
            'option_d_image' => 'nullable|image|max:2048',
            'remove_option_d_image' => 'nullable|boolean',
            'option_e' => 'required|string|max:1000',
            'option_e_image' => 'nullable|image|max:2048',
            'remove_option_e_image' => 'nullable|boolean',
            'correct_answer' => 'required|string|in:A,B,C,D,E',
            'explanation' => 'nullable|string|max:5000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question_number.required' => 'O número da questão é obrigatório.',
            'question_number.unique' => 'Já existe uma questão com este número neste simulado.',
            'statement.required' => 'O enunciado é obrigatório.',
            'option_a.required' => 'A alternativa A é obrigatória.',
            'option_b.required' => 'A alternativa B é obrigatória.',
            'option_c.required' => 'A alternativa C é obrigatória.',
            'option_d.required' => 'A alternativa D é obrigatória.',
            'option_e.required' => 'A alternativa E é obrigatória.',
            'correct_answer.required' => 'A resposta correta é obrigatória.',
            'correct_answer.in' => 'A resposta correta deve ser A, B, C, D ou E.',
            'statement_image.max' => 'A imagem do enunciado deve ter no máximo 2MB.',
            'option_a_image.max' => 'A imagem da alternativa A deve ter no máximo 2MB.',
            'option_b_image.max' => 'A imagem da alternativa B deve ter no máximo 2MB.',
            'option_c_image.max' => 'A imagem da alternativa C deve ter no máximo 2MB.',
            'option_d_image.max' => 'A imagem da alternativa D deve ter no máximo 2MB.',
            'option_e_image.max' => 'A imagem da alternativa E deve ter no máximo 2MB.',
        ];
    }
}
