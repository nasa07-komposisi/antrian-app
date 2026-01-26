@extends('layouts.app')

@section('title', 'Manajemen Layanan')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">Tambah Layanan Baru</div>
                <div class="card-body">
                    <form action="{{ route('admin.services.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Layanan</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Customer Service"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prefix Nomor</label>
                            <input type="text" name="prefix" class="form-control" placeholder="Contoh: A" maxlength="5"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Warna HEX Kustom</label>
                            <input type="color" name="hex_color" class="form-control form-control-color w-100"
                                value="#0d6efd" title="Pilih warna layanan">
                            <input type="hidden" name="color_class" value="primary">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Simpan Layanan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Daftar Jenis Layanan</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Prefix</th>
                                <th>Nama Layanan</th>
                                <th>Warna</th>
                                <th class="text-end px-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td class="align-middle fw-bold px-3">{{ $service->prefix }}</td>
                                    <td class="align-middle">{{ $service->name }}</td>
                                    <td class="align-middle text-center">
                                        <div
                                            style="width: 30px; height: 30px; background-color: {{ $service->hex_color }}; border-radius: 5px; display: inline-block; border: 1px solid #ddd;">
                                        </div>
                                    </td>
                                    <td class="text-end px-3 align-middle">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editService{{ $service->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.services.delete', $service->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hapus layanan ini?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editService{{ $service->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Layanan: {{ $service->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Layanan</label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ $service->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Prefix Nomor</label>
                                                        <input type="text" name="prefix" class="form-control"
                                                            value="{{ $service->prefix }}" maxlength="5" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Warna HEX Kustom</label>
                                                        <input type="color" name="hex_color"
                                                            class="form-control form-control-color w-100"
                                                            value="{{ $service->hex_color }}" title="Pilih warna layanan">
                                                        <input type="hidden" name="color_class" value="primary">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea name="description" class="form-control"
                                                            rows="3">{{ $service->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
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