@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h3>Pengembalian Alat</h3>
    <p class="text-muted">Pastikan pengembalian dilengkapi bukti foto sekali upload.</p>
</div>

<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Peminjam</label>
            <input type="text" class="form-control" value="{{ $loan->user->name }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Alat</label>
            <input type="text" class="form-control" value="{{ $loan->tool->nama_alat }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="text" class="form-control" value="{{ $loan->tanggal_pinjam }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Rencana Kembali</label>
            <input type="text" class="form-control" value="{{ $loan->tanggal_kembali_rencana }}" disabled>
        </div>

        <form action="{{ route('petugas.return', $loan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="bukti_foto" class="form-label">Bukti Foto Pengembalian</label>
                <input type="file" name="bukti_foto" id="bukti_foto" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
                <div class="form-text">Upload foto pengembalian, maksimal 2MB.</div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Konfirmasi: Alat sudah diterima kembali dan kondisi baik?')">Proses Pengembalian</button>
            </div>

        </form>
    </div>
</div>
@endsection
