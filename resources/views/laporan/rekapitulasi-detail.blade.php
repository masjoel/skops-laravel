@extends('layouts.app')
@section('title', $title)
@section('content')
    <style>
        .info-card {
            border-radius: 14px;
            overflow: hidden;
        }

        .avatar-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            flex-shrink: 0;
        }

        .badge-jenis {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .skor-pill {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .stat-mini {
            border-radius: 10px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stat-mini .stat-val {
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
        }

        .stat-mini .stat-lbl {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }
    </style>

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-graduate me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('laporan.rekapitulasi') }}">Rekapitulasi</a></li>
                <li class="breadcrumb-item active">Detil</li>
            </ol>
        </div>
        <a href="{{ route('laporan.rekapitulasi') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    {{-- Student Info Card --}}
    <div class="card info-card mb-4" style="border-left: 4px solid #6366f1">
        <div class="card-body" style="padding: 20px 24px">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="avatar-circle">
                    {{ strtoupper(substr($muridKelas->murid?->personil?->nama ?? 'S', 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="mb-2 text-primary" style="font-size:18px;font-weight:700;">
                        {{ $muridKelas->murid?->personil?->nama ?? '-' }}
                    </div>
                    <div class="d-flex flex-wrap gap-3 mt-1" style="font-size:13px;color:var(--text-muted)">
                        <span><i class="fas fa-school me-1"></i>Kelas
                            <strong>{{ $muridKelas->kelas?->nama_kelas ?? '-' }}</strong></span>
                        <span><i class="fas fa-id-card me-1"></i>NIS:
                            <strong>{{ $muridKelas->murid?->nis ?? '-' }}</strong></span>
                        <span><i class="fas fa-id-badge me-1"></i>NISN:
                            <strong>{{ $muridKelas->murid?->nisn ?? '-' }}</strong></span>
                        <span><i class="fas fa-calendar me-1"></i>{{ $muridKelas->tahunAjaran?->nama ?? '-' }}</span>
                    </div>
                </div>
                {{-- Poin Akhir Badge --}}
                <div class="text-center px-3 py-2 rounded-3"
                    style="background:rgba(99,102,241,.1);min-width:90px">
                    <div style="font-size:26px;font-weight:800;color:{{ $poinAkhir < 0 ? '#ef4444' : '#6366f1' }}">
                        {{ $poinAkhir }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">Poin Akhir</div>
                </div>
            </div>

            {{-- Stat Mini Row --}}
            <div class="d-flex flex-wrap gap-2 mt-3">
                <div class="col-12 col-md-3 stat-mini" style="background:rgba(34,197,94,.08)">
                    <i class="fas fa-star" style="color:#22c55e;font-size:18px"></i>
                    <div>
                        <div class="stat-val" style="color:#22c55e">{{ $totalReward }}</div>
                        <div class="stat-lbl">Total Reward</div>
                    </div>
                </div>
                <div class="col-12 col-md-3 stat-mini" style="background:rgba(239,68,68,.08)">
                    <i class="fas fa-exclamation-triangle" style="color:#ef4444;font-size:18px"></i>
                    <div>
                        <div class="stat-val" style="color:#ef4444">{{ $totalPelanggaran }}</div>
                        <div class="stat-lbl">Total Pelanggaran</div>
                    </div>
                </div>
                <div class="col-12 col-md-3 stat-mini" style="background:rgba(99,182,241,.08)">
                    <i class="fas fa-recycle" style="color:#63b6f1;font-size:18px"></i>
                    <div>
                        <div class="stat-val" style="color:#63b6f1">{{ $totalPemutihan }}</div>
                        <div class="stat-lbl">Total Pemutihan</div>
                    </div>
                </div>
                {{-- <div class="stat-mini ms-auto" style="background:rgba(99,102,241,.08)">
                    <i class="fas fa-list-ol" style="color:#6366f1;font-size:18px"></i>
                    <div>
                        <div class="stat-val" style="color:#6366f1">{{ $kartuKontrol->count() }}</div>
                        <div class="stat-lbl">Total Catatan</div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

    {{-- Filter + Table --}}
    <div class="card bg-light">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span style="font-weight:600"><i class="fas fa-history me-2 text-primary"></i>Riwayat Catatan Poin</span>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                {{-- Filter inline --}}
                <form method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <select name="jenis" class="form-select form-select-sm" style="width:auto">
                        <option value="">Semua Jenis</option>
                        <option value="pelanggaran" {{ request('jenis') == 'pelanggaran' ? 'selected' : '' }}>Pelanggaran</option>
                        <option value="reward" {{ request('jenis') == 'reward' ? 'selected' : '' }}>Reward</option>
                        <option value="pemutihan" {{ request('jenis') == 'pemutihan' ? 'selected' : '' }}>Pemutihan</option>
                    </select>
                    <select name="semester" class="form-select form-select-sm" style="width:auto">
                        <option value="">Semua Semester</option>
                        <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>Ganjil</option>
                        <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>Genap</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-accent">Filter</button>
                    @if (request()->hasAny(['jenis', 'semester']))
                        <a href="{{ route('laporan.rekapitulasi.show', $muridKelas->id) }}"
                            class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                    <a href="{{ route('laporan.rekapitulasi.download-detail', array_merge(['muridKelasId' => $muridKelas->id], request()->query())) }}"
                        class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </a>
                </form>
            </div>
        </div>
        <div class="card-body table-responsive" style="padding:0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Deskripsi</th>
                        <th>Jenis</th>
                        <th class="text-center">Skor</th>
                        <th>Tindakan</th>
                        <th>Guru</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kartuKontrol as $i => $kk)
                        <tr>
                            <td style="color:var(--text-muted);font-size:12px">{{ $i + 1 }}</td>
                            <td style="white-space:nowrap">{{ $kk->tgl?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $kk->jenisPoin?->kode ?? '-' }}</span>
                            </td>
                            <td style="font-size:13px;max-width:220px">{{ $kk->jenisPoin?->deskripsi ?? '-' }}</td>
                            <td>
                                @if ($kk->jenisPoin?->jenis == 'pelanggaran')
                                    <span class="badge-jenis"
                                        style="background:rgba(239,68,68,.13);color:#ef4444">Pelanggaran</span>
                                @elseif ($kk->jenisPoin?->jenis == 'reward')
                                    <span class="badge-jenis"
                                        style="background:rgba(34,197,94,.13);color:#16a34a">Reward</span>
                                @elseif ($kk->jenisPoin?->jenis == 'pemutihan')
                                    <span class="badge-jenis"
                                        style="background:rgba(99,182,241,.13);color:#0ea5e9">Pemutihan</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php $skor = $kk->skor ?? $kk->jenisPoin?->skor ?? 0; @endphp
                                <span class="skor-pill"
                                    style="background:{{ $skor < 0 ? 'rgba(239,68,68,.1)' : 'rgba(34,197,94,.1)' }};color:{{ $skor < 0 ? '#ef4444' : '#16a34a' }}">
                                    {{ $skor > 0 ? '+' : '' }}{{ $skor }}
                                </span>
                            </td>
                            <td style="font-size:12px;max-width:180px">{{ $kk->tindakan ?? '-' }}</td>
                            <td style="font-size:12px">{{ $kk->guru?->personil?->nama ?? '-' }}</td>
                            <td style="font-size:12px">
                                @if ($kk->periodeAkademik)
                                    {{ $kk->periodeAkademik->semester == 1 ? 'Ganjil' : 'Genap' }}
                                    <br><small class="text-muted">{{ $kk->periodeAkademik->tahunAjaran?->nama }}</small>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center" style="padding:40px;color:var(--text-muted)">
                                <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
                                Belum ada catatan poin untuk siswa ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($kartuKontrol->isNotEmpty())
                <tfoot>
                    <tr style="background:rgba(99,102,241,.04)">
                        <td colspan="5" class="text-end" style="font-weight:600;font-size:13px">Total Poin Akhir:</td>
                        <td class="text-center">
                            <span class="skor-pill"
                                style="background:{{ $poinAkhir < 0 ? 'rgba(239,68,68,.12)' : 'rgba(99,102,241,.12)' }};color:{{ $poinAkhir < 0 ? '#ef4444' : '#6366f1' }};font-size:13px">
                                {{ $poinAkhir > 0 ? '+' : '' }}{{ $poinAkhir }}
                            </span>
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
