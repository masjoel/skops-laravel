@extends('layouts.app')
@section('title', 'Chart of Accounts')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-list-ol me-2" style="color:#6366f1"></i>Chart of Accounts</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('keuangan.index') }}">Keuangan</a></li><li class="breadcrumb-item active">COA</li></ol>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="d-flex gap-2">
            <div class="input-group" style="max-width:360px">
                <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Cari nama / kode akun..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-accent">Cari</button>
            @if(request('search'))<a href="{{ route('keuangan.coa') }}" class="btn btn-outline-secondary">Reset</a>@endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Daftar Akun <span class="text-muted fw-normal" style="font-size:13px">({{ $accounts->total() }} akun)</span></div>
    <div class="card-body" style="padding:0">
        <table class="table table-hover mb-0">
            <thead><tr><th style="width:120px">Kode</th><th>Nama Akun</th><th>Tipe</th><th>Kategori</th><th class="text-end">Saldo</th></tr></thead>
            <tbody>
                @forelse($accounts as $acc)
                <tr>
                    <td><span style="font-family:monospace;font-size:12px;background:rgba(99,102,241,.08);color:#6366f1;padding:2px 8px;border-radius:6px">{{ $acc->kode ?? $acc->id }}</span></td>
                    <td style="font-weight:500">{{ $acc->nama }}</td>
                    <td style="font-size:13px">{{ $acc->tipe ?? '-' }}</td>
                    <td style="font-size:13px">{{ $acc->kategori ?? '-' }}</td>
                    <td class="text-end" style="font-size:13px">{{ $acc->saldo !== null ? 'Rp '.number_format($acc->saldo,0,',','.') : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-list-ol fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada akun</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($accounts->hasPages())<div class="card-body border-top" style="padding:14px 20px">{{ $accounts->links() }}</div>@endif
</div>
@endsection
