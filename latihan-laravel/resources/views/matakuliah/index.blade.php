@extends('layouts.app')

@section('judul', 'Daftar Matakuliah')

@section('konten')
<h1 class="h3 mb-4">Daftar Matakuliah</h1>
<x-kartu-info judul="Informasi">
    Data pada halaman ini masih berupa array statis. Pada modul berikutnya data akan diambil dari basis data.
</x-kartu-info>

<form action="/cari-matakuliah" method="get">
    <div class="input-group mb-3">
      <input type="text" class="form-control" name="kodeMatkul" placeholder="Masukkan kode matkul" aria-label="Masukkan kode matkul" aria-describedby="button-addon2">
      <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Cari</button>
    </div>
</form>

<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>SKS</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($dataMatakuliah as $matakuliah)
            <tr>
                <td>{{ $matakuliah['kode'] }}</td>
                <td>{{ $matakuliah['nama']}}</td>
                <td><x-badge-sks sks="{{ $matakuliah['sks'] }}">{{ $matakuliah['sks'] }}</x-badge-sks></td>
                <td>
                    <a href="{{ route('matakuliah.show', $matakuliah['kode']) }}" class="btn btn-sm btn-primary">Detail</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Data belum tersedia</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection