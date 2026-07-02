@extends('layouts.app')
@section('title', 'Penjualan')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-shopping-cart me-2" style="color:#6366f1"></i>Penjualan</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Penjualan</li></ol>
    </div>
    <a href="{{ route('penjualan.create') }}" class="btn btn-accent"><i class="fas fa-plus me-2"></i>Transaksi Baru</a>
</div>

<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nomor invoice..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}" placeholder="Dari tanggal">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}" placeholder="Sampai tanggal">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-accent">Filter</button>
                @if(request()->hasAny(['search','dari','sampai']))<a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>@endif
            </div>
        </form>
    </div>
</div>

@if(request()->hasAny(['dari','sampai']))
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="card" style="border-left:4px solid #22c55e">
            <div class="card-body" style="padding:14px 18px">
                <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Total Periode</div>
                <div style="font-size:20px;font-weight:700;color:#22c55e">Rp {{ number_format($totalPeriode,0,',','.') }}</div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">Daftar Transaksi Penjualan <span class="text-muted fw-normal" style="font-size:13px">({{ $penjualans->total() }} transaksi)</span></div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th style="width:40px">#</th><th>Invoice</th><th>Tanggal</th><th>Customer</th><th>Operator</th><th class="text-end">Total</th><th class="text-center" style="width:120px">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $i => $p)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px">{{ $penjualans->firstItem() + $i }}</td>
                        <td>
                            <a href="{{ route('penjualan.show', $p->invoice) }}" style="color:#6366f1;font-weight:700;font-size:13px;text-decoration:none;font-family:monospace">
                                {{ $p->invoice }}
                            </a>
                        </td>
                        <td style="font-size:13px">{{ \Carbon\Carbon::parse($p->tgl_inv)->format('d M Y') }}</td>
                        <td style="font-size:13px">{{ $p->anggota?->nama ?? '<span style="color:var(--text-muted)">Umum</span>' }}</td>
                        <td style="font-size:12px;color:var(--text-muted)">{{ $p->op ?? '-' }}</td>
                        <td class="text-end" style="font-weight:700;color:#22c55e">Rp {{ number_format($p->jml,0,',','.') }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('penjualan.show', $p->invoice) }}" class="btn btn-sm" style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px" title="Detail"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('penjualan.cetak', $p->invoice) }}" class="btn btn-sm" style="padding:4px 8px;background:rgba(34,197,94,.1);color:#16a34a;border-radius:6px" title="Cetak" target="_blank"><i class="fas fa-print"></i></a>
                                <a href="{{ route('penjualan.edit', $p->invoice) }}" class="btn btn-sm" style="padding:4px 8px;background:rgba(245,158,11,.1);color:#d97706;border-radius:6px" title="Edit"><i class="fas fa-pen"></i></a>
                                <form method="POST" action="{{ route('penjualan.destroy', $p->invoice) }}" onsubmit="return confirm('Hapus invoice {{ $p->invoice }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-shopping-cart fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada transaksi penjualan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($penjualans->hasPages())<div class="card-body border-top" style="padding:14px 20px">{{ $penjualans->links() }}</div>@endif
</div>
@endsection
