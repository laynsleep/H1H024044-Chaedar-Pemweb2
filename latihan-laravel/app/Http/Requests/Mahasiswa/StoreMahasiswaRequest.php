<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMahasiswaRequest extends FormRequest
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
            'program_studi_id' => ['required', 'integer', 'exists:program_studis,id'],
            'nim' => ['required', 'string', 'max:20', 'unique:mahasiswas,nim'],
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:mahasiswas,email'],
            'angkatan' => ['required', 'integer', 'min:2000', 'max:2100'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'aktif' => ['nullable', 'boolean']
        ];
    }

    public function message(): array
    {
        return [
            'nim.unique' => 'NIM tersebut sudah terdaftar',
            'email.email' => 'Format email tidak valid',
            'angkatan.nim' => 'Tahun angkatan tidak wajar'
        ];
    }
}
