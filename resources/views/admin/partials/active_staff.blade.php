@forelse($activeCounters as $ac)
    @php
        $isRecentlyActive = $ac->last_seen_at && $ac->last_seen_at->diffInSeconds(now()) < 40;
    @endphp
    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
        <div>
            <div class="fw-bold">{{ $ac->occupiedBy->name }}</div>
            <small class="text-muted"><i class="bi bi-display me-1"></i> {{ $ac->name }} - {{ $ac->service->name }}</small>
        </div>
        @if($isRecentlyActive)
            <span class="badge bg-success rounded-pill">ONLINE</span>
        @else
            <span class="badge bg-secondary rounded-pill">OFFLINE</span>
        @endif
    </li>
@empty
    <li class="list-group-item text-center py-4 text-muted small">Tidak ada petugas yang sedang online.</li>
@endforelse