@php $isEdit = isset($anggota); @endphp
@extends('layouts.app')
@section('title', $isEdit ? 'Edit Anggota' : 'Tambah Anggota')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-{{ $isEdit ? 'pen' : 'user-plus' }} me-2" style="color:#6366f1"></i>{{ $isEdit ? 'Edit' : 'Tambah' }} Anggota</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('master.anggota.index') }}">Anggota</a></li><li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Tambah' }}</li></ol>
    </div>
    <a href="{{ route('master.anggota.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
</div>
<div class="row justify-content-center"><div class="col-12 col-lg-7">
    <div class="card">
        <div class="card-header"><i class="fas fa-user me-2" style="color:#6366f1"></i>Form Anggota / Customer</div>
        <div class="card-body">
            <form method="POST" action="{{ $isEdit ? route('master.anggota.update', $anggota->id) : route('master.anggota.store') }}">
                @csrf @if($isEdit) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $anggota->nama ?? '') }}" required autofocus>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $anggota->alamat ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kota</label>
                        <input type="text" name="kota" class="form-control" value="{{ old('kota', $anggota->kota ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $anggota->telepon ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $anggota->email ?? '') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Golongan</label>
                        <select name="gol" class="form-select">
                            @foreach(['Umum','Member','VIP'] as $g)
                                <option value="{{ $g }}" {{ old('gol', $anggota->gol ?? 'Umum') === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $anggota->keterangan ?? '') }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-accent flex-fill"><i class="fas fa-save me-2"></i>Simpan</button>
                    <a href="{{ route('master.anggota.index') }}" class="btn btn-outline-secondary flex-fill"><i class="fas fa-times me-2"></i>Batal</a>
                </div>
            </form>
        </div>
    </div>
</div></div>
@endsection
