@extends('layouts.app')

@section('title', 'Log Aktivitas Loket')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2>Log Aktivitas Login Loket</h2>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark" style="border-bottom: 2px solid var(--gold-accent);">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Petugas</th>
                            <th>Loket</th>
                            <th>Login Pada</th>
                            <th>Logout Pada</th>
                            <th>Durasi Sesi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-3 text-muted small">
                                    {{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $log->user->name }}</div>
                                    <small class="text-muted">{{ $log->user->email }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $log->counter->name }}</span>
                                </td>
                                <td>{{ $log->login_at->format('d M Y H:i:s') }}</td>
                                <td>
                                    @if($log->logout_at)
                                        {{ $log->logout_at->format('d M Y H:i:s') }}
                                    @else
                                        <span class="badge bg-success">Sedang Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->logout_at)
                                        {{ $log->login_at->diffForHumans($log->logout_at, true) }}
                                    @else
                                        {{ $log->login_at->diffForHumans(now(), true) }} (Aktif)
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x display-4 d-block mb-3"></i>
                                    Belum ada data log aktivitas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection