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

    <!-- Filter & Statistics -->
    <div class="row g-4 mb-5">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body bg-light rounded shadow-sm">
                    <form action="{{ route('admin.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter me-1"></i> Filter Statistik
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Global Summary -->
        <div class="col-6 col-md-6">
            <div class="card bg-dark text-white shadow-sm border-0 h-100 border-start border-warning border-5">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h6 class="text-uppercase small opacity-75 mb-0">Total Antrian Diambil</h6>
                        <h1 class="fw-bold mb-0 display-4">{{ $totalQueues }}</h1>
                    </div>
                    <i class="bi bi-ticket-perforated fs-1 text-warning opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6">
            <div class="card bg-dark text-white shadow-sm border-0 h-100 border-start border-success border-5">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h6 class="text-uppercase small opacity-75 mb-0">Total Terlayani (Selesai)</h6>
                        <h1 class="fw-bold mb-0 display-4">{{ $finishedQueues }}</h1>
                    </div>
                    <i class="bi bi-check2-circle fs-1 text-success opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Stats Per Service (Horizontal Adaptive Row) -->
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

        <!-- Master Data Summary (Ringkasan Sistem) dengan Ikon -->
        <div class="col-12 mt-5">
            <h5 class="text-secondary text-uppercase letter-spacing-1 mb-3 border-bottom pb-2">Ringkasan Sistem & Master
                Data</h5>
            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="card bg-primary text-white shadow-sm border-0 h-100">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-gear-fill display-5 mb-2"></i>
                            <h6 class="mb-1 small text-uppercase">Total Layanan</h6>
                            <h2 class="fw-bold mb-0">{{ $serviceCount }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card bg-success text-white shadow-sm border-0 h-100">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-display display-5 mb-2"></i>
                            <h6 class="mb-1 small text-uppercase">Total Loket</h6>
                            <h2 class="fw-bold mb-0">{{ $counterCount }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card bg-info text-white shadow-sm border-0 h-100">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-people-fill display-5 mb-2"></i>
                            <h6 class="mb-1 small text-uppercase">User Petugas</h6>
                            <h2 class="fw-bold mb-0">{{ $userCount }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card bg-warning text-dark shadow-sm border-0 h-100">
                        <div class="card-body text-center p-3">
                            <i class="bi bi-activity display-5 mb-2"></i>
                            <h6 class="mb-1 small text-uppercase">Loket Aktif</h6>
                            <h2 class="fw-bold mb-0">{{ $activeCounterCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Menus -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i> Menu Pengaturan Master</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.quotas') }}"
                            class="list-group-item list-group-item-action py-3 fw-bold bg-light">
                            <i class="bi bi-shield-check me-2 text-warning"></i> Atur Kuota Antrian Hari Ini
                        </a>
                        <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action py-3">
                            <i class="bi bi-person-plus me-2 text-primary"></i> Manajemen Petugas Baru
                        </a>
                        <a href="{{ route('admin.services') }}" class="list-group-item list-group-item-action py-3">
                            <i class="bi bi-plus-square me-2 text-success"></i> Kelola Jenis Layanan
                        </a>
                        <a href="{{ route('admin.counters') }}" class="list-group-item list-group-item-action py-3">
                            <i class="bi bi-pcn-vertical me-2 text-info"></i> Konfigurasi Penambahan Loket
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm bg-light border-0 h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center text-center">
                    <i class="bi bi-info-circle display-4 text-secondary mb-3"></i>
                    <h5>Informasi Proyek</h5>
                    <p class="text-muted mb-4">ANT APP - Sistem Manajemen Antrian v1.1</p>
                    <p class="small text-secondary px-3">Panel ini dipisahkan menjadi dua bagian utama:
                        <strong>Statistik Performa</strong> untuk evaluasi antrian, dan
                        <strong>Ringkasan Sistem</strong> untuk manajemen data master.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection