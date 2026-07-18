<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reports.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'status' => ['nullable', Rule::in(['active', 'inactive', 'submitted', 'approved', 'rejected', 'disbursed', 'paid'])],
            'direction' => ['nullable', Rule::in(['deposit', 'withdrawal'])],
            'saving_type_id' => ['nullable', 'integer', Rule::exists('saving_types', 'id')],
        ];
    }
}
