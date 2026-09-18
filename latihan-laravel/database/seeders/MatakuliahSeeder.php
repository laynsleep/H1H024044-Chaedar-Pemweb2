<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Matakuliah;

class MatakuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['kode' => 'TK245002', 'nama' => 'Keamanan Jaringan Komputer B', 'sks' => '2', 'semester' => '5',],
            ['kode' => 'TK245004', 'nama' => 'Internet of Things B', 'sks' => '3', 'semester' => '5',],
            ['kode' => 'TK245005', 'nama' => 'Praktikum Sistem Internet of Thing B', 'sks' => '1', 'semester' => '5',],
            ['kode' => 'TK245003', 'nama' => 'Praktikum Keamanan Jaringan Komputer A', 'sks' => '1', 'semester' => '5',],
            ['kode' => 'TK245008', 'nama' => 'Manajemen Proyek B', 'sks' => '2', 'semester' => '5',],
            ['kode' => 'TK245001', 'nama' => 'Sistem Kendali B', 'sks' => '3', 'semester' => '5',],
            ['kode' => 'TK24A001', 'nama' => 'Komputer Medis A', 'sks' => '3', 'semester' => '5',],
            ['kode' => 'TK245010', 'nama' => 'Praktikum Pemrograman Web II B', 'sks' => '1', 'semester' => '5',],
            ['kode' => 'TK245009', 'nama' => 'Pemrograman Web II B', 'sks' => '2', 'semester' => '5',],
            ['kode' => 'TK245006', 'nama' => 'Etika Profesi B', 'sks' => '2', 'semester' => '5',],
            ['kode' => 'TK245007', 'nama' => 'Metode Numerik B', 'sks' => '2', 'semester' => '5',],
        ];

        foreach ($data as $item) {
            Matakuliah::create($item);
        }
    }
}
