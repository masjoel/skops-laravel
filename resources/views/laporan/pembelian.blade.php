@extends('layouts.app')
@section('title', 'Laporan Pembelian')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-bar me-2" style="color:#6366f1"></i>Laporan Pembelian</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Laporan Pembelian</li></ol>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary no-print"><i class="fas fa-print me-2"></i>Cetak</button>
</div>

<div class="card mb-3 no-print">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3"><label class="form-label" style="font-size:12px;color:var(--text-muted)">Dari Tanggal</label><input type="date" name="dari" class="form-control" value="{{ $dari }}"></div>
            <div class="col-6 col-md-3"><label class="form-label" style="font-size:12px;color:var(--text-muted)">Sampai Tanggal</label><input type="date" name="sampai" class="form-control" value="{{ $sampai }}"></div>
            <div class="col-auto"><button type="submit" class="btn btn-accent mt-3">Tampilkan</button></div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3 no-print">
    <div class="col-6 col-md-3"><div class="card" style="border-left:4px solid #f59e0b"><div class="card-body" style="padding:14px 18px"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Total Pembelian</div><div style="font-size:20px;font-weight:700;color:#f59e0b">Rp {{ number_format($total,0,',','.') }}</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card" style="border-left:4px solid #6366f1"><div class="card-body" style="padding:14px 18px"><div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Jumlah Transaksi</div><div style="font-size:20px;font-weight:700;color:#6366f1">{{ $jumlahTrx }}</div></div></div></div>
</div>

<div class="card">
    <div class="card-header">Laporan Pembelian <span class="text-muted fw-normal ms-2" style="font-size:13px">{{ \Carbon\Carbon::parse($dari)->isoFormat('D MMM Y') }} — {{ \Carbon\Carbon::parse($sampai)->isoFormat('D MMM Y') }}</span></div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th style="width:40px">#</th><th>Invoice</th><th>Tanggal</th><th>Suplier</th><th>Operator</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                    @forelse($transaksis as $i => $t)
                    <tr>
                        <td style="font-size:12px;color:var(--text-muted)">{{ $i+1 }}</td>
                        <td><a href="{{ route('pembelian.show',$t->invoice) }}" style="color:#6366f1;font-family:monospace;font-size:13px;text-decoration:none;font-weight:600">{{ $t->invoice }}</a></td>
                        <td style="font-size:13px">{{ \Carbon\Carbon::parse($t->tgl_inv)->format('d M Y') }}</td>
                        <td style="font-size:13px">{{ $t->suplier?->nama ?? '-' }}</td>
                        <td style="font-size:12px;color:var(--text-muted)">{{ $t->op }}</td>
                        <td class="text-end" style="font-weight:600;color:#f59e0b">Rp {{ number_format($t->jml,0,',','.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-shopping-bag fa-2x mb-2 d-block" style="opacity:.2"></i>Tidak ada data pembelian</td></tr>
                    @endforelse
                </tbody>
                @if($transaksis->isNotEmpty())
                <tfoot><tr style="background:rgba(99,102,241,.04)"><td colspan="5" class="text-end" style="font-weight:700;font-size:14px">TOTAL</td><td class="text-end" style="font-weight:800;font-size:16px;color:#f59e0b">Rp {{ number_format($total,0,',','.') }}</td></tr></tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
<style>@media print{.no-print,nav,aside,.sidebar{display:none!important;}.card{box-shadow:none;border:1px solid #ddd;}body{background:white;}}</style>
@endsection
