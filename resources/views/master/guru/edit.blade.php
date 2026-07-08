@extends('layouts.app')
@section('title', 'Edit ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-pen me-2" style="color:#6366f1"></i>Edit {{ $title }}</h1>
        </div>
        <a href="{{ route('master.guru.index') }}" class="btn btn-outline-secondary"><i
                class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-5">
            <div class="card bg-light">
                <div class="card-header"><i class="fas fa-ruler me-2" style="color:#6366f1"></i>Form Edit {{ $title }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('master.guru.update', $guru->id) }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $guru->personil->nama) }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Jenis Kelamin</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror" type="radio"
                                    name="jenis_kelamin" id="jk_l" value="L"
                                    {{ old('jenis_kelamin', $guru->personil->jenis_kelamin) == 'L' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jk_l">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('jenis_kelamin') is-invalid @enderror" type="radio"
                                    name="jenis_kelamin" id="jk_p" value="P"
                                    {{ old('jenis_kelamin', $guru->personil->jenis_kelamin) == 'P' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jk_p">Perempuan</label>
                            </div>
                            @error('jenis_kelamin')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="form-label">NIP</label>
                                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                    value="{{ old('nip', $guru->nip) }}" placeholder="">
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp"
                                    class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $guru->personil->no_hp) }}"
                                    placeholder="">
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $guru->personil->email) }}"
                                    placeholder="">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label">Jabatan</label>
                                <select name="jabatan_struktural_id"
                                    class="form-select @error('jabatan_struktural_id') is-invalid @enderror">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach ($jabatan as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('jabatan_struktural_id', $guru->jabatan_struktural_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama_jabatan }}</option>
                                    @endforeach
                                </select>
                                @error('jabatan_struktural_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="">{{ old('alamat', $guru->personil->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('status') is-invalid @enderror" type="radio"
                                    name="status" id="status_aktif" value="aktif"
                                    {{ old('status', $guru->personil->status) == 'aktif' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_aktif">Aktif</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('status') is-invalid @enderror" type="radio"
                                    name="status" id="status_nonaktif" value="nonaktif"
                                    {{ old('status', $guru->personil->status) == 'nonaktif' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_nonaktif">Non Aktif</label>
                            </div>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
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
