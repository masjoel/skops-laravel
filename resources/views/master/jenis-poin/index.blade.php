@extends('layouts.app')
@section('title', 'Data ' . $title)

@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-tags me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ $title }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('master.jenis-poin.create') }}" class="btn btn-accent">
            <i class="fas fa-plus me-2"></i>Tambah
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>
                Daftar {{ $title }}
                <span class="text-muted fw-normal" style="font-size:13px">
                    ({{ $jenisPoin->total() }} data)
                </span>
            </span>

            <form method="GET" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari {{ $title }}..." value="{{ request('search') }}" style="width:200px">

                <button type="submit" class="btn btn-sm btn-accent">Cari</button>

                @if (request('search'))
                    <a href="{{ route('master.jenis-poin.index') }}" class="btn btn-sm btn-outline-secondary">
                        Reset
                    </a>
                @endif
            </form>
        </div>
        <div class="card-body table-responsive" style="padding:0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Kode</th>
                        <th>Deskripsi</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Tindak Lanjut</th>
                        <th class="text-center">Skor</th>
                        <th class="text-center" style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenisPoin as $i => $jp)
                        <tr>
                            {{-- <td style="color:var(--text-muted);font-size:12px">{{ $jenisPoin->firstItem() + $i }}</td> --}}
                            <td>{{ $jp->urut }}</td>
                            <td class="text-nowrap">{{ $jp->kode }}</td>
                            <td>{{ $jp->deskripsi ?: '-' }}</td>
                            <td>{{ ucfirst($jp->jenis) }}</td>
                            <td style="color:var(--text-muted)">{{ $jp->keterangan }}</td>
                            <td style="color:var(--text-muted)">{{ $jp->tindakan }}</td>
                            <td class="text-center">
                                <span
                                    style="background:rgba(99,102,241,.1);color:{{ $jp->skor < 0 ? '#ef4444' : '#6366f1' }};padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600">
                                    {{ $jp->skor }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('master.jenis-poin.edit', $jp->id) }}" class="btn btn-sm"
                                        style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('master.jenis-poin.destroy', $jp->id) }}"
                                        onsubmit="return confirm('Hapus jenis poin {{ addslashes($jp->nama) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                            style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding:40px;color:var(--text-muted)">
                                <i class="fas fa-tags fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada jenis poin
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($jenisPoin->hasPages())
            <div class="card-body border-top" style="padding:12px 20px">{{ $jenisPoin->links() }}</div>
        @endif
    </div>
@endsection
