<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateAkunRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->is($this->route('akun')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('akun')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$userId],
            'current_password' => [
                'nullable',
                'required_with:password',
                function ($attribute, $value, $fail) {
                    $akun = $this->route('akun');
                    if ($akun && $value && ! Hash::check($value, $akun->password)) {
                        $fail('Password awal tidak cocok.');
                    }
                },
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'required_with:current_password'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'current_password.required_with' => 'Password awal wajib diisi jika ingin mengubah password.',
            'password.required_with' => 'Password baru wajib diisi jika password awal dimasukkan.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ];
    }
}
