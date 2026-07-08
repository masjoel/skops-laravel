@extends('layouts.app')
@section('title', 'Data ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-friends me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
        <a href="{{ route('master.guru.create') }}" class="btn btn-accent">
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
                            <select name="jabatan" class="form-select">
                                <option value="">Jabatan</option>
                                @foreach ($jabatan as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request('jabatan') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_jabatan }}</option>
                                @endforeach
                            </select>
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
                        <div class="col-auto">
                            <button type="submit" class="btn btn-accent">Filter</button>
                            @if (request()->hasAny(['search', 'gender', 'status']))
                                <a href="{{ route('master.guru.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="card bg-light">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Daftar {{ $title }} <span class="text-muted fw-normal"
                            style="font-size:13px">({{ $guru->total() }} data)</span></span>
                    <a href="{{ route('master.guru.download', request()->query()) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </a>
                </div>
                <div class="card-body table-responsive" style="padding:0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nama</th>
                                <th>NIP</th>
                                <th>No. HP</th>
                                <th>Email</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th class="text-center" style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guru as $i => $s)
                                <tr>
                                    <td style="color:var(--text-muted);font-size:12px">{{ $guru->firstItem() + $i }}
                                    </td>
                                    <td>{{ $s->personil->nama }}</td>
                                    <td>{{ $s->nip }}</td>
                                    <td>{{ $s->personil->no_hp }}</td>
                                    <td>{{ $s->personil->email }}</td>
                                    <td>{{ $s->jabatanStruktural ? $s->jabatanStruktural->nama_jabatan : '-' }}</td>
                                    <td>{{ ucfirst($s->personil->status) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('master.guru.edit', $s->id) }}" class="btn btn-sm"
                                                style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i
                                                    class="fas fa-pen"></i></a>
                                            <form method="POST" action="{{ route('master.guru.destroy', $s->id) }}"
                                                onsubmit="return confirm('Hapus guru {{ addslashes($s->personil->nama) }}?')">
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
                                    <td colspan="5" class="text-center" style="padding:40px;color:var(--text-muted)"><i
                                            class="fas fa-ruler fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada guru
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($guru->hasPages())
                    <div class="card-body border-top" style="padding:12px 20px">{{ $guru->links() }}</div>
                @endif
            </div>
        </div>

    </div>
@endsection
