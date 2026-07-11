@extends('layouts.app')
@section('title', 'Data Sekolah')
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-building me-2" style="color:#6366f1"></i>Data Sekolah</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('seting.index') }}">Seting</a></li>
                <li class="breadcrumb-item active">Sekolah</li>
            </ol>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header"><i class="fas fa-pen me-2" style="color:#6366f1"></i>Edit Data Sekolah</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('seting.sekolah.update') }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                                <input type="text" name="nama_client"
                                    class="form-control @error('nama_client') is-invalid @enderror"
                                    value="{{ old('nama_client', $sekolah?->nama_client) }}" required autofocus>
                                @error('nama_client')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat_client" class="form-control" rows="2">{{ old('alamat_client', $sekolah?->alamat_client) }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kota</label>
                                <input type="text" name="kota" class="form-control"
                                    value="{{ old('kota', $sekolah?->kota ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Telpon</label>
                                <input type="text" name="telpon" class="form-control"
                                    value="{{ old('telpon', $sekolah?->telpon) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $sekolah?->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">NPSN <span class="text-danger">*</span></label>
                                <input type="text" name="npsn" class="form-control"
                                value="{{ old('npsn', $sekolah?->npsn ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah max. baris data import excel <span class="text-danger">*</span></label>
                                <input type="number" name="jdigit" class="form-control"
                                    value="{{ old('jdigit', $sekolah?->jdigit ?? 1) }}" min="1">
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
                <div class="card-header"><i class="fas fa-image me-2" style="color:#6366f1"></i>Logo Sekolah</div>
                <div class="card-body text-center">
                    <div style="width:100%;height:160px;border:2px dashed var(--border-color);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;cursor:pointer;overflow:hidden"
                        onclick="document.getElementById('logo-input').click()">
                        @if ($sekolah?->logo)
                            <img id="preview-img" src="{{ asset('storage/' . $sekolah->logo) }}"
                                style="max-width:100%;max-height:156px;object-fit:contain">
                        @else
                            <div id="preview-placeholder">
                                <i class="fas fa-building fa-2x mb-2" style="color:#6366f1;opacity:.4;display:block"></i>
                                <div style="font-size:12px;color:var(--text-muted)">Klik untuk upload logo</div>
                            </div>
                            <img id="preview-img" src=""
                                style="display:none;max-width:100%;max-height:156px;object-fit:contain">
                        @endif
                    </div>

                    <form method="POST" action="{{ route('seting.sekolah.update') }}" enctype="multipart/form-data"
                        id="logo-form">
                        @csrf @method('PUT')
                        {{-- hidden fields to preserve existing data --}}
                        <input type="hidden" name="nama_client" value="{{ $sekolah?->nama_client ?? '' }}">
                        <input type="hidden" name="alamat_client" value="{{ $sekolah?->alamat_client ?? '' }}">
                        <input type="hidden" name="kota" value="{{ $sekolah?->kota ?? '' }}">
                        <input type="hidden" name="telpon" value="{{ $sekolah?->telpon ?? '' }}">
                        <input type="hidden" name="email" value="{{ $sekolah?->email ?? '' }}">
                        <input type="hidden" name="npsn" value="{{ $sekolah?->npsn ?? '' }}">
                        <input type="hidden" name="jdigit" value="{{ $sekolah?->jdigit ?? 1 }}">
                        <input type="file" id="logo-input" name="logo" accept="image/*" class="d-none"
                            onchange="submitLogo(this)">
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                            onclick="document.getElementById('logo-input').click()">
                            <i class="fas fa-upload me-2"></i>Ganti Logo
                        </button>
                    </form>
                </div>
            </div>
            {{-- <div class="card mt-3">
                <div class="card-header"><i class="fas fa-circle-info me-2" style="color:#6366f1"></i>Info Sistem</div>
                <div class="card-body" style="font-size:13px">
                    @php $infos = [['Versi Laravel', app()->version()], ['PHP', phpversion()], ['Timezone', config('app.timezone')], ['Environment', app()->environment()]]; @endphp
                    @foreach ($infos as [$label, $val])
                        <div class="d-flex justify-content-between py-1"
                            style="border-bottom:1px solid var(--border-color)">
                            <span style="color:var(--text-muted)">{{ $label }}</span>
                            <span style="font-weight:500">{{ $val }}</span>
                        </div>
                    @endforeach
                </div>
            </div> --}}
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
                    img.src = e.target.result;
                    img.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
                document.getElementById('logo-form').submit();
            }
        </script>
    @endpush
@endsection
