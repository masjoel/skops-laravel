@php $isEdit = isset($user); @endphp
@extends('layouts.app')
@section('title', $isEdit ? 'Edit User' : 'Tambah User')
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-{{ $isEdit ? 'user-pen' : 'user-plus' }} me-2"
                    style="color:#6366f1"></i>{{ $isEdit ? 'Edit' : 'Tambah' }} User</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('seting.index') }}">Seting</a></li>
                <li class="breadcrumb-item"><a href="{{ route('seting.user.index') }}">User</a></li>
                <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Tambah' }}</li>
            </ol>
        </div>
        <a href="{{ route('seting.user.index') }}" class="btn btn-outline-secondary"><i
                class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header"><i class="fas fa-user-cog me-2" style="color:#6366f1"></i>Form
                    {{ $isEdit ? 'Edit' : 'Tambah' }} User</div>
                <div class="card-body">
                    <form method="POST"
                        action="{{ $isEdit ? route('seting.user.update', $user->idx ?? $user->id) : route('seting.user.store') }}">
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name ?? '') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username', $user->username ?? '') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email ?? '') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password {{ $isEdit ? '(kosongkan jika tidak diganti)' : '' }}
                                    <span class="text-danger">{{ $isEdit ? '' : '*' }}</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    {{ $isEdit ? '' : 'required' }} autocomplete="new-password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Level / Hak Akses <span class="text-danger">*</span></label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    @foreach ($roles as $lvl)
                                        <option value="{{ $lvl }}"
                                            {{ old('role', $user->role ?? '') === $lvl ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $lvl)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach ([0, 1] as $s)
                                        <option value="{{ $s }}"
                                            {{ old('status', $user->status ?? 0) === $s ? 'selected' : '' }}>
                                            {{ $s == 1 ? 'Aktif' : 'Non Aktif' }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if (!$isEdit)
                            <div class="alert mt-3"
                                style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);border-radius:8px;padding:12px 16px;font-size:13px">
                                <i class="fas fa-shield-halved me-2" style="color:#6366f1"></i>
                                <strong>Keamanan:</strong> Gunakan password minimal 6 karakter yang kuat.
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-accent flex-fill"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                            <a href="{{ route('seting.user.index') }}" class="btn btn-outline-secondary flex-fill"><i
                                    class="fas fa-times me-2"></i>Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
