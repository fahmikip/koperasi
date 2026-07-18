<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('loans.create') || $this->user()?->can('loans.manage');
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', Rule::exists('members', 'id')->where('status', 'active')->whereNull('deleted_at')],
            'principal_amount' => ['required', 'numeric', 'min:1000', 'decimal:0,2', 'max:9999999999999.99'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100', 'decimal:0,4'],
            'term_months' => ['required', 'integer', 'min:1', 'max:120'],
            'applied_at' => ['required', 'date', 'before_or_equal:today'],
            'purpose' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
