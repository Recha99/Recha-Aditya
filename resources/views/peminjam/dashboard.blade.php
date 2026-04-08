@extends('layouts.app')

@section('content')
<h3>Daftar Alat Tersedia</h3>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ url('/peminjam/ajukan') }}" method="POST">
    @csrf
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3 mb-3">
                <label class="form-label fw-bold">Tgl Rencana Kembali</label>
                <input type="date" name="tanggal_kembali" class="form-control" required min="{{ date('Y-m-d') }}" value="{{ old('tanggal_kembali', date('Y-m-d')) }}">
            </div>
        </div>
    </div>

    <div class="row mt-2">
        @foreach($tools as $tool)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                @if($tool->gambar)
                <img src="{{ asset('storage/' . $tool->gambar) }}" class="card-img-top" alt="{{ $tool->nama_alat }}">
                @endif
                <div class="card-body d-flex flex-column">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="tool-{{ $tool->id }}" name="tools[{{ $tool->id }}][selected]" {{ old('tools.'.$tool->id.'.selected') ? 'checked' : '' }} {{ $tool->stok > 0 ? '' : 'disabled' }}>
                        <label class="form-check-label fw-bold" for="tool-{{ $tool->id }}">
                            Pilih alat ini
                        </label>
                    </div>

                    <h5 class="card-title">{{ $tool->nama_alat }}</h5>
                    <span class="badge bg-secondary mb-2">{{ $tool->category->nama_kategori }}</span>
                    <p class="card-text">{{ $tool->deskripsi }}</p>
                    <p class="fw-bold mb-3">Sisa Stok: {{ $tool->stok }}</p>

                    <input type="hidden" name="tools[{{ $tool->id }}][tool_id]" value="{{ $tool->id }}">
                    <div class="mb-2">
                        <label class="small">Jumlah</label>
                        <input type="number" name="tools[{{ $tool->id }}][jumlah]" class="form-control form-control-sm" min="1" max="{{ $tool->stok }}" value="{{ old('tools.'.$tool->id.'.jumlah', 1) }}" {{ $tool->stok > 0 ? '' : 'disabled' }}>
                        <div class="form-text">Maksimum {{ $tool->stok }} unit</div>
                    </div>

                    @if($tool->stok == 0)
                        <span class="badge bg-danger mt-auto">Stok Habis</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
</form>
@endsection
