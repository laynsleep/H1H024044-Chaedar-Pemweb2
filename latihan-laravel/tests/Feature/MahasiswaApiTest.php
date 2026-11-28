<?php

namespace Tests\Feature;

use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MahasiswaApiTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_daftar_mahasiswa_menolak_permintaan_tanpa_token(): void
    {    
        $respons = $this->getJson('/api/mahasiswa');

        $respons->assertStatus(401);
    }

    public function test_daftar_mahasiswa_mengembalikan_struktur_yang_benar(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $respons = $this->getJson('/api/mahasiswa');

        $respons->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => ['id', 'nim', 'nama', 'angkatan', 'ipk'],
            ],
            'meta' => ['current_page', 'last_page', 'total'],
        ]);
    }

    public function test_membuat_mahasiswa_dengan_data_valid(): void
    {
        Sanctum::actingAs(User::factory()->create(['peran' => 'admin']), ['mahasiswa:tulis']);

        $prodi = ProgramStudi::create([
            'kode' => 'TK',
            'nama' => 'Teknik Komputer',
            'jenjang' => 'S1',
        ]);

        $respons = $this->postJson('/api/mahasiswa', [
            'program_studi_id' => $prodi->id,
            'nim' => 'H1A125010',
            'nama' => 'Gita Permata',
            'email' => 'gita@example.com',
            'angkatan' => 2025,
            'ipk' => 3.70,
        ]);

        $respons->assertStatus(201);

        $this->assertDatabaseHas('mahasiswas', ['nim' => 'H1A125010']);
    }

    public function test_membuat_mahasiswa_dengan_data_tidak_valid(): void
    {
        Sanctum::actingAs(User::factory()->create(['peran' => 'admin']), ['mahasiswa:tulis']);

        $respons = $this->postJson('/api/mahasiswa', [
            'nama' => 'Ab',
        ]);
        
        $respons->assertStatus(422);
    }
}
