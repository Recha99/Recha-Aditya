@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Dashboard Petugas</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Pending Loans -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Peminjaman Menunggu Persetujuan</h5>
        </div>
        <div class="card-body">
            @if($loans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Jumlah</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->tool->nama_alat }}</td>
                                <td>{{ $loan->jumlah ?? 1 }}</td>
                                <td>{{ $loan->tanggal_pinjam }}</td>
                                <td>{{ $loan->tanggal_kembali_rencana }}</td>
                                <td>
                                    <form action="{{ route('petugas.approve', $loan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                    </form>
                                    <form action="{{ route('petugas.reject', $loan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Tolak</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Tidak ada peminjaman menunggu persetujuan.</p>
            @endif
        </div>
    </div>

    <!-- Waiting Confirmation -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Menunggu Konfirmasi Pengembalian</h5>
        </div>
        <div class="card-body">
            @if($waitingConfirmation->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Jumlah</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Bukti Foto</th>
                                <th>Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($waitingConfirmation as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->tool->nama_alat }}</td>
                                <td>{{ $loan->jumlah ?? 1 }}</td>
                                <td>{{ $loan->tanggal_pinjam }}</td>
                                <td>{{ $loan->tanggal_kembali_rencana }}</td>
                                <td>
                                    @if($loan->bukti_foto)
                                        <a href="{{ asset('storage/' . $loan->bukti_foto) }}" target="_blank" class="btn btn-sm btn-info">Lihat Bukti</a>
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($loan->tanggal_kembali_rencana)->addDays(2);
                                        $now = now();
                                        $daysLate = $now->gt($dueDate) ? $now->diffInDays($dueDate) : 0;
                                        $fine = $daysLate * 5000;
                                    @endphp
                                    Rp {{ number_format($fine) }}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal{{ $loan->id }}">Upload Bukti & Konfirmasi</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modals for Upload Bukti Foto -->
                @foreach($waitingConfirmation as $loan)
                <div class="modal fade" id="uploadModal{{ $loan->id }}" tabindex="-1" aria-labelledby="uploadModalLabel{{ $loan->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadModalLabel{{ $loan->id }}">Upload Bukti Pengembalian - {{ $loan->tool->nama_alat }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('petugas.return.process', $loan->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="bukti_foto{{ $loan->id }}" class="form-label">Bukti Foto Pengembalian</label>
                                        <input type="file" name="bukti_foto" id="bukti_foto{{ $loan->id }}" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
                                        <div class="form-text">Upload foto pengembalian, maksimal 2MB.</div>
                                    </div>
                                    <p><strong>Peminjam:</strong> {{ $loan->user->name }}</p>
                                    <p><strong>Alat:</strong> {{ $loan->tool->nama_alat }}</p>
                                    <p><strong>Rencana Kembali:</strong> {{ $loan->tanggal_kembali_rencana }}</p>
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($loan->tanggal_kembali_rencana)->addDays(2);
                                        $now = now();
                                        $daysLate = $now->gt($dueDate) ? $now->diffInDays($dueDate) : 0;
                                        $fine = $daysLate * 5000;
                                    @endphp
                                    <p><strong>Denda Estimasi:</strong> Rp {{ number_format($fine) }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Konfirmasi Pengembalian</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-muted">Tidak ada pengembalian menunggu konfirmasi.</p>
            @endif
        </div>
    </div>

    <!-- Active Loans -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Peminjaman Sedang Berlangsung</h5>
        </div>
        <div class="card-body">
            @if($activeLoans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Jumlah</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeLoans as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->tool->nama_alat }}</td>
                                <td>{{ $loan->jumlah ?? 1 }}</td>
                                <td>{{ $loan->tanggal_pinjam }}</td>
                                <td>{{ $loan->tanggal_kembali_rencana }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Tidak ada peminjaman sedang berlangsung.</p>
            @endif
        </div>
    </div>

    <!-- Returned Loans -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Riwayat Pengembalian</h5>
        </div>
        <div class="card-body">
            @if($sudahDikembalikan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Alat</th>
                                <th>Jumlah</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Denda</th>
                                <th>Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sudahDikembalikan as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->tool->nama_alat }}</td>
                                <td>{{ $loan->jumlah ?? 1 }}</td>
                                <td>{{ $loan->tanggal_pinjam }}</td>
                                <td>
                                    {{ $loan->tanggal_kembali_aktual }}
                                    @if($loan->tanggal_kembali_aktual > $loan->tanggal_kembali_rencana)
                                        <span class="badge bg-danger">Telat</span>
                                    @else
                                        <span class="badge bg-success">Tepat Waktu</span>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->total_denda > 0)
                                        <span class="badge bg-danger">Rp {{ number_format($loan->total_denda) }}</span>
                                    @else
                                        <span class="text-success">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->bukti_foto)
                                        <a href="{{ asset('storage/' . $loan->bukti_foto) }}" target="_blank" class="btn btn-sm btn-info">Lihat Bukti</a>
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Belum ada riwayat pengembalian.</p>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('petugas.report') }}" class="btn btn-secondary">Lihat Laporan</a>
    </div>
</div>
@endsection
