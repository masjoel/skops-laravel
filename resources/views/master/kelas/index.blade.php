@extends('layouts.app')
@section('title', 'Data ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-ruler me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
        <a href="{{ route('master.kelas.create') }}" class="btn btn-accent">
            <i class="fas fa-plus me-2"></i>Tambah Kelas
        </a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-8">
            {{-- Filter Bar --}}
            <div class="card mb-3">
                <div class="card-body" style="padding:14px 20px">
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-12 col-md-6">
                            <div class="input-group" style="border-radius:8px;overflow:hidden">
                                <span class="input-group-text"
                                    style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control" placeholder="Cari..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <select name="jurusan" class="form-select">
                                <option value="">Semua Jurusan</option>
                                @foreach ($jurusan as $j)
                                    <option value="{{ $j->id }}"
                                        {{ request('jurusan') == $j->id ? 'selected' : '' }}>
                                        {{ $j->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-accent">Filter</button>
                            @if (request()->hasAny(['search', 'jurusan']))
                                <a href="{{ route('master.kelas.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Daftar {{ $title }} <span class="text-muted fw-normal"
                            style="font-size:13px">({{ $kelas->total() }} data)</span></span>
                </div>
                <div class="card-body table-responsive" style="padding:0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Nama Kelas</th>
                                <th>Tingkat</th>
                                <th>Jurusan</th>
                                <th class="text-center" style="width:100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kelas as $i => $s)
                                <tr>
                                    <td style="color:var(--text-muted);font-size:12px">{{ $kelas->firstItem() + $i }}
                                    </td>
                                    <td style="font-weight:500">{{ $s->nama_kelas }}</td>
                                    <td style="font-weight:500">{{ $s->tingkat }}</td>
                                    <td style="font-size:13px;color:var(--text-muted)">{{ $s->jurusan->nama ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('master.kelas.edit', $s->id) }}" class="btn btn-sm"
                                                style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i
                                                    class="fas fa-pen"></i></a>
                                            <form method="POST" action="{{ route('master.kelas.destroy', $s->id) }}"
                                                onsubmit="return confirm('Hapus kelas {{ addslashes($s->nama) }}?')">
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
                                            class="fas fa-ruler fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada kelas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($kelas->hasPages())
                    <div class="card-body border-top" style="padding:12px 20px">{{ $kelas->links() }}</div>
                @endif
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-plus-circle me-2" style="color:#6366f1"></i>Tambah Kelas</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('master.kelas.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kelas"
                                class="form-control @error('nama_kelas') is-invalid @enderror"
                                value="{{ old('nama_kelas') }}" placeholder="Contoh: 7A, 8B, 9C"
                                oninput="this.value = this.value.toUpperCase();" required>
                            @error('nama_kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <input type="text" name="tingkat" class="form-control @error('tingkat') is-invalid @enderror"
                                value="{{ old('tingkat') }}" placeholder="Contoh: 7, 8, 9, X, XI, XII"
                                oninput="this.value = this.value.toUpperCase();" required>
                            @error('tingkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jurusan</label>
                            <select name="jurusan_id" class="form-control @error('jurusan_id') is-invalid @enderror">
                                <option value="">Pilih Jurusan</option>
                                @foreach ($jurusan as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('jurusan_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jurusan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-accent w-100"><i
                                class="fas fa-save me-2"></i>Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
