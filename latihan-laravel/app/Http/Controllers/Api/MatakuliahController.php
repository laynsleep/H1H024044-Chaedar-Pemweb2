<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Matakuliah\StoreMatakuliahRequest;
use App\Http\Requests\Matakuliah\UpdateMatakuliahRequest;
use App\Http\Resources\MatakuliahResource;
use App\Models\Matakuliah;

class MatakuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Matakuliah::query();

        if ($request->filled('cari')) {
            $keyword = $request->query('cari');
            $query->where(function ($sub) use ($keyword) {
                $sub->where('kode', 'like', '%' . $keyword . '%')
                    ->orWhere('nama', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->filled('sks')) {
            $keyword = $request->query('sks');
            $query->where('sks', $keyword);
        }

        if ($request->filled('semester')) {
            $keyword = $request->query('semester');
            $query->where('semester', $keyword);
        }

        $urutan = $request->query('urutan', 'kode');
        $arah = $request->query('arah', 'asc');

        $kolomDiizinkan = ['kode', 'nama', 'sks', 'semester'];

        if (in_array($urutan, $kolomDiizinkan, true)) {
            $query->orderBy($urutan, $arah === 'desc' ? 'desc' : 'asc');
        }

        $perHalaman = min($request->query('per_halaman', 10), 100);

        return MatakuliahResource::collection($query->paginate($perHalaman));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMatakuliahRequest $request): JsonResponse
    {
        $matakuliah = Matakuliah::create($request->validated());

        return response()->json([
            'sukses' => true,
            'pesan' => 'Data matakuliah berhasil ditambahkan',
            'data' => new MatakuliahResource($matakuliah)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Matakuliah $matakuliah): JsonResponse
    {
        return response()->json([
            'sukses' => true,
            'data' => new MatakuliahResource($matakuliah)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMatakuliahRequest $request, Matakuliah $matakuliah): JsonResponse
    {
        $matakuliah->update($request->validated());

        return response()->json([
            'sukses' => true,
            'pesan' => 'Data matakuliah berhasil diperbarui',
            'data' => new MatakuliahResource($matakuliah)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matakuliah $matakuliah): JsonResponse
    {
        $matakuliah->delete();

        return response()->json([
            'sukses'=> true,
            'pesan' => 'Data matakuliah berhasil dihapus'
        ]);
    }
}
