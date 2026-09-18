<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Matakuliah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(ProgramStudiSeeder::class);
        $this->call(MatakuliahSeeder::class);

        $matakuliah = Matakuliah::all();
        
        Mahasiswa::factory()->count(30)->create()->each(function ($mahasiswa) use ($matakuliah) {
            $data = [];
            foreach ($matakuliah as $item) {
                $data[$item->id] = ['nilai' => rand(0, 100)];
            }
            $mahasiswa->matakuliah()->attach($data);
        });
    }
}
