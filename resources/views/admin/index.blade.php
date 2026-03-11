@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Panel Admin</h2>
            @php
                $today = now()->format('Y-m-d');
                $unsetQuotas = \App\Models\Service::where('quota_date', '!=', $today)->orWhereNull('quota_date')->count();
            @endphp
            @if($unsetQuotas > 0)
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mt-3">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Perhatian:</strong> Ada {{ $unsetQuotas }} layanan yang belum diatur kuotanya untuk hari ini.
                        Antrian tidak dapat diambil sampai kuota ditentukan.
                        <a href="{{ route('admin.quotas') }}" class="btn btn-warning btn-sm ms-3">Atur Sekarang</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Master Data Summary (Ringkasan Sistem) dengan Ikon -->
    <div class="row g-3 mb-5">
        <div class="col-12">
            <h5 class="text-secondary text-uppercase letter-spacing-1 mb-3 border-bottom pb-2">Ringkasan Sistem & Master
                Data</h5>
            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="card text-white shadow-sm border-0 h-100"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-gear-fill display-5 mb-2 text-warning"></i>
                            <h6 class="mb-1 small text-uppercase opacity-75">Total Layanan</h6>
                            <h2 class="fw-bold mb-0">{{ $serviceCount }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card text-white shadow-sm border-0 h-100"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-display display-5 mb-2 text-warning"></i>
                            <h6 class="mb-1 small text-uppercase opacity-75">Total Loket</h6>
                            <h2 class="fw-bold mb-0">{{ $counterCount }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card text-white shadow-sm border-0 h-100"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-people-fill display-5 mb-2 text-warning"></i>
                            <h6 class="mb-1 small text-uppercase opacity-75">User Petugas</h6>
                            <h2 class="fw-bold mb-0">{{ $userCount }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card text-white shadow-sm border-0 h-100"
                        style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-activity display-5 mb-2 text-warning"></i>
                            <h6 class="mb-1 small text-uppercase opacity-75">Loket Aktif</h6>
                            <h2 class="fw-bold mb-0" id="stat-active">{{ $activeCounterCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Statistics -->
    <div class="row g-4 mb-5">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body bg-light rounded shadow-sm">
                    <form action="{{ route('admin.index') }}" method="GET" class="row g-3" id="main-filter-form">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter me-1"></i> Filter Statistik Global
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Global Summary -->
        <div class="col-6 col-md-6">
            <div class="card bg-dark text-white shadow-sm border-0 h-100 border-start border-5"
                style="border-color: var(--gold-accent) !important;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h6 class="text-uppercase small opacity-75 mb-0">Total Antrian Diambil</h6>
                        <h1 class="fw-bold mb-0 display-4" id="stat-total" style="color: var(--gold-accent);">
                            {{ $totalQueues }}
                        </h1>
                    </div>
                    <i class="bi bi-ticket-perforated fs-1 opacity-50" style="color: var(--gold-accent);"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6">
            <div class="card bg-dark text-white shadow-sm border-0 h-100 border-start border-success border-5">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h6 class="text-uppercase small opacity-75 mb-0">Total Terlayani (Selesai)</h6>
                        <h1 class="fw-bold mb-0 display-4" id="stat-finished">{{ $finishedQueues }}</h1>
                    </div>
                    <i class="bi bi-check2-circle fs-1 text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Per Service (Horizontal Adaptive Row) -->
    <div class="row mb-5">
        <div class="col-12 mt-4">
            <h4 class="mb-3"><i class="bi bi-bar-chart-fill me-2 text-primary"></i> Statistik Antrian Per Layanan</h4>
            <div class="d-flex gap-3 overflow-x-auto pb-3" style="scrollbar-width: thin;">
                @foreach($serviceStats as $stat)
                    <div style="flex: 1 1 0px; min-width: 250px;">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header border-0 text-white d-flex justify-content-between align-items-center py-3"
                                style="background: {{ $stat['hex_color'] }}">
                                <h6 class="mb-0 fw-bold text-truncate">{{ $stat['name'] }} ({{ $stat['prefix'] }})</h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center g-0">
                                    <div class="col-6 border-end">
                                        <div class="small text-muted text-uppercase mb-1" style="font-size: 0.7rem;">Diambil
                                        </div>
                                        <div class="h3 fw-bold mb-0 text-dark">{{ $stat['total'] }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="small text-muted text-uppercase mb-1" style="font-size: 0.7rem;">Terlayani
                                        </div>
                                        <div class="h3 fw-bold mb-0 text-success">{{ $stat['finished'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Real-time Activity Monitoring -->
    <div class="row g-4 mb-5">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i> Antrian Sedang Dipanggil</h5>
                    <span class="badge bg-white text-primary" id="stat-calling-count">{{ $callingQueues->count() }}
                        Aktif</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-uppercase small fw-bold">No. Antrian</th>
                                    <th class="text-uppercase small fw-bold">Layanan</th>
                                    <th class="text-uppercase small fw-bold">Loket / Petugas</th>
                                    <th class="text-end pe-3 text-uppercase small fw-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="calling-queues-container">
                                @include('admin.partials.calling_queues')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-person-check-fill me-2"></i> Petugas Online</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="active-staff-container">
                        @include('admin.partials.active_staff')
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <!-- Menu Master Column -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100 text-white"
                style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="card-header bg-transparent border-0 py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2"></i> Menu Pengaturan & Laporan Master</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded-bottom overflow-hidden">
                        <a href="{{ route('admin.performance') }}"
                            class="list-group-item list-group-item-action py-3 fw-bold bg-transparent text-white border-white border-opacity-10">
                            <i class="bi bi-star-fill me-2 text-warning"></i> Laporan Performa Petugas Loket
                        </a>
                        <a href="{{ route('admin.quotas') }}"
                            class="list-group-item list-group-item-action py-3 bg-transparent text-white border-white border-opacity-10">
                            <i class="bi bi-shield-check me-2 text-warning"></i> Atur Kuota Antrian Hari Ini
                        </a>
                        <a href="{{ route('admin.logs') }}"
                            class="list-group-item list-group-item-action py-3 bg-transparent text-white border-white border-opacity-10">
                            <i class="bi bi-clock-history me-2 text-warning"></i> Log Aktivitas Login Loket
                        </a>
                        <a href="{{ route('admin.users') }}"
                            class="list-group-item list-group-item-action py-3 bg-transparent text-white border-white border-opacity-10">
                            <i class="bi bi-person-plus me-2 text-warning"></i> Manajemen Petugas Baru
                        </a>
                        <a href="{{ route('admin.services') }}"
                            class="list-group-item list-group-item-action py-3 bg-transparent text-white border-white border-opacity-10">
                            <i class="bi bi-plus-square me-2 text-warning"></i> Kelola Jenis Layanan
                        </a>
                        <a href="{{ route('admin.counters') }}"
                            class="list-group-item list-group-item-action py-3 bg-transparent text-white border-white border-opacity-10">
                            <i class="bi bi-cpu-fill me-2 text-warning"></i> Konfigurasi Penambahan Loket
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- App Info Column -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 text-white"
                style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg"
                            style="width: 70px; height: 70px;">
                            <i class="bi bi-info-circle-fill fs-1"></i>
                        </div>
                        <h4 class="mt-3 fw-bold">Informasi Aplikasi</h4>
                        <div class="badge bg-warning text-dark px-3 py-2 rounded-pill mt-2">Versi 2.1</div>
                        <div class="small mt-2 opacity-75">Update: 06 Maret 2026</div>
                    </div>

                    <div class="text-center px-2">
                        <p class="mb-4 fw-bold">"ANT APP adalah sistem informasi antrian pelayanan wajib pajak real time"
                        </p>
                        <hr class="border-white border-opacity-25">
                    </div>

                    <div class="small opacity-90 mt-3">
                        <p class="mb-3 fw-bold text-uppercase letter-spacing-1 text-warning"><i
                                class="bi bi-shuffle me-2"></i>Alur Antrian:</p>
                        <ul class="list-unstyled">
                            <li class="mb-3 d-flex align-items-center">
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                    style="width: 30px; height: 30px; min-width: 30px;">
                                    <small class="fw-bold">1</small>
                                </div>
                                <span>Ambil Antrian</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                    style="width: 30px; height: 30px; min-width: 30px;">
                                    <small class="fw-bold">2</small>
                                </div>
                                <span>Tunggu Panggilan</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                    style="width: 30px; height: 30px; min-width: 30px;">
                                    <small class="fw-bold">3</small>
                                </div>
                                <span>Menuju Loket</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    @push('scripts')
        <script>
            let currentVersion = "{{ \Illuminate\Support\Facades\Storage::disk('local')->get('config_version.txt') ?? 0 }}";

            function checkUpdates() {
                const startDate = document.querySelector('input[name="start_date"]').value;
                const endDate = document.querySelector('input[name="end_date"]').value;
                const url = `{{ route('admin.realtime') }}?start_date=${startDate}&end_date=${endDate}`;

                fetch(url)
                    .then(response => {
                        if (response.status === 401) {
                            window.location.reload();
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Update stats
                        document.getElementById('stat-total').innerText = data.totalQueues;
                        document.getElementById('stat-finished').innerText = data.finishedQueues;
                        document.getElementById('stat-active').innerText = data.activeCounterCount;

                        // Update lists
                        document.getElementById('calling-queues-container').innerHTML = data.html_calling;
                        document.getElementById('active-staff-container').innerHTML = data.html_staff;

                        // Update badge count
                        const callingCount = (data.html_calling.match(/<tr>/g) || []).length;
                        document.getElementById('stat-calling-count').innerText = (callingCount || 0) + ' Aktif';

                        // If config version changes significantly (master data changed), still allow full reload
                        if (data.config_version && data.config_version !== currentVersion) {
                            // Optional: window.location.reload(); 
                            // But for now, let's just update the version to avoid reload loops if data is already updated via AJAX
                            currentVersion = data.config_version;
                        }
                    })
                    .catch(error => console.error('Error checking updates:', error));
            }

            // Check every 5 seconds
            setInterval(checkUpdates, 5000);
        </script>
    @endpush
@endsection