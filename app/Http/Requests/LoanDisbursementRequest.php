<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanDisbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('loans.disburse') || $this->user()?->can('loans.manage');
    }

    public function rules(): array
    {
        return [
            'disbursed_at' => ['required', 'date', 'before_or_equal:today'],
            'disbursement_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
