<?php

namespace App\Http\Requests\Matakuliah;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMatakuliahRequest extends FormRequest
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
            'kode' => ['required', 'string', 'max:10', 'unique:matakuliahs,kode'],
            'nama' => ['required', 'string', 'max:50'],
            'sks' => ['required', 'integer', 'min:1', 'max:3'],
            'semester' => ['required', 'integer', 'min:1', 'max:16'],
        ];
    }

    public function message(): array
    {
        return [
            'kode.unique' => 'Kode sudah terdaftar'
        ];
    }
}
