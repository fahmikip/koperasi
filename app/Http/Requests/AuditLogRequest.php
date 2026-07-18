<?php

namespace App\Http\Requests;

use App\Models\Installment;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Saving;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuditLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('audit.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'event' => ['nullable', Rule::in(['created', 'updated', 'deleted', 'login', 'logout', 'other'])],
            'subject_type' => ['nullable', Rule::in([Member::class, Saving::class, Loan::class, Installment::class])],
            'causer_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ];
    }
}
