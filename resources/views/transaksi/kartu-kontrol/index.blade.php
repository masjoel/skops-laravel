@extends('layouts.app')
@section('title', 'Data ' . $title)
@section('content')
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
        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #22c55e">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Reward</div>
                            <div class="stat-value text-success" id="val-jual">
                                {{ number_format($totalReward, 0, ',', '.') }}
                            </div>
                            <div class="stat-sub text-success">{{ number_format($skorReward, 0, ',', '.') }} poin
                            </div>
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
                            <div class="stat-sub text-info">{{ number_format($skorPemutihan, 0, ',', '.') }} poin
                            </div>
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
        <div class="col-12">
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
                                <option value="reward" {{ request('jenis') == 'reward' ? 'selected' : '' }}>Reward
                                </option>
                                <option value="pemutihan" {{ request('jenis') == 'pemutihan' ? 'selected' : '' }}>Pemutihan
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
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#importKKModal" style="font-size:12px;padding:4px 12px">
                            <i class="fas fa-file-import me-1"></i> Import
                        </button>
                        <a href="{{ route('transaksi.kartu-kontrol.download', request()->query()) }}"
                            class="btn btn-sm btn-success" style="font-size:12px;padding:4px 12px">
                            <i class="fas fa-file-excel me-1"></i> Export
                        </a>
                    </div>
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
                                        @elseif ($kk->jenisPoin?->jenis == 'pemutihan')
                                            <span class="badge"
                                                style="background:rgba(8, 215, 234, 0.15);color:#2bbaed">Pemutihan</span>
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

    <!-- Modal Import Kartu Kontrol -->
    <div class="modal fade" id="importKKModal" tabindex="-1" aria-labelledby="importKKLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background:var(--card-bg);border-color:var(--border-color)">
                <form action="{{ route('transaksi.kartu-kontrol.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom" style="border-color:var(--border-color)!important">
                        <h5 class="modal-title" id="importKKLabel">Import Data Kartu Kontrol</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileKK" class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input class="form-control" type="file" id="fileKK" name="file"
                                accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="alert alert-warning py-2" style="font-size:12px">
                            <strong>Penting:</strong> Gunakan file Export dari sistem ini sebagai template.
                            <ul class="mb-0 mt-1">
                                <li>Data akan diimpor ke <strong>tahun ajaran aktif</strong>.</li>
                                <li>Kolom <strong>Kode</strong> digunakan untuk mencari jenis poin.</li>
                                <li>Kolom <strong>Guru</strong> bisa diisi <strong>Nama</strong> atau <strong>NIP</strong>
                                    guru (sesuai hasil Export).</li>
                                <li>Kolom <strong>Semester</strong>: tulis <em>Ganjil</em> atau <em>Genap</em>.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer border-top" style="border-color:var(--border-color)!important">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
