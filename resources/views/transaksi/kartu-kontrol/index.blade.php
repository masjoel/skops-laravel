@extends('layouts.app')
@section('title', 'Data ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-chart-line me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
        <a href="{{ route('transaksi.kartu-kontrol.create') }}" class="btn btn-accent">
            <i class="fas fa-plus me-2"></i>Tambah {{ $title }}
        </a>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card-body border-top d-flex flex-wrap gap-3" style="padding:12px 20px">
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-2" style="background:rgba(239,68,68,.1)">
                    <i class="fas fa-exclamation-triangle" style="color:#ef4444"></i>
                    <span style="font-size:13px;color:var(--text-muted)">Total Pelanggaran:</span>
                    <strong style="color:#ef4444">{{ $totalPelanggaran }}</strong>
                </div>
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-2" style="background:rgba(239,68,68,.07)">
                    <i class="fas fa-minus-circle" style="color:#ef4444"></i>
                    <span style="font-size:13px;color:var(--text-muted)">Poin Pelanggaran:</span>
                    <strong style="color:#ef4444">{{ $skorPelanggaran }}</strong>
                </div>
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-2" style="background:rgba(34,197,94,.1)">
                    <i class="fas fa-star" style="color:#16a34a"></i>
                    <span style="font-size:13px;color:var(--text-muted)">Total Reward:</span>
                    <strong style="color:#16a34a">{{ $totalReward }}</strong>
                </div>
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-2" style="background:rgba(34,197,94,.07)">
                    <i class="fas fa-plus-circle" style="color:#16a34a"></i>
                    <span style="font-size:13px;color:var(--text-muted)">Poin Reward:</span>
                    <strong style="color:#16a34a">{{ $skorReward }}</strong>
                </div>
            </div>
            {{-- Filter Bar --}}
            <div class="card bg-light mb-3">
                <div class="card-body" style="padding:14px 20px">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-12 col-md-3">
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
                            <select name="jenis" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="pelanggaran" {{ request('jenis') == 'pelanggaran' ? 'selected' : '' }}>
                                    Pelanggaran</option>
                                <option value="prestasi" {{ request('jenis') == 'prestasi' ? 'selected' : '' }}>Prestasi
                                </option>
                            </select>
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
                                <a href="{{ route('transaksi.kartu-kontrol.index') }}"
                                    class="btn btn-outline-secondary ms-1">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <span class="mb-2">Daftar {{ $title }} <span class="text-muted fw-normal"
                            style="font-size:13px">({{ $kartuKontrol->total() }} data)</span></span>
                    <a href="{{ route('transaksi.kartu-kontrol.download', request()->query()) }}"
                        class="btn btn-sm btn-success" style="font-size:12px;padding:4px 12px">
                        <i class="fas fa-file-excel me-1"></i> Excel 
                        {{-- Download <i class="fas fa-download ms-1"></i> --}}
                    </a>
                </div>
                <div class="card-body table-responsive" style="padding:0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Kode</th>
                                <th>Deskripsi</th>
                                <th>Jenis</th>
                                <th class="text-center">Skor</th>
                                <th>Tindakan</th>
                                <th>Guru</th>
                                <th>Semester</th>
                                <th>Opr</th>
                                <th class="text-center" style="width:80px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kartuKontrol as $i => $kk)
                                <tr>
                                    <td style="color:var(--text-muted);font-size:12px">
                                        {{ $kartuKontrol->firstItem() + $i }}</td>
                                    <td style="white-space:nowrap">{{ $kk->tgl?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ $kk->muridKelas?->murid?->personil?->nama ?? '-' }}</td>
                                    <td>
                                        {{ $kk->muridKelas?->kelas?->nama_kelas ?? '-' }}
                                        {{ $kk->muridKelas?->kelas?->jurusan?->nama ?? '' }}
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $kk->jenisPoin?->kode ?? '-' }}</span></td>
                                    <td style="font-size: 70%">{{ $kk->jenisPoin?->deskripsi ?? '-' }}</td>
                                    <td>
                                        @if ($kk->jenisPoin?->jenis == 'pelanggaran')
                                            <span class="badge"
                                                style="background:rgba(239,68,68,.15);color:#ef4444">Pelanggaran</span>
                                        @elseif ($kk->jenisPoin?->jenis == 'reward')
                                            <span class="badge"
                                                style="background:rgba(34,197,94,.15);color:#16a34a">Reward</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="{{ ($kk->skor ?? 0) < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                            {{ $kk->skor ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="font-size: 70%">{{ $kk->tindakan ?? '-' }}</td>
                                    <td style="font-size: 70%">{{ $kk->guru?->personil?->nama ?? '-' }}</td>
                                    <td style="font-size: 70%">
                                        @if ($kk->periodeAkademik)
                                            {{ $kk->periodeAkademik->semester == 1 ? 'Ganjil' : 'Genap' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="font-size: 70%">{{ $kk->user?->name ?? '-' }}<br><span
                                            class="text-muted">{{ $kk->created_at?->format('d/m/Y H:i') }}</span></td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('transaksi.kartu-kontrol.edit', $kk->id) }}"
                                                class="btn btn-sm"
                                                style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i
                                                    class="fas fa-pen"></i></a>
                                            <form method="POST"
                                                action="{{ route('transaksi.kartu-kontrol.destroy', $kk->id) }}"
                                                onsubmit="return confirm('Hapus data ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm"
                                                    style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center" style="padding:40px;color:var(--text-muted)">
                                        <i class="fas fa-chart-line fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada
                                        data
                                        kartu kontrol
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($kartuKontrol->hasPages())
                    <div class="card-body border-top" style="padding:12px 20px">{{ $kartuKontrol->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
