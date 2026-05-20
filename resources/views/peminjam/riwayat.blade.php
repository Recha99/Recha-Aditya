@extends('layouts.app')
@section('content')
<h3>Riwayat Peminjaman Saya</h3>
<div class="card mt-3">
    <div class="card-body">
        <table class="table">

            <thead>
                <tr>
                    <th>Alat</th>
                    <th>Jumlah</th>
                    <th>Tgl Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th>Denda</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loans as $loan)
                <tr>
                    <td>{{ $loan->tool->nama_alat }}</td>
                    <td>{{ $loan->jumlah ?? 1 }}</td>
                    <td>{{ $loan->tanggal_pinjam }}</td>
                    <td>{{ $loan->tanggal_kembali_rencana }}</td>
                    <td>
                        @if($loan->status == 'pending')
                            <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                        @elseif($loan->status == 'disetujui')
                            <span class="badge bg-primary">Sedang Dipinjam</span>
                        @elseif($loan->status == 'menunggu_konfirmasi')
                            <span class="badge bg-info">Menunggu Konfirmasi Pengembalian</span>
                        @elseif($loan->status == 'kembali')
                            <span class="badge bg-success">Sudah Dikembalikan</span>
                        @elseif($loan->status == 'ditolak')
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        @if($loan->status == 'kembali' && $loan->total_denda > 0)
                            <span class="text-danger">Rp {{ number_format($loan->total_denda) }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($loan->status == 'disetujui')
                            <small class="text-muted">Harap kembalikan ke petugas sebelum tanggal rencana.</small>
                        @elseif($loan->status == 'menunggu_konfirmasi')
                            <small class="text-info">Pengajuan pengembalian sedang diproses petugas.</small>
                        @elseif($loan->status == 'kembali')
                            <small class="text-success">Diterima tanggal {{ $loan->tanggal_kembali_aktual}}</small>
                        @endif
                    </td>
                    <td>
                        @if($loan->status == 'disetujui')
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#returnModal{{ $loan->id }}">Sudah Dikembalikan</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">Belum ada riwayat peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal untuk upload bukti pengembalian -->
@foreach($loans as $loan)
@if($loan->status == 'disetujui')
<div class="modal fade" id="returnModal{{ $loan->id }}" tabindex="-1" aria-labelledby="returnModalLabel{{ $loan->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel{{ $loan->id }}">Konfirmasi Pengembalian Alat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('peminjam.return', $loan->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin alat <strong>{{ $loan->tool->nama_alat }}</strong> sudah dikembalikan?</p>
                    <p>Petugas akan memverifikasi pengembalian Anda tanpa perlu upload foto.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Beritahu Petugas</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection
