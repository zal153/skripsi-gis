<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePosyanduRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'desa_id' => ['required', 'exists:desa,id'],
            'nama_posyandu' => ['required', 'string', 'max:255', 'unique:posyandu,nama_posyandu'],
            'alamat' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'status' => ['required', 'in:aktif,non-aktif'],
            'keterangan' => ['nullable', 'string'],
        ];
    }
}
