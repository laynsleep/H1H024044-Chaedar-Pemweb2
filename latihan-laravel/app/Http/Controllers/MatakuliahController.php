<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatakuliahController extends Controller
{
    private $dataMatakuliah = [
            ['kode' => 'TK245004', 'nama' => 'Internet of Things', 'sks' => '3'],
            ['kode' => 'TK245006', 'nama' => 'Etika Profesi', 'sks' => '3'],
            ['kode' => 'TK245008', 'nama' => 'Manajemen Proyek', 'sks' => '3'],
            ['kode' => 'TK245001', 'nama' => 'Sistem Kendali', 'sks' => '0'],
            ['kode' => 'TK245002', 'nama' => 'Keamanan Jaringan', 'sks' => '3']
        ];

    public function index() {
        return view('matakuliah.index', ['dataMatakuliah' => $this->dataMatakuliah]);
    }

    public function show(string $kode) {
        return view('matakuliah.show', ['kode' => $kode]);
    }

    public function search(Request $request) {
        $query = $request->query('kodeMatkul', '');
        $index = array_search($query, array_column($this->dataMatakuliah, 'kode'));
        $matkul = $index !== false ? $this->dataMatakuliah[$index] : [];

        return response()->json([
            "query" => $query,
            "matkul" => $matkul
        ]);
    }
}
