<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('savings.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'member_id' => ['required', Rule::exists('members', 'id')->where('status', 'active')->whereNull('deleted_at')],
            'saving_type_id' => ['required', Rule::exists('saving_types', 'id')->where('is_active', true)],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'direction' => ['required', Rule::in(['deposit', 'withdrawal'])],
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2', 'max:9999999999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'member_id' => 'anggota',
            'saving_type_id' => 'jenis simpanan',
            'transaction_date' => 'tanggal transaksi',
            'direction' => 'arah transaksi',
            'amount' => 'nominal',
            'notes' => 'catatan',
        ];
    }
}
