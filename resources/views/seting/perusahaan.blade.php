@extends('layouts.app')
@section('title', 'Data Perusahaan')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-building me-2" style="color:#6366f1"></i>Data Perusahaan</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('seting.index') }}">Seting</a></li><li class="breadcrumb-item active">Perusahaan</li></ol>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-pen me-2" style="color:#6366f1"></i>Edit Data Perusahaan</div>
            <div class="card-body">
                <form method="POST" action="{{ route('seting.perusahaan.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Perusahaan / Toko <span class="text-danger">*</span></label>
                            <input type="text" name="NamaClient" class="form-control @error('NamaClient') is-invalid @enderror"
                                   value="{{ old('NamaClient', $perusahaan?->NamaClient) }}" required autofocus>
                            @error('NamaClient')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="Alamat" class="form-control" rows="2">{{ old('Alamat', $perusahaan?->Alamat) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kota</label>
                            <input type="text" name="Kota" class="form-control" value="{{ old('Kota', $perusahaan?->Kota ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="Telp" class="form-control" value="{{ old('Telp', $perusahaan?->Telp) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="Email" class="form-control @error('Email') is-invalid @enderror"
                                   value="{{ old('Email', $perusahaan?->Email) }}">
                            @error('Email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="url" name="Website" class="form-control" value="{{ old('Website', $perusahaan?->Website ?? '') }}" placeholder="https://...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jumlah Digit Desimal</label>
                            <input type="number" name="jdigit" class="form-control" value="{{ old('jdigit', $perusahaan?->jdigit ?? 0) }}" min="0" max="5">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">NPWP</label>
                            <input type="text" name="NPWP" class="form-control" value="{{ old('NPWP', $perusahaan?->NPWP ?? '') }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-accent px-5">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-image me-2" style="color:#6366f1"></i>Logo Perusahaan</div>
            <div class="card-body text-center">
                <div style="width:100%;height:160px;border:2px dashed var(--border-color);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;cursor:pointer;overflow:hidden"
                     onclick="document.getElementById('logo-input').click()">
                    @if($perusahaan?->Logo)
                        <img id="preview-img" src="{{ asset('storage/'.$perusahaan->Logo) }}" style="max-width:100%;max-height:156px;object-fit:contain">
                    @else
                        <div id="preview-placeholder">
                            <i class="fas fa-building fa-2x mb-2" style="color:#6366f1;opacity:.4;display:block"></i>
                            <div style="font-size:12px;color:var(--text-muted)">Klik untuk upload logo</div>
                        </div>
                        <img id="preview-img" src="" style="display:none;max-width:100%;max-height:156px;object-fit:contain">
                    @endif
                </div>

                <form method="POST" action="{{ route('seting.perusahaan.update') }}" enctype="multipart/form-data" id="logo-form">
                    @csrf @method('PUT')
                    {{-- hidden fields to preserve existing data --}}
                    <input type="hidden" name="NamaClient" value="{{ $perusahaan?->NamaClient ?? 'Perusahaan' }}">
                    <input type="file" id="logo-input" name="Logo" accept="image/*" class="d-none" onchange="submitLogo(this)">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="document.getElementById('logo-input').click()">
                        <i class="fas fa-upload me-2"></i>Ganti Logo
                    </button>
                </form>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header"><i class="fas fa-circle-info me-2" style="color:#6366f1"></i>Info Sistem</div>
            <div class="card-body" style="font-size:13px">
                @php $infos = [['Versi Laravel', app()->version()], ['PHP', phpversion()], ['Timezone', config('app.timezone')], ['Environment', app()->environment()]]; @endphp
                @foreach($infos as [$label, $val])
                <div class="d-flex justify-content-between py-1" style="border-bottom:1px solid var(--border-color)">
                    <span style="color:var(--text-muted)">{{ $label }}</span>
                    <span style="font-weight:500">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function submitLogo(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const ph = document.getElementById('preview-placeholder');
        if (ph) ph.style.display = 'none';
        const img = document.getElementById('preview-img');
        img.src = e.target.result; img.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
    document.getElementById('logo-form').submit();
}
</script>
@endpush
@endsection
