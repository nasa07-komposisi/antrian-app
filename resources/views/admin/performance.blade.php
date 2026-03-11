@extends('layouts.app')

@section('title', 'Laporan Performa Petugas')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0"><i class="bi bi-star-fill text-warning me-2"></i> Laporan Performa Petugas Loket</h2>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
        <div class="col-md-12 mt-2">
            <p class="text-muted mb-0">Statistik penyelesaian antrian per petugas (Satu baris per nama) berdasarkan periode
                waktu dan jenis layanan.</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light rounded shadow-sm">
            <form action="{{ route('admin.performance') }}" method="GET" class="row g-3" id="performance-filter-form">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-uppercase">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-uppercase">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-uppercase">Layanan</label>
                    <div class="dropdown">
                        <button class="btn btn-white border w-100 text-start dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            @if(count($selectedServiceIds) > 0)
                                {{ count($selectedServiceIds) }} Layanan Terpilih
                            @else
                                Semua Layanan
                            @endif
                        </button>
                        <div class="dropdown-menu p-3 shadow-lg border-0 w-100"
                            style="max-height: 300px; overflow-y: auto;">
                            @foreach($services as $service)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="service_ids[]"
                                        value="{{ $service->id }}" id="svc_{{ $service->id }}" {{ in_array($service->id, $selectedServiceIds) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="svc_{{ $service->id }}">
                                        {{ $service->name }} ({{ $service->prefix }})
                                    </label>
                                </div>
                            @endforeach
                            <hr class="dropdown-divider">
                            <div class="d-grid">
                                <button type="button" class="btn btn-primary btn-sm"
                                    onclick="document.getElementById('performance-filter-form').submit()">Terapkan
                                    filter</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white py-3" style="border-bottom: 2px solid var(--gold-accent);">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i> Rincian Kinerja Petugas</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-uppercase small fw-bold">Layanan Terkait</th>
                            <th class="py-3 text-uppercase small fw-bold">Nama Petugas</th>
                            <th class="text-center py-3 text-uppercase small fw-bold">Jumlah Dilayani</th>
                            <th class="text-center py-3 text-uppercase small fw-bold">Average (Menit)</th>
                            <th class="text-center py-3 text-uppercase small fw-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffPerformance as $perf)
                            <tr>
                                <td class="ps-4">
                                    <span
                                        class="badge bg-light text-dark border px-3 py-2 small fw-normal">{{ $perf['service_name'] }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($perf['user_name'], 0, 1)) }}
                                        </div>
                                        <div class="fw-bold fs-6">{{ $perf['user_name'] }}</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-success rounded-pill px-4 py-2 fs-6 fw-bold shadow-sm">{{ $perf['served'] }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="text-primary fw-bold fs-5 mb-0">
                                        {{ $perf['avg_minutes'] }} <small class="text-muted fs-6 fw-normal">Min</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @php
                                        $stars = 0;
                                        if ($perf['avg_minutes'] <= 3)
                                            $stars = 5;
                                        elseif ($perf['avg_minutes'] <= 6)
                                            $stars = 4;
                                        elseif ($perf['avg_minutes'] <= 10)
                                            $stars = 3;
                                        elseif ($perf['avg_minutes'] <= 15)
                                            $stars = 2;
                                        else
                                            $stars = 1;
                                    @endphp
                                    <div class="text-warning fs-5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $stars ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        @if($stars == 5) Sangat Cepat
                                        @elseif($stars == 4) Cepat
                                        @elseif($stars == 3) Normal
                                        @elseif($stars == 2) Cukup
                                        @else Lambat
                                        @endif
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x fs-1 opacity-25 d-block mb-3"></i>
                                    Tidak ada data performa ditemukan untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection