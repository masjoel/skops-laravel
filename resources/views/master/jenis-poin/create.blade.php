@extends('layouts.app')
@section('title', 'Tambah ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-plus-circle me-2" style="color:#6366f1"></i>Tambah {{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('master.jenis-poin.index') }}">Jenis Poin</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </div>
        <a href="{{ route('master.jenis-poin.index') }}" class="btn btn-outline-secondary"><i
                class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="card bg-white">
                <div class="card-header"><i class="fas fa-tags me-2" style="color:#6366f1"></i>Form {{ $title }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('master.jenis-poin.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">No. Urut</label>
                                <input type="text" name="urut"
                                    class="form-control @error('urut') is-invalid @enderror" value="{{ old('urut', $noUrut) }}"
                                    autofocus>
                                @error('urut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kode <span class="text-danger">*</span></label>
                                <input type="text" name="kode"
                                    class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode') }}"
                                    required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Jenis <span class="text-danger">*</span></label>
                                <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                    <option value="reward" {{ old('jenis') == 'reward' ? 'selected' : '' }}>Reward</option>
                                    <option value="pelanggaran" {{ old('jenis') == 'pelanggaran' ? 'selected' : '' }}>
                                        Pelanggaran</option>
                                </select>
                                @error('jenis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Skor <span class="text-danger">*</span></label>
                                <input type="text" name="skor" class="form-control @error('skor') is-invalid @enderror"
                                    value="{{ old('skor') }}" required>
                                @error('skor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Tindakan</label>
                            <textarea name="tindakan" class="form-control" rows="2">{{ old('tindakan') }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-accent flex-fill"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                            <a href="{{ route('master.jenis-poin.index') }}" class="btn btn-outline-secondary flex-fill"><i
                                    class="fas fa-times me-2"></i>Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
