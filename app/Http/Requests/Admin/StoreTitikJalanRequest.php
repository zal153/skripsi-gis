<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTitikJalanRequest extends FormRequest
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
            'nama_titik' => 'required|string|max:255',
            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
                Rule::unique('titik_jalan', 'latitude')
                    ->where(fn ($query) => $query->where('longitude', $this->input('longitude'))),
            ],
            'longitude' => 'required|numeric|between:-180,180',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'latitude.unique' => 'Koordinat latitude dan longitude sudah terdaftar.',
        ];
    }
}
