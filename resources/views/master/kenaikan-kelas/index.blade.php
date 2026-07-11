@extends('layouts.app')
@section('title', 'Kenaikan Kelas')
@section('content')

<div class="page-header">
    <div>
        <h1><i class="fas fa-graduation-cap me-2" style="color:#6366f1"></i>Kenaikan Kelas</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Kenaikan Kelas</li>
        </ol>
    </div>
</div>

{{-- Info prasyarat --}}
<div class="alert d-flex align-items-start gap-3 mb-4"
    style="border-radius:10px;border:none;background:rgba(99,102,241,.1);color:var(--text-main)">
    <i class="fas fa-circle-info fa-lg mt-1" style="color:#6366f1;flex-shrink:0"></i>
    <div style="font-size:13.5px">
        <strong>Prasyarat:</strong> Tahun ajaran tujuan (misal <em>2026/2027</em>) harus sudah dibuat terlebih dahulu
        melalui halaman <a href="{{ route('master.tahun-ajaran.index') }}" style="color:#6366f1">Tahun Ajaran</a>
        sebelum melakukan proses kenaikan kelas.
    </div>
</div>

{{-- Form Filter --}}
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-filter me-2" style="color:#6366f1"></i>Pilih Kelas & Tahun Ajaran Tujuan</div>
    <div class="card-body">
        <form method="GET" action="{{ route('master.kenaikan-kelas.index') }}" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Kelas Asal <small class="text-muted">(tahun ajaran aktif: <strong>{{ $tahunAjaranAktif?->nama ?? '-' }}</strong>)</small></label>
                    <select name="kelas_id" class="form-select" required onchange="this.form.submit()">
                        <option value="">— Pilih Kelas Asal —</option>
                        @foreach ($semuaKelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun Ajaran Tujuan</label>
                    <select name="tahun_ajaran_tujuan_id" class="form-select" required onchange="this.form.submit()">
                        <option value="">— Pilih Tahun Ajaran Tujuan —</option>
                        @foreach ($semuaTahunAjaran as $ta)
                            @if (!$ta->is_aktif)
                                <option value="{{ $ta->id }}" {{ request('tahun_ajaran_tujuan_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-search me-2"></i>Tampilkan Siswa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if ($kelasAsal && $tahunAjaranTujuan && $muridKelasList->count() > 0)
{{-- Form Submit Kenaikan Kelas --}}
<form method="POST" action="{{ route('master.kenaikan-kelas.store') }}" id="kenaikanForm">
    @csrf
    <input type="hidden" name="tahun_ajaran_tujuan_id" value="{{ $tahunAjaranTujuan->id }}">

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>
                <i class="fas fa-users me-2" style="color:#6366f1"></i>
                Siswa Kelas <strong>{{ $kelasAsal->nama_kelas }}</strong>
                &mdash; Tujuan Tahun Ajaran <strong>{{ $tahunAjaranTujuan->nama }}</strong>
            </span>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setAllStatus('naik')">
                    <i class="fas fa-arrow-up me-1"></i>Naik Semua
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="setAllStatus('lulus')">
                    <i class="fas fa-graduation-cap me-1"></i>Lulus Semua
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="siswaTable">
                    <thead style="position:sticky;top:0;background:var(--card-bg);z-index:1">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th style="width:170px">Keputusan</th>
                            <th>Kelas Tujuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($muridKelasList as $i => $mk)
                        @php
                            $tingkatTertinggi = $semuaKelas->max('tingkat');
                            $isLevelTertinggi = $kelasAsal->tingkat >= $tingkatTertinggi;
                            $defaultStatus = $isLevelTertinggi ? 'lulus' : 'naik';
                        @endphp
                        <tr>
                            <td class="text-muted" style="font-size:12px">{{ $i + 1 }}</td>
                            <td>
                                <strong>{{ $mk->murid?->personil?->nama ?? '-' }}</strong>
                            </td>
                            <td><small class="text-muted">{{ $mk->murid?->nis ?? '-' }}</small></td>
                            <td>
                                <select name="keputusan[{{ $mk->id }}]"
                                    class="form-select form-select-sm keputusan-select"
                                    data-mk-id="{{ $mk->id }}"
                                    onchange="toggleKelasTujuan(this)">
                                    <option value="naik"   {{ $defaultStatus == 'naik'   ? 'selected' : '' }}>⬆ Naik Kelas</option>
                                    <option value="tinggal"{{ $defaultStatus == 'tinggal'? 'selected' : '' }}>↩ Tinggal Kelas</option>
                                    <option value="lulus"  {{ $defaultStatus == 'lulus'  ? 'selected' : '' }}>🎓 Lulus</option>
                                </select>
                            </td>
                            <td>
                                <div class="kelas-tujuan-wrapper" data-mk-id="{{ $mk->id }}"
                                    style="{{ $defaultStatus == 'lulus' ? 'display:none' : '' }}">
                                    <select name="kelas_tujuan[{{ $mk->id }}]" class="form-select form-select-sm">
                                        <option value="">— Pilih Kelas —</option>
                                        @foreach ($semuaKelas as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $kelasTujuanDefault && $kelasTujuanDefault->id == $k->id && $defaultStatus == 'naik' ? 'selected' : '' }}
                                                {{ $kelasAsal->id == $k->id && $defaultStatus == 'tinggal' ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }} (Tkt.{{ $k->tingkat }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="text-muted fst-italic lulus-label" data-mk-id="{{ $mk->id }}"
                                    style="font-size:12px;{{ $defaultStatus != 'lulus' ? 'display:none' : '' }}">
                                    Tidak perlu kelas
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('master.kenaikan-kelas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times me-2"></i>Batal
            </a>
            <button type="submit" class="btn btn-accent px-5" id="btnSimpan">
                <i class="fas fa-save me-2"></i>Simpan Kenaikan Kelas
            </button>
        </div>
    </div>
</form>

@elseif ($kelasAsal && $tahunAjaranTujuan && $muridKelasList->count() == 0)
<div class="alert" style="border-radius:10px;border:none;background:rgba(245,158,11,.1);color:#92400e">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Tidak ada siswa aktif di kelas <strong>{{ $kelasAsal->nama_kelas }}</strong> pada tahun ajaran yang sedang aktif.
</div>
@elseif (request('kelas_id') || request('tahun_ajaran_tujuan_id'))
<div class="alert" style="border-radius:10px;border:none;background:rgba(99,102,241,.08);color:var(--text-muted)">
    <i class="fas fa-info-circle me-2"></i>Pilih kelas asal <em>dan</em> tahun ajaran tujuan untuk menampilkan daftar siswa.
</div>
@endif

@push('scripts')
<script>
    function toggleKelasTujuan(sel) {
        const id     = sel.dataset.mkId;
        const val    = sel.value;
        const wrap   = document.querySelector(`.kelas-tujuan-wrapper[data-mk-id="${id}"]`);
        const label  = document.querySelector(`.lulus-label[data-mk-id="${id}"]`);
        const isLulus = val === 'lulus';

        wrap.style.display  = isLulus ? 'none' : '';
        label.style.display = isLulus ? ''     : 'none';

        // Auto-select kelas tujuan based on keputusan
        if (!isLulus) {
            const kelasAsal  = {{ $kelasAsal?->id ?? 'null' }};
            const kelasTujuan = {{ $kelasTujuanDefault?->id ?? 'null' }};
            const select = wrap.querySelector('select');

            if (val === 'naik' && kelasTujuan) {
                select.value = kelasTujuan;
            } else if (val === 'tinggal' && kelasAsal) {
                select.value = kelasAsal;
            }
        }
    }

    function setAllStatus(status) {
        document.querySelectorAll('.keputusan-select').forEach(sel => {
            sel.value = status;
            toggleKelasTujuan(sel);
        });
    }

    // Prevent submitting with empty kelas_tujuan for naik/tinggal
    document.getElementById('kenaikanForm')?.addEventListener('submit', function (e) {
        let ok = true;
        document.querySelectorAll('.keputusan-select').forEach(sel => {
            const id = sel.dataset.mkId;
            if (sel.value !== 'lulus') {
                const kelas = document.querySelector(`.kelas-tujuan-wrapper[data-mk-id="${id}"] select`);
                if (!kelas.value) {
                    ok = false;
                    kelas.classList.add('is-invalid');
                } else {
                    kelas.classList.remove('is-invalid');
                }
            }
        });
        if (!ok) {
            e.preventDefault();
            alert('Harap pilih kelas tujuan untuk semua siswa yang Naik atau Tinggal Kelas.');
        }
    });
</script>
@endpush
@endsection
