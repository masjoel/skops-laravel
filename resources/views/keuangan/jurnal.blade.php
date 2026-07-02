@extends('layouts.app')
@section('title', 'Jurnal Umum')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-journal-whills me-2" style="color:#6366f1"></i>Jurnal Umum</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('keuangan.index') }}">Keuangan</a></li><li class="breadcrumb-item active">Jurnal</li></ol>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari invoice / keterangan..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2"><input type="date" name="dari" class="form-control" value="{{ request('dari') }}"></div>
            <div class="col-6 col-md-2"><input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}"></div>
            <div class="col-auto">
                <button type="submit" class="btn btn-accent">Filter</button>
                @if(request()->hasAny(['search','dari','sampai']))<a href="{{ route('keuangan.jurnal') }}" class="btn btn-outline-secondary ms-1">Reset</a>@endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Daftar Jurnal <span class="text-muted fw-normal" style="font-size:13px">({{ $jurnals->total() }} entri)</span></div>
    <div class="card-body" style="padding:0">
        <table class="table table-hover mb-0">
            <thead><tr><th style="width:40px">#</th><th>Invoice</th><th>Tanggal</th><th>Keterangan</th><th class="text-center">Entri</th></tr></thead>
            <tbody>
                @forelse($jurnals as $i => $j)
                <tr>
                    <td style="color:var(--text-muted);font-size:12px">{{ $jurnals->firstItem()+$i }}</td>
                    <td style="font-family:monospace;font-size:13px;color:#6366f1;font-weight:600">{{ $j->invoice }}</td>
                    <td style="font-size:13px">{{ \Carbon\Carbon::parse($j->tanggal)->format('d M Y') }}</td>
                    <td style="font-size:13px">{{ $j->keterangan ?: '-' }}</td>
                    <td class="text-center"><span style="background:rgba(99,102,241,.1);color:#6366f1;padding:2px 10px;border-radius:20px;font-size:12px">{{ $j->detail->count() }} baris</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-journal-whills fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada jurnal</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jurnals->hasPages())<div class="card-body border-top" style="padding:14px 20px">{{ $jurnals->links() }}</div>@endif
</div>
@endsection
