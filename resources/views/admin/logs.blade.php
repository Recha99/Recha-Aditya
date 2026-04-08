@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3>Activity Logs</h3>
                <p class="text-muted mb-0">Riwayat aktivitas admin, petugas, dan peminjam.</p>
            </div>
            <a href="{{ url('/admin/dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Waktu</th>
                                <th width="20%">User</th>
                                <th width="25%">Aksi</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $index => $log)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->description }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada log aktivitas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
