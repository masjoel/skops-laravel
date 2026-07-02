@extends('layouts.app')
@section('title', 'Tambah Barang')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-plus-circle me-2" style="color:#6366f1"></i>Tambah Barang</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('master.barang.index') }}">Barang</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('master.barang.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<form method="POST" action="{{ route('master.barang.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        {{-- Kolom Kiri --}}
        <div class="col-12 col-lg-8">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-info-circle me-2" style="color:#6366f1"></i>Informasi Barang</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kdbrg" class="form-control @error('kdbrg') is-invalid @enderror"
                                   value="{{ old('kdbrg') }}" placeholder="Contoh: BRG001" required>
                            @error('kdbrg')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="namabrg" class="form-control @error('namabrg') is-invalid @enderror"
                                   value="{{ old('namabrg') }}" placeholder="Nama lengkap barang" required>
                            @error('namabrg')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}" placeholder="Scan barcode">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis</label>
                            <select name="jenis" class="form-select">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="produk" {{ old('jenis') === 'produk' ? 'selected' : '' }}>Produk</option>
                                <option value="bahan" {{ old('jenis') === 'bahan' ? 'selected' : '' }}>Bahan Baku</option>
                                <option value="jasa" {{ old('jenis') === 'jasa' ? 'selected' : '' }}>Jasa</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}" {{ old('kategori') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan" class="form-select @error('satuan') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @foreach($satuans as $sat)
                                    <option value="{{ $sat->id }}" {{ old('satuan') == $sat->id ? 'selected' : '' }}>{{ $sat->nama }}</option>
                                @endforeach
                            </select>
                            @error('satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lokasi</label>
                            <select name="lokasi" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach($lokasis as $lok)
                                    <option value="{{ $lok->id }}" {{ old('lokasi') == $lok->id ? 'selected' : '' }}>{{ $lok->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Suplier</label>
                            <select name="suplier" class="form-select">
                                <option value="">-- Pilih --</option>
                                @foreach($supliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('suplier') == $sup->id ? 'selected' : '' }}>{{ $sup->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan...">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Harga --}}
            <div class="card">
                <div class="card-header"><i class="fas fa-tag me-2" style="color:#6366f1"></i>Harga & Stok</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted);font-size:12px">Rp</span>
                                <input type="number" name="hrg_beli" class="form-control @error('hrg_beli') is-invalid @enderror"
                                       value="{{ old('hrg_beli', 0) }}" min="0" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Jual 1 <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted);font-size:12px">Rp</span>
                                <input type="number" name="hrg1" class="form-control @error('hrg1') is-invalid @enderror"
                                       value="{{ old('hrg1', 0) }}" min="0" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Jual 2</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted);font-size:12px">Rp</span>
                                <input type="number" name="hrg2" class="form-control" value="{{ old('hrg2', 0) }}" min="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Jual 3</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted);font-size:12px">Rp</span>
                                <input type="number" name="hrg3" class="form-control" value="{{ old('hrg3', 0) }}" min="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror"
                                   value="{{ old('stok', 0) }}" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="stok_kritis" class="form-control @error('stok_kritis') is-invalid @enderror"
                                   value="{{ old('stok_kritis', 5) }}" min="0" required>
                            <div class="form-text">Notifikasi stok kritis jika stok ≤ nilai ini</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Foto --}}
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-image me-2" style="color:#6366f1"></i>Foto Barang</div>
                <div class="card-body text-center">
                    <div id="photo-preview" style="width:100%;height:200px;border:2px dashed var(--border-color);border-radius:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:border-color .2s;background:rgba(99,102,241,.03);margin-bottom:12px"
                         onclick="document.getElementById('photo-input').click()">
                        <div id="preview-placeholder">
                            <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:#6366f1;opacity:.5;display:block"></i>
                            <div style="font-size:12px;color:var(--text-muted)">Klik untuk upload foto</div>
                            <div style="font-size:11px;color:var(--text-muted)">JPG, PNG, max 2MB</div>
                        </div>
                        <img id="preview-img" src="" alt="Preview" style="display:none;max-width:100%;max-height:196px;border-radius:8px;object-fit:contain">
                    </div>
                    <input type="file" id="photo-input" name="photo" accept="image/*" class="d-none" onchange="previewPhoto(this)">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="document.getElementById('photo-input').click()">
                        <i class="fas fa-upload me-2"></i>Pilih Foto
                    </button>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" class="btn btn-accent w-100 mb-2">
                        <i class="fas fa-save me-2"></i>Simpan Barang
                    </button>
                    <a href="{{ route('master.barang.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function previewPhoto(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('preview-placeholder').style.display = 'none';
        const img = document.getElementById('preview-img');
        img.src = e.target.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>
@endpush
@endsection
