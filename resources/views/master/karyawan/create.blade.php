@php $isEdit = isset($karyawan); @endphp
@extends('layouts.app')
@section('title', $isEdit ? 'Edit Karyawan' : 'Tambah Karyawan')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-{{ $isEdit ? 'pen' : 'user-plus' }} me-2" style="color:#6366f1"></i>{{ $isEdit ? 'Edit' : 'Tambah' }} Karyawan</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('master.karyawan.index') }}">Karyawan</a></li><li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Tambah' }}</li></ol>
    </div>
    <a href="{{ route('master.karyawan.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
</div>
<form method="POST" action="{{ $isEdit ? route('master.karyawan.update', $karyawan->id) : route('master.karyawan.store') }}" enctype="multipart/form-data">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header"><i class="fas fa-id-badge me-2" style="color:#6366f1"></i>Data Karyawan</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $karyawan->nama ?? '') }}" required autofocus>
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $karyawan->jabatan ?? '') }}" placeholder="Contoh: Kasir, Admin">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $karyawan->telepon ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $karyawan->alamat ?? '') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gaji Pokok</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted);font-size:12px">Rp</span>
                                <input type="number" name="gaji_pokok" class="form-control" value="{{ old('gaji_pokok', $karyawan->gaji_pokok ?? 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tgl_masuk" class="form-control" value="{{ old('tgl_masuk', $karyawan->tgl_masuk ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach(['Aktif','Nonaktif'] as $s)
                                    <option value="{{ $s }}" {{ old('status', $karyawan->status ?? 'Aktif') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-image me-2" style="color:#6366f1"></i>Foto Karyawan</div>
                <div class="card-body text-center">
                    <div style="width:100%;height:180px;border:2px dashed var(--border-color);border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;margin-bottom:12px" onclick="document.getElementById('photo-input').click()">
                        @if($isEdit && $karyawan->photo)
                            <img id="preview-img" src="{{ asset('storage/'.$karyawan->photo) }}" style="max-width:100%;max-height:176px;border-radius:8px;object-fit:contain">
                        @else
                            <div id="preview-placeholder"><i class="fas fa-user-circle fa-3x mb-2" style="color:#6366f1;opacity:.4;display:block"></i><div style="font-size:12px;color:var(--text-muted)">Klik untuk upload foto</div></div>
                            <img id="preview-img" src="" style="display:none;max-width:100%;max-height:176px;border-radius:8px;object-fit:contain">
                        @endif
                    </div>
                    <input type="file" id="photo-input" name="photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="document.getElementById('photo-input').click()"><i class="fas fa-upload me-2"></i>Pilih Foto</button>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-accent w-100 mb-2"><i class="fas fa-save me-2"></i>Simpan</button>
                    <a href="{{ route('master.karyawan.index') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-times me-2"></i>Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>
@push('scripts')
<script>
function previewPhoto(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const ph = document.getElementById('preview-placeholder');
        if (ph) ph.style.display = 'none';
        const img = document.getElementById('preview-img');
        img.src = e.target.result; img.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
@endsection
