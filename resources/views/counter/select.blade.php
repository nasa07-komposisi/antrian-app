@extends('layouts.app')

@section('title', 'Pilih Loket')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-dark text-white text-center py-3">
                    <h4 class="mb-0">PILIH LOKET TUGAS</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-center text-muted">Selamat datang, <strong>{{ Auth::user()->name }}</strong>. Silakan
                        pilih loket yang akan Anda aktifkan.</p>

                    @if($availableCounters->isEmpty())
                        <div class="alert alert-warning text-center">
                            Maaf, saat ini tidak ada loket yang tersedia atau semua loket sudah digunakan.
                        </div>
                    @else
                        <form action="{{ route('counter.select.post') }}" method="POST">
                            @csrf
                            <div class="list-group mb-4">
                                @foreach($availableCounters as $counter)
                                    <label
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-3" type="radio" name="counter_id"
                                                value="{{ $counter->id }}" required>
                                            <div>
                                                <h5 class="mb-0">{{ $counter->name }}</h5>
                                                <small class="text-muted">Melayani: {{ $counter->service->name }}</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-success">Tersedia</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Aktifkan Loket</button>
                            </div>
                        </form>
                    @endif

                    <div class="mt-3 text-center">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link text-danger">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection