@php $isEdit = isset($suplier); @endphp
@extends('layouts.app')
@section('title', $isEdit ? 'Edit Suplier' : 'Tambah Suplier')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-{{ $isEdit ? 'pen' : 'plus-circle' }} me-2" style="color:#6366f1"></i>{{ $isEdit ? 'Edit' : 'Tambah' }} Suplier</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('master.suplier.index') }}">Suplier</a></li><li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Tambah' }}</li></ol>
    </div>
    <a href="{{ route('master.suplier.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
</div>
<div class="row justify-content-center"><div class="col-12 col-lg-7">
    <div class="card">
        <div class="card-header"><i class="fas fa-truck me-2" style="color:#6366f1"></i>Form Suplier</div>
        <div class="card-body">
            <form method="POST" action="{{ $isEdit ? route('master.suplier.update', $suplier->id) : route('master.suplier.store') }}">
                @csrf
                @if($isEdit) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama Suplier <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $suplier->nama ?? '') }}" required autofocus>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $suplier->alamat ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kota</label>
                        <input type="text" name="kota" class="form-control" value="{{ old('kota', $suplier->kota ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $suplier->telepon ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $suplier->email ?? '') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Kontak</label>
                        <input type="text" name="kontak" class="form-control" value="{{ old('kontak', $suplier->kontak ?? '') }}" placeholder="Person in charge">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $suplier->keterangan ?? '') }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-accent flex-fill"><i class="fas fa-save me-2"></i>Simpan</button>
                    <a href="{{ route('master.suplier.index') }}" class="btn btn-outline-secondary flex-fill"><i class="fas fa-times me-2"></i>Batal</a>
                </div>
            </form>
        </div>
    </div>
</div></div>
@endsection
