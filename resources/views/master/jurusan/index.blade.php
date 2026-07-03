@extends('layouts.app')
@section('title', 'Data ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-sitemap me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
        <a href="{{ route('master.jurusan.create') }}" class="btn btn-accent">
            <i class="fas fa-plus me-2"></i>Tambah Jurusan
        </a>
    </div>
    <div class="row g-3">
        <div class="col-12 col-md-8">
            <div class="card bg-light">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <span>Daftar {{ $title }} <span class="text-muted fw-normal"
                            style="font-size:13px">({{ $jurusans->total() }} data)</span></span>
                    <form method="GET" class="d-flex flex-wrap gap-2">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..."
                            value="{{ request('search') }}" style="width:180px">
                        <button type="submit" class="btn btn-sm btn-accent">Cari</button>
                        @if (request('search'))
                            <a href="{{ route('master.jurusan.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </form>
                </div>
                <div class="card-body" style="padding:0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nama Jurusan</th>
                                <th>Kode</th>
                                <th class="text-center" style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jurusans as $i => $s)
                                <tr>
                                    <td style="color:var(--text-muted);font-size:12px">{{ $jurusans->firstItem() + $i }}
                                    </td>
                                    <td style="font-weight:500">{{ $s->nama }}</td>
                                    <td style="font-size:13px;color:var(--text-muted)">{{ $s->kode ?: '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('master.jurusan.edit', $s->id) }}" class="btn btn-sm"
                                                style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i
                                                    class="fas fa-pen"></i></a>
                                            <form method="POST" action="{{ route('master.jurusan.destroy', $s->id) }}"
                                                onsubmit="return confirm('Hapus jurusan {{ addslashes($s->nama) }}?')">
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
                                    <td colspan="4" class="text-center" style="padding:40px;color:var(--text-muted)"><i
                                            class="fas fa-ruler fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada jurusan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($jurusans->hasPages())
                    <div class="card-body border-top" style="padding:12px 20px">{{ $jurusans->links() }}</div>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card bg-light">
                <div class="card-header"><i class="fas fa-plus-circle me-2" style="color:#6366f1"></i>Tambah Jurusan</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('master.jurusan.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Kode</label>
                            <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror"
                                value="{{ old('kode') }}" placeholder="Contoh: IPA, RPL, TKJ">
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama') }}" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-accent w-100"><i class="fas fa-save me-2"></i>Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
