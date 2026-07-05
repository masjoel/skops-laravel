@extends('layouts.app')

@section('title', 'Rekapitulasi Poin')
@section('style')
    <style>
        .stat-card {
            border-radius: 12px;
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            /* text-transform: uppercase; */
            letter-spacing: .5px;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .stat-sub {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
    </style>
@endsection
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-chart-pie me-2" style="color:#6366f1"></i>Rekapitulasi Poin</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Beranda</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary"
                style="font-size:12px;padding:6px 12px;border-radius:20px">
                <i class="fas fa-calendar-alt me-1"></i>{{ now()->isoFormat('D MMMM Y') }}
            </span>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #22c55e">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Reward</div>
                            <div class="stat-value text-success" id="val-jual">
                                {{ number_format($totalReward, 0, ',', '.') }}
                            </div>
                            <div class="stat-sub text-success">{{ number_format($skorReward, 0, ',', '.') }} poin</div>
                        </div>
                        <div class="stat-icon" style="background:rgba(34,197,94,.12);color:#22c55e">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #63b6f1">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Pemutihan</div>
                            <div class="stat-value text-info" id="val-barang">
                                {{ number_format($totalPemutihan, 0, ',', '.') }}</div>
                            <div class="stat-sub text-info">{{ number_format($skorPemutihan, 0, ',', '.') }} poin</div>
                        </div>
                        <div class="stat-icon" style="background:rgba(99,102,241,.12);color:#63b6f1">
                            <i class="fas fa-recycle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #ef4444">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Pelanggaran</div>
                            <div class="stat-value text-danger" id="val-kritis">
                                {{ number_format($totalPelanggaran, 0, ',', '.') }}</div>
                            <div class="stat-sub text-danger">
                                {{ number_format($skorPelanggaran, 0, ',', '.') }} poin
                            </div>
                        </div>
                        <div class="stat-icon" style="background:rgba(239,68,68,.12);color:#ef4444">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Bottom Tables ── --}}
    <div class="row g-3">
        <div class="col-12 col-xl-12">
            <div class="card bg-light mb-3">
                <div class="card-body" style="padding:14px 20px">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-12 col-md-6">
                            <div class="input-group" style="border-radius:8px;overflow:hidden">
                                <span class="input-group-text"
                                    style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari siswa, kode, guru..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <select name="semester" class="form-select">
                                <option value="">Semua Semester</option>
                                <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>Ganjil</option>
                                <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <select name="tahun_ajaran_id" class="form-select">
                                <option value="">Semua Tahun</option>
                                @foreach ($tahunAjaran as $ta)
                                    <option value="{{ $ta->id }}"
                                        {{ request('tahun_ajaran_id', $tahunAjaranAktifId) == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-accent">Filter</button>
                            @if (request()->hasAny(['search', 'jenis', 'semester', 'tahun_ajaran_id']))
                                <a href="{{ route('laporan.rekapitulasi') }}"
                                    class="btn btn-outline-secondary ms-1">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="card bg-light">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="text-primary"><i class="fas fa-receipt me-2 text-primary"></i>Rekapitulasi Poin Siswa <span
                            class="text-muted fw-normal" style="font-size:13px">({{ $rekapitullasi->total() }} data)</span>
                    </div>
                    <a href="{{ route('laporan.rekapitulasi') }}" class="btn btn-sm btn-primary"
                        style="font-size:12px;padding:4px 12px">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body" style="padding:0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th class="text-center">NIS</th>
                                    <th class="text-center">NISN</th>
                                    <th class="text-center">Pelanggaran</th>
                                    <th class="text-center">Reward</th>
                                    <th class="text-center">Pemutihan</th>
                                    <th class="text-end">Poin Akhir</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapitullasi as $index => $siswa)
                                    <tr>
                                        <td>
                                            {{ $siswa->muridKelas?->murid?->personil?->nama ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $siswa->muridKelas?->kelas?->nama_kelas ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $siswa->muridKelas?->murid?->nis ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ $siswa->muridKelas?->murid?->nisn ?? '-' }}
                                        </td>
                                        <td class="text-center text-danger">
                                            {{ $siswa->total_pelanggaran ?: '0' }}
                                        </td>
                                        <td class="text-center text-success">
                                            {{ $siswa->total_reward ?: '0' }}
                                        </td>
                                        <td class="text-center text-warning">
                                            {{ $siswa->total_pemutihan ?: '0' }}
                                        </td>
                                        <td class="text-end" style="font-weight:600;">
                                            <span
                                                style="background:rgba(99,102,241,.1);color:{{ $siswa->total_reward + $siswa->total_pelanggaran + $siswa->total_pemutihan < 0 ? '#ef4444' : '#6366f1' }};padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600">
                                                {{ $siswa->total_reward + $siswa->total_pelanggaran + $siswa->total_pemutihan }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('laporan.rekapitulasi.show', $siswa->murid_kelas_id) }}"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-list"></i> Detil
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center"
                                            style="padding:32px;color:var(--text-muted);font-size:13px">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
                                            Belum ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($rekapitullasi->hasPages())
                        <div class="card-body border-top" style="padding:12px 20px">{{ $rekapitullasi->links() }}</div>
                    @endif

                </div>
            </div>
        </div>

    </div>
@endsection
