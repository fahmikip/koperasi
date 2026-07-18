<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'period' => ['nullable', Rule::in(['7_days', '30_days', '90_days', 'year', 'custom'])],
            'date_from' => ['nullable', 'required_if:period,custom', 'date'],
            'date_to' => ['nullable', 'required_if:period,custom', 'date', 'after_or_equal:date_from'],
        ];
    }
}
