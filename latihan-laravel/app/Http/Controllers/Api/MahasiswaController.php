<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Mahasiswa\StoreMahasiswaRequest;
use App\Http\Requests\Mahasiswa\UpdateMahasiswaRequest;
use App\Http\Resources\MahasiswaResource;
use App\Models\Mahasiswa;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::query()->with('programStudi');

        if ($request->filled('cari')) {
            $keyword = $request->query('cari');

            $query->where(function ($sub) use ($keyword) {
                $sub->where('nama', 'like', '%' . $keyword . '%')
                    ->orWhere('nim', 'like', '%'. $keyword . '%');
            });
        }

        if ($request->filled('angkatan')) {
            $keyword = $request->integer('angkatan');
            $query->where('angkatan', $keyword);
        }

        if ($request->filled('program_studi_id')) {
            $keyword = $request->integer('program_studi_id');
            $query->where('program_studi_id', $keyword);
        }

        $urutan = $request->query('urut', 'nama');
        $arah = $request->query('arah', 'asc');

        $kolomDiizikan = ['nama', 'nim', 'angkatan', 'ipk'];

        if (in_array($urutan, $kolomDiizikan, true)) {
            $query->orderBy($urutan, $arah === 'desc' ? 'desc' : 'asc');
        }

        $perHalaman = min($request->integer('per_halaman', 10), 100);

        return MahasiswaResource::collection($query->paginate($perHalaman));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMahasiswaRequest $request): JsonResponse
    {
        $mahasiswa = Mahasiswa::create($request->validated());

        $mahasiswa->load('programStudi');

        return response()->json([
            'sukses' => true,
            'pesan' => 'Data mahasiswa berhasil dibuat',
            'data' => new MahasiswaResource($mahasiswa)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mahasiswa $mahasiswa): JsonResponse
    {
        $mahasiswa->load('programStudi');

        return response()->json([
            'sukses' => true,
            'data' => new MahasiswaResource($mahasiswa)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMahasiswaRequest $request, Mahasiswa $mahasiswa): JsonResponse
    {
        $mahasiswa->update($request->validated());

        $mahasiswa->load('programStudi');

        return response()->json([
            'sukses' => true,
            'pesan' => 'Data mahasiswa berhasil diperbarui',
            'data' =>  new MahasiswaResource($mahasiswa)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mahasiswa $mahasiswa): JsonResponse
    {
        $mahasiswa->delete();

        return response()->json([
            'sukses' => true,
            'pesan' => 'Data mahasiswa berhasil dihapus'
        ]);
    }

    public function indexProdi(string $id) {
        $query = Mahasiswa::query()->with('programStudi')->where('program_studi_id', $id);
        return MahasiswaResource::collection($query->paginate(5));
    }
}
