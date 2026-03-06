@extends('layouts.app')

@section('title', 'Dashboard Loket')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body bg-dark text-white d-flex justify-content-between align-items-center rounded">
                    <div>
                        <h2 class="mb-0">{{ $counter->name }}</h2>
                        <div class="d-flex flex-column mt-1">
                            <div class="d-flex align-items-center">
                                <span class="text-info me-3">Jenis loket saat ini :
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
                            <small class="text-warning mt-1 fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> Jangan lupa klik tombol selesai atau pastikan tidak ada antrian yang dipanggil saat ini sebelum mengganti jenis layanan!!</small>
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
            <div class="card shadow-sm" style="border: 2px solid var(--navy-blue);">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Daftar Antrian Baru</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Klik salah satu layanan di bawah untuk mencetak nomor antrian baru</p>
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
            <div class="card mb-4 shadow-sm border-0" style="min-height: 300px;">
                <div class="card-header text-white text-center py-3 border-0"
                    style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <h5 class="mb-0 fw-bold">PANGGILAN SAAT INI</h5>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    @if($currentQueue)
                        <p class="text-muted mb-0 fw-bold">Nomor Antrian</p>
                        <div class="display-1 fw-bold mb-4" style="color: #1e3c72;">
                            {{ $currentQueue->queue_number }}
                        </div>
                        <div class="d-flex flex-nowrap justify-content-center gap-2">
                            <form action="{{ route('counter.finish', $currentQueue->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-lg w-100 py-3 text-dark fw-bold shadow-sm"
                                    style="background: linear-gradient(135deg, #ffca2c 0%, #ffc107 100%); border: none;">
                                    <i class="bi bi-check-circle me-1"></i> SELESAI
                                </button>
                            </form>
                            <form action="{{ route('counter.recall', $currentQueue->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-lg w-100 py-3 text-white fw-bold shadow-sm"
                                    style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">
                                    <i class="bi bi-megaphone-fill me-1 text-warning"></i> PANGGIL ULANG
                                </button>
                            </form>
                            <form action="{{ route('counter.next', $currentQueue->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-lg w-100 py-3 text-white fw-bold shadow-sm"
                                    style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">
                                    <i class="bi bi-chevron-double-right me-1 text-warning"></i> SELANJUTNYA
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
                            <button type="submit" class="btn btn-xl py-3 px-5 w-100 text-white fw-bold shadow"
                                style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none;">
                                <i class="bi bi-megaphone me-2 text-warning"></i> <strong>PANGGIL SELANJUTNYA</strong>
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
    <!-- Hidden iframe for silent printing -->
    <iframe id="print-iframe" style="display:none;"></iframe>

    @push('scripts')
        <script>
            // Auto Print Trigger via Hidden Iframe
            @if(session('print_queue_id'))
                (function () {
                    const printUrl = "{{ route('queue.print', session('print_queue_id')) }}";
                    const iframe = document.getElementById('print-iframe');

                    // Set the source to the print template
                    iframe.src = printUrl;

                    console.log("Printing started for ID:", "{{ session('print_queue_id') }}");
                })();
            @endif

            // Heartbeat to keep status active/online
            function sendHeartbeat() {
                fetch("{{ route('counter.heartbeat') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        window.location.reload();
                    }
                })
                .catch(err => console.error('Heartbeat failed:', err));
            }

            // Initial heartbeat
            sendHeartbeat();
            // Interval heartbeat setiap 15 detik
            setInterval(sendHeartbeat, 15000);

            // Auto-refresh polling
            let currentVersion = "{{ \Illuminate\Support\Facades\Storage::disk('local')->get('config_version.txt') ?? 0 }}";

            function checkUpdates() {
                fetch("{{ route('public.config-version') }}")
                    .then(response => {
                        if (response.status === 401) {
                            window.location.reload();
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.config_version && data.config_version !== currentVersion) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error('Error checking updates:', error));
            }

            // Check every 5 seconds
            setInterval(checkUpdates, 5000);
        </script>
    @endpush
@endsection