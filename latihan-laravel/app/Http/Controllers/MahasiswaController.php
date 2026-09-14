<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index() {
        $daftarMahasiswa = [
            ['nim' => 'H1A123001', 'nama' => 'Andi Prasetyo', 'angkatan' => '2023'],
            ['nim' => 'J1L000001', 'nama' => 'Verity Eclipsity', 'angkatan' => '2026'],
            ['nim' => 'H1H024042', 'nama' => 'Ariel Bijak Wicaksono', 'angkatan' => '2024']
        ];

        return view('mahasiswa.index', ['daftarMahasiswa' => $daftarMahasiswa]);
    }

    public function show(string $nim) {
        return view('mahasiswa.show', ['nim' => $nim]);
    }

    public function cari(Request $request) {
        $katakunci = $request->query('q', '');

        return response()->json([
            'kata_kunci' => $katakunci,
            'metode' => $request->method(),
            'path' => $request->path()
        ]);
    }
}
