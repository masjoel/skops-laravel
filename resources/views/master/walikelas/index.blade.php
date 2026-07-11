@extends('layouts.app')
@section('title', 'Data ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-plus me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
        @if (auth()->user()->role == 'administrator' || auth()->user()->role == 'operator')
            <a href="{{ route('master.walikelas.create') }}" class="btn btn-accent">
                <i class="fas fa-plus me-2"></i>Tambah {{ $title }}
            </a>
        @endif
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
                                <option value="">Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non Aktif
                                </option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <select name="tahun_ajaran_id" class="form-select">
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id }}"
                                        {{ request('tahun_ajaran_id', $tahunAjaranAktifId) == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-accent">Filter</button>
                            @if (request()->hasAny(['search', 'gender', 'status', 'tahun_ajaran_id']))
                                <a href="{{ route('master.walikelas.index') }}"
                                    class="btn btn-outline-secondary ms-1">Reset</a>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
            <div class="card bg-light">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Daftar {{ $title }} <span class="text-muted fw-normal"
                            style="font-size:13px">({{ $walikelas->total() }} data)</span></span>
                    @if (auth()->user()->role == 'administrator' || auth()->user()->role == 'operator')
                        <div>
                            <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal"
                                data-bs-target="#importWaliKelasModal">
                                <i class="fas fa-file-import me-1"></i> Import
                            </button>
                            <a href="{{ route('master.walikelas.download', request()->query()) }}"
                                class="btn btn-sm btn-success">
                                <i class="fas fa-file-excel me-1"></i> Export
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body table-responsive" style="padding:0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>Kelas</th>
                                <th>Tahun Ajaran</th>
                                <th>Status</th>
                                @if (auth()->user()->role == 'administrator' || auth()->user()->role == 'operator')
                                    <th class="text-center" style="width:100px">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($walikelas as $i => $s)
                                <tr>
                                    <td style="color:var(--text-muted);font-size:12px">{{ $walikelas->firstItem() + $i }}
                                    </td>
                                    <td>{{ $s->guru->personil->nama }}</td>
                                    <td>{{ $s->guru->nip }}</td>
                                    <td>{{ $s->kelas->nama_kelas }}</td>
                                    <td>{{ $s->tahunAjaran->nama }}</td>
                                    <td>{{ ucfirst($s->guru->personil->status) }}</td>
                                    @if (auth()->user()->role == 'administrator' || auth()->user()->role == 'operator')
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="{{ route('master.walikelas.edit', $s->id) }}" class="btn btn-sm"
                                                    style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i
                                                        class="fas fa-pen"></i></a>
                                                <form method="POST"
                                                    action="{{ route('master.walikelas.destroy', $s->id) }}"
                                                    onsubmit="return confirm('Hapus walikelas {{ addslashes($s->guru->personil->nama) }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm"
                                                        style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px"><i
                                                            class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center" style="padding:40px;color:var(--text-muted)"><i
                                            class="fas fa-ruler fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada
                                        walikelas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($walikelas->hasPages())
                    <div class="card-body border-top" style="padding:12px 20px">{{ $walikelas->links() }}</div>
                @endif
            </div>
        </div>

    </div>

    <!-- Modal Import Wali Kelas -->
    <div class="modal fade" id="importWaliKelasModal" tabindex="-1" aria-labelledby="importWaliKelasLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background:var(--card-bg);border-color:var(--border-color)">
                <form action="{{ route('master.walikelas.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom" style="border-color:var(--border-color)!important">
                        <h5 class="modal-title" id="importWaliKelasLabel">Import Data Wali Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileWaliKelas" class="form-label">Pilih File Excel (.xlsx, .xls, .csv)</label>
                            <input class="form-control" type="file" id="fileWaliKelas" name="file"
                                accept=".xlsx,.xls,.csv" required>
                        </div>
                        <small class="text-muted">Format kolom: No, Tahun Ajaran, Kelas, Tingkat, NIP, Nama Guru. Pastikan
                            data Guru dan Kelas sudah ada di sistem. Gunakan tombol Export untuk melihat formatnya.</small>
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
