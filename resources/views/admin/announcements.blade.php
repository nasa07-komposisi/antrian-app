@extends('layouts.app')

@section('title', 'Kelola Pengumuman')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2>Kelola Pengumuman Publik</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pengumuman Baru
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark" style="border-bottom: 2px solid var(--gold-accent);">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>Isi Pengumuman</th>
                            <th style="width: 150px;">Status</th>
                            <th class="text-end pe-3" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $ann)
                            <tr>
                                <td class="ps-3">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold">{{ $ann->content }}</div>
                                    <small class="text-muted">Dibuat: {{ $ann->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ann->is_active ? 'success' : 'secondary' }}">
                                        {{ $ann->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal"
                                        data-bs-target="#editAnnouncementModal{{ $ann->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.announcements.delete', $ann->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editAnnouncementModal{{ $ann->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('admin.announcements.update', $ann->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Pengumuman</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Isi Pengumuman</label>
                                                    <textarea name="content" class="form-control" rows="3"
                                                        required>{{ $ann->content }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="is_active" class="form-select">
                                                        <option value="1" {{ $ann->is_active ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ !$ann->is_active ? 'selected' : '' }}>Non-Aktif
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada pengumuman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pengumuman Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Isi Pengumuman</label>
                            <textarea name="content" class="form-control" rows="4"
                                placeholder="Masukkan teks pengumuman di sini..." required></textarea>
                            <small class="text-muted">Teks ini akan muncul sebagai running text di tampilan publik.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pengumuman</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
@endsection