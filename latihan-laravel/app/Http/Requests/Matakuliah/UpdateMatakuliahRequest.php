<?php

namespace App\Http\Requests\Matakuliah;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMatakuliahRequest extends FormRequest
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
        $id = $this->route('matakuliah')->id;

        return [
            'kode' => ['sometimes', 'string', 'max:10', 'unique:matakuliahs,kode,'],
            'nama' => ['sometimes', 'string', 'max:50'],
            'sks' => ['sometimes', 'integer', 'min:1', 'max:3'],
            'semester' => ['sometimes', 'integer', 'min:1', 'max:16'],
        ];
    }
}
