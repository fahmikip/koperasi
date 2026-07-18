<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can($this->route('member') ? 'members.update' : 'members.create') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('member')?->id;

        return ['nik' => ['required', 'digits:16', Rule::unique('members')->ignore($id)], 'name' => ['required', 'string', 'max:255'], 'birth_place' => ['required', 'string', 'max:100'], 'birth_date' => ['required', 'date', 'before:today'], 'gender' => ['required', Rule::in(['male', 'female'])], 'address' => ['required', 'string'], 'district' => ['required', 'string', 'max:100'], 'regency' => ['required', 'string', 'max:100'], 'province' => ['required', 'string', 'max:100'], 'whatsapp' => ['required', 'regex:/^[0-9+]{9,20}$/'], 'email' => ['nullable', 'email'], 'occupation' => ['nullable', 'string', 'max:100'], 'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], 'joined_at' => ['required', 'date'], 'valid_until' => ['required', 'date', 'after_or_equal:joined_at'], 'status' => ['required', Rule::in(['active', 'inactive'])]];
    }
}
