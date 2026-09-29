<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\MatakuliahController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/status', function () {
    return response()->json([
        'sukses' => true,
        'pesan' => 'API Pemweb II aktif',
        'waktu' => now()->toIso8601String()
    ]);
});

Route::get('/program-studi/{id}/mahasiswa', [MahasiswaController::class, 'indexProdi']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/profil', [AuthController::class, 'profil'])->middleware('throttle:5,1');
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('auth/logout-semua', [AuthController::class, 'logoutSemua']);

    Route::get('/mahasiswa', [MahasiswaController::class, 'index']);
    Route::get('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'show']);

    Route::middleware('ability:mahasiswa:tulis')->group(function () {
        Route::post('/mahasiswa', [MahasiswaController::class, 'store']);
        Route::put('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'update']);
        Route::patch('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'update']);
        Route::delete('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'destroy']);
    });
});

// Route::apiResource('mahasiswa', MahasiswaController::class);
Route::apiResource('matakuliah', MatakuliahController::class);

