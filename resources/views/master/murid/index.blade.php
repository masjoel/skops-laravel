@extends('layouts.app')
@section('title', 'Data ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-users me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
        <a href="{{ route('master.murid.create') }}" class="btn btn-accent">
            <i class="fas fa-plus me-2"></i>Tambah {{ $title }}
        </a>
    </div>

    <div class="row g-3">
        <div class="col-12">
            {{-- Filter Bar --}}
            <div class="card bg-light mb-3">
                <div class="card-body" style="padding:14px 20px">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <div class="input-group" style="border-radius:8px;overflow:hidden">
                                <span class="input-group-text"
                                    style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control" placeholder="Cari..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <select name="gender" class="form-select">
                                <option value="">Gender</option>
                                <option value="L" {{ request('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ request('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <select name="status" class="form-select">
                                <option value="aktif" {{ request('status', 'aktif') == 'aktif' ? 'selected' : '' }}>
                                    Aktif</option>
                                <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus
                                </option>
                                <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar
                                </option>
                                <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah
                                    Sekolah</option>
                                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua
                                    Status</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <select name="tahun_ajaran_id" class="form-select">
                                @foreach ($tahunAjaran as $ta)
                                    <option value="{{ $ta->id }}"
                                        {{ request('tahun_ajaran_id', $tahunAjaranAktif->id ?? null) == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-accent">Filter</button>
                            @if (request()->hasAny(['search', 'gender', 'status', 'tahun_ajaran_id']))
                                <a href="{{ route('master.murid.index') }}"
                                    class="btn btn-outline-secondary ms-1">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="card bg-light">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Daftar {{ $title }} <span class="text-muted fw-normal"
                            style="font-size:13px">({{ $murid->total() }} data)</span></span>
                    <div>
                        <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            <i class="fas fa-file-import me-1"></i> Import
                        </button>
                        <a href="{{ route('master.murid.download', request()->query()) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel me-1"></i> Export
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive" style="padding:0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nama</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th class="text-center" style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($murid as $i => $s)
                                <tr>
                                    <td style="color:var(--text-muted);font-size:12px">{{ $murid->firstItem() + $i }}
                                    </td>
                                    <td>{{ $s->personil->nama }}</td>
                                    <td>{{ $s->nis }}</td>
                                    <td>{{ $s->nisn }}</td>
                                    <td>{{ $s->riwayatKelas->first()->kelas->nama_kelas ?? 'Tidak ada kelas' }}
                                        {{ $s->riwayatKelas->first()->kelas->jurusan->nama ?? '' }}</td>
                                    <td>
                                        @switch($s->status)
                                            @case('aktif')
                                                <span class="badge"
                                                    style="background:rgba(34,197,94,.15);color:#16a34a">Aktif</span>
                                            @break

                                            @case('lulus')
                                                <span class="badge"
                                                    style="background:rgba(99,102,241,.15);color:#6366f1">Lulus</span>
                                            @break

                                            @case('keluar')
                                                <span class="badge"
                                                    style="background:rgba(239,68,68,.15);color:#ef4444">Keluar</span>
                                            @break

                                            @case('pindah')
                                                <span class="badge"
                                                    style="background:rgba(234,179,8,.15);color:#ca8a04">Pindah</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('master.murid.edit', ['murid' => $s->id, 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"
                                                class="btn btn-sm"
                                                style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i
                                                    class="fas fa-pen"></i></a>
                                            <form method="POST" action="{{ route('master.murid.destroy', $s->id) }}"
                                                onsubmit="return confirm('Hapus siswa {{ addslashes($s->personil->nama) }}?')">
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
                                    <td colspan="7" class="text-center" style="padding:40px;color:var(--text-muted)"><i
                                            class="fas fa-ruler fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada siswa
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($murid->hasPages())
                    <div class="card-body border-top" style="padding:12px 20px">{{ $murid->links() }}</div>
                @endif
            </div>
        </div>

    </div>

    <!-- Modal Import Siswa -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background:var(--card-bg);border-color:var(--border-color)">
                <form action="{{ route('master.murid.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom" style="border-color:var(--border-color)!important">
                        <h5 class="modal-title" id="importModalLabel">Import Data Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileMurid" class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input class="form-control" type="file" id="fileMurid" name="file" accept=".xlsx,.xls,.csv"
                                required>
                        </div>
                        <small class="text-muted">Format kolom: No, NIS, NISN, Nama, L/P, Kelas, Status. Gunakan
                            tombol Export untuk melihat formatnya.</small>
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