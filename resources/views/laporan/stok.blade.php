@extends('layouts.app')
@section('title', 'Laporan Stok Barang')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-warehouse me-2" style="color:#6366f1"></i>Laporan Stok Barang</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Laporan Stok</li></ol>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary no-print"><i class="fas fa-print me-2"></i>Cetak</button>
</div>

<div class="card mb-3 no-print">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)<option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="kritis" class="form-select">
                    <option value="">Semua Stok</option>
                    <option value="1" {{ request('kritis') === '1' ? 'selected' : '' }}>Stok Kritis</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-accent">Tampilkan</button>
                @if(request()->hasAny(['kategori','kritis']))<a href="{{ route('laporan.stok') }}" class="btn btn-outline-secondary ms-1">Reset</a>@endif
            </div>
        </form>
    </div>
</div>

@php
    $totalNilai = $barangs->sum(fn($b) => $b->stok * $b->hrg_beli);
    $kritisCount = $barangs->filter(fn($b) => $b->stok <= $b->stok_kritis)->count();
@endphp

<div class="row g-3 mb-3 no-print">
    <div class="col-6 col-md-3"><div class="card" style="border-left:4px solid #6366f1"><div class="card-body" style="padding:14px 18px"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Total Item</div><div style="font-size:20px;font-weight:700;color:#6366f1">{{ $barangs->count() }}</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card" style="border-left:4px solid #ef4444"><div class="card-body" style="padding:14px 18px"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Stok Kritis</div><div style="font-size:20px;font-weight:700;color:#ef4444">{{ $kritisCount }}</div></div></div></div>
    <div class="col-12 col-md-4"><div class="card" style="border-left:4px solid #22c55e"><div class="card-body" style="padding:14px 18px"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Nilai Stok (Harga Beli)</div><div style="font-size:20px;font-weight:700;color:#22c55e">Rp {{ number_format($totalNilai,0,',','.') }}</div></div></div></div>
</div>

<div class="card">
    <div class="card-header">Laporan Stok <span class="text-muted fw-normal ms-2" style="font-size:13px">per {{ now()->isoFormat('D MMMM Y') }}</span></div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th style="width:40px">#</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Satuan</th><th>Lokasi</th><th class="text-end">Hrg Beli</th><th class="text-center">Stok</th><th class="text-center">Min Stok</th><th class="text-end">Nilai</th></tr></thead>
                <tbody>
                    @forelse($barangs as $i => $b)
                    <tr style="{{ $b->stok <= $b->stok_kritis ? 'background:rgba(239,68,68,.04)' : '' }}">
                        <td style="font-size:12px;color:var(--text-muted)">{{ $i+1 }}</td>
                        <td><span style="font-family:monospace;font-size:11px;background:rgba(99,102,241,.08);color:#6366f1;padding:2px 6px;border-radius:5px">{{ $b->kdbrg }}</span></td>
                        <td style="font-weight:500;font-size:13px">{{ $b->namabrg }}</td>
                        <td style="font-size:12px;color:var(--text-muted)">{{ $b->kategori?->nama ?? '-' }}</td>
                        <td style="font-size:12px">{{ $b->satuan?->nama ?? '-' }}</td>
                        <td style="font-size:12px;color:var(--text-muted)">{{ $b->lokasi?->nama ?? '-' }}</td>
                        <td class="text-end" style="font-size:13px">Rp {{ number_format($b->hrg_beli,0,',','.') }}</td>
                        <td class="text-center">
                            @if($b->stok <= $b->stok_kritis)
                                <span class="badge-status badge-danger">{{ $b->stok }}</span>
                            @else
                                <span class="badge-status badge-success">{{ $b->stok }}</span>
                            @endif
                        </td>
                        <td class="text-center" style="font-size:12px;color:var(--text-muted)">{{ $b->stok_kritis }}</td>
                        <td class="text-end" style="font-size:13px;font-weight:500">Rp {{ number_format($b->stok*$b->hrg_beli,0,',','.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-warehouse fa-2x mb-2 d-block" style="opacity:.2"></i>Tidak ada data stok</td></tr>
                    @endforelse
                </tbody>
                @if($barangs->isNotEmpty())
                <tfoot><tr style="background:rgba(99,102,241,.04)"><td colspan="9" class="text-end" style="font-weight:700">TOTAL NILAI STOK</td><td class="text-end" style="font-weight:800;color:#22c55e">Rp {{ number_format($totalNilai,0,',','.') }}</td></tr></tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
<style>@media print{.no-print,nav,aside,.sidebar{display:none!important;}.card{box-shadow:none;border:1px solid #ddd;}body{background:white;}}</style>
@endsection
