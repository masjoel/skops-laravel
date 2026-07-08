@extends('layouts.app')
@section('title', 'Tambah ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-pen me-2" style="color:#6366f1"></i>Tambah {{ $title }}</h1>
        </div>
        <a href="{{ route('master.jabatan.index') }}" class="btn btn-outline-secondary"><i
                class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-5">
            <div class="card bg-light">
                <div class="card-header"><i class="fas fa-ruler me-2" style="color:#6366f1"></i>Form Tambah
                    {{ $title }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('master.jabatan.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Jabatan<span class="text-danger">*</span></label>
                            <input type="text" name="nama_jabatan" class="form-control @error('nama_jabatan') is-invalid @enderror"
                                value="{{ old('nama_jabatan') }}" required>
                            @error('nama_jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-control @error('kategori') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoriOptions as $kategori)
                                    <option value="{{ $kategori }}" {{ old('kategori') == $kategori ? 'selected' : '' }}>
                                        {{ $kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-accent flex-fill"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                            <a href="{{ route('master.jabatan.index') }}" class="btn btn-outline-secondary flex-fill"><i
                                    class="fas fa-times me-2"></i>Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
