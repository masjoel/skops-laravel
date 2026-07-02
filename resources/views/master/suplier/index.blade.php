@extends('layouts.app')
@section('title', 'Data Suplier')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-truck me-2" style="color:#6366f1"></i>Data Suplier</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Suplier</li></ol>
    </div>
    <a href="{{ route('master.suplier.create') }}" class="btn btn-accent"><i class="fas fa-plus me-2"></i>Tambah Suplier</a>
</div>

<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, kota, telepon..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-accent">Filter</button>
                @if(request('search'))<a href="{{ route('master.suplier.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>@endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Daftar Suplier <span class="text-muted fw-normal" style="font-size:13px">({{ $supliers->total() }} data)</span></div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th style="width:40px">#</th><th>Nama Suplier</th><th>Kota</th><th>Telepon</th><th>Email</th><th>Kontak</th><th class="text-center">Barang</th><th class="text-center" style="width:100px">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($supliers as $i => $s)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px">{{ $supliers->firstItem() + $i }}</td>
                        <td><div style="font-weight:500">{{ $s->nama }}</div><div style="font-size:11px;color:var(--text-muted)">{{ $s->alamat ?: '' }}</div></td>
                        <td style="font-size:13px">{{ $s->kota ?: '-' }}</td>
                        <td style="font-size:13px">{{ $s->telepon ?: '-' }}</td>
                        <td style="font-size:13px">{{ $s->email ?: '-' }}</td>
                        <td style="font-size:13px">{{ $s->kontak ?: '-' }}</td>
                        <td class="text-center"><span style="background:rgba(99,102,241,.1);color:#6366f1;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600">{{ $s->barang_count }}</span></td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('master.suplier.edit', $s->id) }}" class="btn btn-sm" style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i class="fas fa-pen"></i></a>
                                <form method="POST" action="{{ route('master.suplier.destroy', $s->id) }}" onsubmit="return confirm('Hapus suplier {{ addslashes($s->nama) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-truck fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada data suplier</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($supliers->hasPages())<div class="card-body border-top" style="padding:14px 20px">{{ $supliers->links() }}</div>@endif
</div>
@endsection
