@extends('layouts.app')
@section('title', 'Edit ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-pen me-2" style="color:#6366f1"></i>Edit {{ $title }}</h1>
        </div>
        <a href="{{ route('master.tahun-ajaran.index') }}" class="btn btn-outline-secondary"><i
                class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-5">
            <div class="card bg-light">
                <div class="card-header"><i class="fas fa-ruler me-2" style="color:#6366f1"></i>Form Edit
                    {{ $title }}
                </div>
                <div class="card-body">
                    @if ($tahunAjaran->is_aktif)
                        <div class="alert alert-warning" style="font-size:13px">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Ini tahun ajaran yang sedang aktif. Ubah dengan hati-hati.
                        </div>
                    @endif
                    <form method="POST" action="{{ route('master.tahun-ajaran.update', $tahunAjaran->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $tahunAjaran->nama) }}" placeholder="Contoh: 2026/2027" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-accent flex-fill"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                            <a href="{{ route('master.tahun-ajaran.index') }}"
                                class="btn btn-outline-secondary flex-fill"><i class="fas fa-times me-2"></i>Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection