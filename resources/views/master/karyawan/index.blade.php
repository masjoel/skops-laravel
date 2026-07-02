@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-id-badge me-2" style="color:#6366f1"></i>Data Karyawan</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Karyawan</li></ol>
    </div>
    <a href="{{ route('master.karyawan.create') }}" class="btn btn-accent"><i class="fas fa-plus me-2"></i>Tambah Karyawan</a>
</div>

<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, jabatan, telepon..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-accent">Filter</button>
                @if(request()->hasAny(['search','status']))<a href="{{ route('master.karyawan.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>@endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Daftar Karyawan <span class="text-muted fw-normal" style="font-size:13px">({{ $karyawans->total() }} data)</span></div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th style="width:40px">#</th><th>Nama</th><th>Jabatan</th><th>Telepon</th><th class="text-end">Gaji Pokok</th><th>Tgl Masuk</th><th class="text-center">Status</th><th class="text-center" style="width:100px">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($karyawans as $i => $k)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px">{{ $karyawans->firstItem() + $i }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:50%;background:{{ $k->status === 'Aktif' ? 'linear-gradient(135deg,#6366f1,#8b5cf6)' : '#94a3b8' }};display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0">
                                    @if($k->photo)<img src="{{ asset('storage/'.$k->photo) }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover">@else{{ strtoupper(substr($k->nama,0,1)) }}@endif
                                </div>
                                <div><div style="font-weight:500;font-size:13.5px">{{ $k->nama }}</div></div>
                            </div>
                        </td>
                        <td style="font-size:13px">{{ $k->jabatan ?: '-' }}</td>
                        <td style="font-size:13px">{{ $k->telepon ?: '-' }}</td>
                        <td class="text-end" style="font-size:13px">{{ $k->gaji_pokok ? 'Rp '.number_format($k->gaji_pokok,0,',','.') : '-' }}</td>
                        <td style="font-size:13px">{{ $k->tgl_masuk ? \Carbon\Carbon::parse($k->tgl_masuk)->format('d M Y') : '-' }}</td>
                        <td class="text-center">
                            <span class="badge-status {{ $k->status === 'Aktif' ? 'badge-success' : 'badge-danger' }}">{{ $k->status }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('master.karyawan.edit', $k->id) }}" class="btn btn-sm" style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i class="fas fa-pen"></i></a>
                                <form method="POST" action="{{ route('master.karyawan.destroy', $k->id) }}" onsubmit="return confirm('Hapus karyawan {{ addslashes($k->nama) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-id-badge fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada data karyawan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($karyawans->hasPages())<div class="card-body border-top" style="padding:14px 20px">{{ $karyawans->links() }}</div>@endif
</div>
@endsection
