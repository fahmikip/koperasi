<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('loans.approve') || $this->user()?->can('loans.manage');
    }

    public function rules(): array
    {
        return ['review_notes' => [$this->routeIs('loans.reject') ? 'required' : 'nullable', 'string', 'max:2000']];
    }
}
