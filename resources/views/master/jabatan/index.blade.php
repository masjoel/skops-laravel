@extends('layouts.app')
@section('title', 'Data ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-tie me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
        <a href="{{ route('master.jabatan.create') }}" class="btn btn-accent">
            <i class="fas fa-plus me-2"></i>Tambah {{ $title }}
        </a>
    </div>

    <div class="row g-3">
        <div class="col-12">
            {{-- Filter Bar --}}
            <div class="card bg-light mb-3">
                <div class="card-body" style="padding:14px 20px">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-12 col-md-10">
                            <div class="input-group" style="border-radius:8px;overflow:hidden">
                                <span class="input-group-text"
                                    style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control" placeholder="Cari..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-accent">Filter</button>
                            @if (request()->hasAny(['search']))
                                <a href="{{ route('master.jabatan.index') }}"
                                    class="btn btn-outline-secondary ms-1">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="card bg-light">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Daftar {{ $title }} <span class="text-muted fw-normal"
                            style="font-size:13px">({{ $jabatan->total() }} data)</span></span>
                </div>
                <div class="card-body table-responsive" style="padding:0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nama Jabatan</th>
                                <th>Kategori</th>
                                <th class="text-center" style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jabatan as $i => $s)
                                <tr>
                                    <td style="color:var(--text-muted);font-size:12px">{{ $jabatan->firstItem() + $i }}
                                    </td>
                                    <td>{{ $s->nama_jabatan }}</td>
                                    <td>{{ $s->kategori }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('master.jabatan.edit', $s->id) }}" class="btn btn-sm"
                                                style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i
                                                    class="fas fa-pen"></i></a>
                                            <form method="POST" action="{{ route('master.jabatan.destroy', $s->id) }}"
                                                onsubmit="return confirm('Hapus jabatan {{ addslashes($s->nama_jabatan) }}?')">
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
                                            class="fas fa-ruler fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada jabatan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($jabatan->hasPages())
                    <div class="card-body border-top" style="padding:12px 20px">{{ $jabatan->links() }}</div>
                @endif
            </div>
        </div>

    </div>
@endsection
