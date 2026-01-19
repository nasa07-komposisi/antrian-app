@extends('layouts.app')

@section('title', 'Manajemen Loket')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-dark fw-bold">Tambah Loket Baru</div>
                <div class="card-body">
                    <form action="{{ route('admin.counters.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Loket</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Loket 5" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Layanan Utama</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">-- Pilih Layanan --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }} ({{ $service->prefix }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Awal</label>
                            <select name="status" class="form-select">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info w-100 fw-bold">Simpan Loket</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Daftar Loket</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Nama Loket</th>
                                <th>Layanan Utama</th>
                                <th>Status Fisik</th>
                                <th class="text-end px-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($counters as $counter)
                                <tr>
                                    <td class="align-middle fw-bold px-3">{{ $counter->name }}</td>
                                    <td class="align-middle">
                                        <span class="badge" style="background-color: {{ $counter->service->hex_color }}">
                                            {{ $counter->service->name }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span
                                            class="badge {{ $counter->status == 'inactive' ? 'bg-secondary' : 'bg-success' }}">
                                            {{ strtoupper($counter->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end px-3 align-middle">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editCounter{{ $counter->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.counters.delete', $counter->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hapus loket ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editCounter{{ $counter->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.counters.update', $counter->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Loket: {{ $counter->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Loket</label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ $counter->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Layanan Utama</label>
                                                        <select name="service_id" class="form-select" required>
                                                            @foreach($services as $service)
                                                                <option value="{{ $service->id }}" {{ $counter->service_id == $service->id ? 'selected' : '' }}>
                                                                    {{ $service->name }} ({{ $service->prefix }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status Fisik</label>
                                                        <select name="status" class="form-select">
                                                            <option value="active" {{ $counter->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                                            <option value="inactive" {{ $counter->status == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-info fw-bold">Simpan Perubahan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection