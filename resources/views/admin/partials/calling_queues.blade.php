@forelse($callingQueues as $q)
    <tr>
        <td class="ps-3"><span class="badge bg-info text-dark fs-6">{{ $q->queue_number }}</span></td>
        <td><small class="fw-bold">{{ $q->service->name }}</small></td>
        <td>
            <div>{{ $q->counter->name }}</div>
            <small class="text-muted"><i
                    class="bi bi-person me-1"></i>{{ $q->counter->occupiedBy->name ?? 'System' }}</small>
        </td>
        <td class="text-end pe-3">
            <form action="{{ route('admin.queues.force-finish', $q->id) }}" method="POST"
                onsubmit="return confirm('Selesaikan antrian ini secara paksa?')">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-check-all me-1"></i> Selesaikan
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="text-center py-4 text-muted small">Tidak ada antrian yang sedang dipanggil saat ini.</td>
    </tr>
@endforelse