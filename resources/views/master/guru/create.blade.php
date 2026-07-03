@extends('layouts.app')
@section('title', 'Tambah ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-pen me-2" style="color:#6366f1"></i>Tambah {{ $title }}</h1>
        </div>
        <a href="{{ route('master.guru.index') }}" class="btn btn-outline-secondary"><i
                class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-5">
            <div class="card bg-light">
                <div class="card-header"><i class="fas fa-ruler me-2" style="color:#6366f1"></i>Form Tambah
                    {{ $title }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('master.guru.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama') }}" placeholder="Contoh: John Doe" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIP <span class="text-danger">*</span></label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                value="{{ old('nip') }}" placeholder="Contoh: 123456789" required>
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>
                                    Aktif</option>
                                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>
                                    Non Aktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-accent flex-fill"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                            <a href="{{ route('master.guru.index') }}" class="btn btn-outline-secondary flex-fill"><i
                                    class="fas fa-times me-2"></i>Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
