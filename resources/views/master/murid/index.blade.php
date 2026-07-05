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
                                <option value="">Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Non Aktif
                                </option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2">
                            <select name="tahun_ajaran_id" class="form-select">
                                @foreach ($tahunAjaran as $ta)
                                    <option value="{{ $ta->id }}"
                                        {{ request('tahun_ajaran_id', $tahunAjaranAktif) == $ta->id ? 'selected' : '' }}>
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
                    <a href="{{ route('master.murid.download', request()->query()) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </a>
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
                                    <td>{{ ucfirst($s->personil->status) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('master.murid.edit', ['murid' => $s->id, 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}" class="btn btn-sm"
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
@endsection
