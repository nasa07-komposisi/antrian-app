@extends('layouts.app')

@section('title', 'Dashboard Loket')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body bg-dark text-white d-flex justify-content-between align-items-center rounded">
                    <div>
                        <h2 class="mb-0">{{ $counter->name }}</h2>
                        <div class="d-flex align-items-center mt-1">
                            <span class="text-info me-3">Melayani Utama:
                                <strong>{{ $counter->service->name }}</strong></span>
                            <!-- Ganti Layanan Dropdown -->
                            <form action="{{ route('counter.update-service') }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <select name="service_id" class="form-select form-select-sm" style="width: auto;"
                                    onchange="this.form.submit()">
                                    @foreach($services as $s)
                                        <option value="{{ $s->id }}" {{ $counter->service_id == $s->id ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="mb-1">Petugas: <strong>{{ Auth::user()->name }}</strong></div>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger">Logout / Nonaktifkan Loket</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendaftaran Antrian Baru (Multiple Services) -->
        <div class="col-md-12 mb-4">
            <div class="card border-primary shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Daftar Antrian Baru</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Klik salah satu layanan di bawah untuk mencetak nomor antrian baru bagi pelanggan.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($services as $service)
                            <form action="{{ route('queue.register', $service->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-lg px-4">
                                    <i class="bi bi-plus-circle me-1"></i> {{ $service->name }} ({{ $service->prefix }})
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4 shadow-sm" style="min-height: 300px;">
                <div class="card-header bg-secondary text-white text-center py-3">
                    <h5 class="mb-0">PANGGILAN SAAT INI</h5>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    @if($currentQueue)
                        <p class="text-muted mb-0">Nomor Antrian</p>
                        <div class="display-1 fw-bold text-primary mb-4">
                            {{ $currentQueue->queue_number }}
                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <form action="{{ route('counter.finish', $currentQueue->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100 py-3">
                                    <i class="bi bi-check-circle me-1"></i> SELESAI
                                </button>
                            </form>
                            <form action="{{ route('counter.next', $currentQueue->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 shadow">
                                    <i class="bi bi-chevron-double-right me-1"></i> ANTRIAN SELANJUTNYA
                                </button>
                            </form>
                        </div>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <form action="{{ route('counter.recall', $currentQueue->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-info btn-sm w-100 py-2">
                                    <i class="bi bi-megaphone-fill me-1"></i> PANGGIL ULANG (RECALL)
                                </button>
                            </form>
                            <form action="{{ route('counter.skip', $currentQueue->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm w-100 py-2 text-dark">
                                    <i class="bi bi-skip-forward me-1"></i> LEWATI
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-muted mb-4">
                            <i class="bi bi-person-dash display-4 d-block mb-3"></i>
                            <h4>Tidak ada antrian aktif</h4>
                        </div>
                        <form action="{{ route('counter.call-next') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-xl py-3 px-5 w-100 shadow">
                                <i class="bi bi-megaphone me-2"></i> <strong>PANGGIL SELANJUTNYA</strong>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm" style="min-height: 300px;">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Daftar Tunggu - {{ $counter->service->name }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Nomor Antrian</th>
                                    <th>Waktu Daftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $waitingQueues = \App\Models\Queue::where('service_id', $counter->service_id)
                                        ->where('status', 'waiting')
                                        ->orderBy('number', 'asc')
                                        ->get();
                                @endphp
                                @forelse($waitingQueues as $q)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                                        <td><span class="badge bg-info text-dark fs-6">{{ $q->queue_number }}</span></td>
                                        <td>{{ $q->created_at->format('H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox d-block display-6 mb-2"></i>
                                            Tidak ada antrian menunggu untuk layanan ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            // Auto Print Trigger
            @if(session('print_queue_id'))
                (function () {
                    const printUrl = "{{ route('queue.print', session('print_queue_id')) }}";
                    const printWindow = window.open(printUrl, 'Cetak Antrian', 'width=300,height=400');
                    if (printWindow) {
                        // The child window handles printing and closing itself
                        console.log("Print window opened for ID:", "{{ session('print_queue_id') }}");
                    } else {
                        alert("Gagal membuka jendela cetak. Pastikan pop-up diperbolehkan di browser ini.");
                    }
                })();
            @endif
        </script>
    @endpush
@endsection