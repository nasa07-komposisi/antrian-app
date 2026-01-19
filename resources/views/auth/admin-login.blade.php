@extends('layouts.app')

@section('title', 'Login Admin')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-4 col-lg-3">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white text-center py-4">
                    <i class="bi bi-shield-lock display-4 mb-2 d-block"></i>
                    <h4 class="mb-0 fw-bold">Login Admin</h4>
                    <p class="small text-secondary mb-0">Hanya untuk pengelola sistem</p>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.login.post') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Email Admin</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="email admin" required
                                    autofocus>
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-key"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark btn-lg w-100 py-3 shadow">
                            <i class="bi bi-box-arrow-in-right me-2"></i> <strong>LOGIN</strong>
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('public.index') }}" class="text-decoration-none text-muted small">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Tampilan Publik
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection