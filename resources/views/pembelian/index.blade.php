@extends('layouts.app')
@section('title', 'Pembelian')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-shopping-bag me-2" style="color:#6366f1"></i>Pembelian</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Pembelian</li></ol>
    </div>
    <a href="{{ route('pembelian.create') }}" class="btn btn-accent"><i class="fas fa-plus me-2"></i>Pembelian Baru</a>
</div>

<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari invoice..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="suplier" class="form-select">
                    <option value="">Semua Suplier</option>
                    @foreach($supliers as $s)
                        <option value="{{ $s->id }}" {{ request('suplier') == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-accent">Filter</button>
                @if(request()->hasAny(['search','dari','sampai','suplier']))<a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>@endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Daftar Pembelian <span class="text-muted fw-normal" style="font-size:13px">({{ $pembelians->total() }} transaksi)</span></div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th style="width:40px">#</th><th>Invoice</th><th>Tanggal</th><th>Suplier</th><th>Operator</th><th class="text-end">Total</th><th class="text-center" style="width:110px">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($pembelians as $i => $p)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px">{{ $pembelians->firstItem() + $i }}</td>
                        <td><a href="{{ route('pembelian.show', $p->invoice) }}" style="color:#6366f1;font-weight:700;font-size:13px;text-decoration:none;font-family:monospace">{{ $p->invoice }}</a></td>
                        <td style="font-size:13px">{{ \Carbon\Carbon::parse($p->tgl_inv)->format('d M Y') }}</td>
                        <td style="font-size:13px">{{ $p->suplier?->nama ?? '-' }}</td>
                        <td style="font-size:12px;color:var(--text-muted)">{{ $p->op ?? '-' }}</td>
                        <td class="text-end" style="font-weight:700;color:#f59e0b">Rp {{ number_format($p->jml,0,',','.') }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('pembelian.show', $p->invoice) }}" class="btn btn-sm" style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('pembelian.edit', $p->invoice) }}" class="btn btn-sm" style="padding:4px 8px;background:rgba(245,158,11,.1);color:#d97706;border-radius:6px"><i class="fas fa-pen"></i></a>
                                <form method="POST" action="{{ route('pembelian.destroy', $p->invoice) }}" onsubmit="return confirm('Hapus invoice {{ $p->invoice }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-shopping-bag fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada transaksi pembelian</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pembelians->hasPages())<div class="card-body border-top" style="padding:14px 20px">{{ $pembelians->links() }}</div>@endif
</div>
@endsection
