@extends('layouts.app')

@section('title', 'Manajemen Kuota Harian')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-uppercase fw-bold letter-spacing-1">
                        <i class="bi bi-shield-check me-2 text-warning"></i> Pengaturan Kuota Antrian
                    </h5>
                    <span class="badge bg-primary px-3 py-2">{{ \Carbon\Carbon::parse($today)->format('d F Y') }}</span>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Penting:</strong> Kuota harus diatur setiap hari. Antrian tidak dapat dicetak jika kuota
                        untuk hari ini belum ditentukan.
                    </div>

                    <form action="{{ route('admin.quotas.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-start px-4">Jenis Layanan</th>
                                        <th>Status Saat Ini</th>
                                        <th style="width: 200px;">Batas Kuota Hari Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $svc)
                                        <tr>
                                            <td class="text-start px-4">
                                                <div class="d-flex align-items-center">
                                                    <div style="width: 12px; height: 12px; background: {{ $svc->hex_color }}; border-radius: 50%;"
                                                        class="me-2"></div>
                                                    <span class="fw-bold">{{ $svc->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($svc->quota_date == $today)
                                                    <span class="badge bg-success px-3">Sudah Diatur: {{ $svc->daily_quota }}</span>
                                                @else
                                                    <span class="badge bg-danger px-3">Belum Diatur</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" name="quotas[{{ $svc->id }}]"
                                                        class="form-control text-center fw-bold"
                                                        value="{{ $svc->quota_date == $today ? $svc->daily_quota : '' }}"
                                                        min="1" placeholder="0" required>
                                                    <span class="input-group-text">Orang</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 d-flex justify-content-center gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                <i class="bi bi-save me-2"></i> SIMPAN & AKTIFKAN ANTRIAN
                            </button>
                        </div>
                    </form>

                    <form action="{{ route('admin.quotas.reset') }}" method="POST" class="mt-3"
                        onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh kuota harian? Antrian tidak akan bisa dicetak sampai kuota diatur kembali.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Kuota Hari Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection