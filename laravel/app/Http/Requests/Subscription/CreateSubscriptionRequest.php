<?php

namespace App\Http\Requests\Subscription;

use App\Domain\Subscription\Enums\PlanType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'string', Rule::in(['monthly', 'yearly'])],
            'platform_id' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.required' => 'O plano é obrigatório',
            'plan_id.in' => 'Plano inválido',
            'platform_id.required' => 'O ID da plataforma é obrigatório',
        ];
    }
}
