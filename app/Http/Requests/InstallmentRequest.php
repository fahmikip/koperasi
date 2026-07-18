<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class InstallmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('installments.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'paid_at' => ['required', 'date', 'before_or_equal:today'],
            'principal_paid' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999999.99'],
            'interest_paid' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999999.99'],
            'penalty' => ['required', 'numeric', 'min:0', 'decimal:0,2', 'max:9999999999999.99'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $total = (float) $this->input('principal_paid', 0) + (float) $this->input('interest_paid', 0) + (float) $this->input('penalty', 0);
            if ($total <= 0) {
                $validator->errors()->add('principal_paid', 'Minimal satu komponen pembayaran harus lebih dari nol.');
            }
        }];
    }

    public function attributes(): array
    {
        return ['paid_at' => 'tanggal pembayaran', 'principal_paid' => 'pembayaran pokok', 'interest_paid' => 'pembayaran bunga', 'penalty' => 'denda', 'notes' => 'catatan'];
    }
}
