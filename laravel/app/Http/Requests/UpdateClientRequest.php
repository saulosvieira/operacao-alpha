<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
        $clientId = $this->route('client');

        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email,'.$clientId,
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20|unique:clients,document,'.$clientId,
            'address' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.email' => 'O e-mail informado não é válido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Já existe um cliente cadastrado com este e-mail.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'document.max' => 'O documento não pode ter mais de 20 caracteres.',
            'document.unique' => 'Já existe um cliente cadastrado com este documento.',
            'address.max' => 'O endereço não pode ter mais de 255 caracteres.',
        ];
    }
}
