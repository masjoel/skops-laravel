@extends('layouts.app')
@section('title', 'Detail Penjualan — '.$transaksi->invoice)
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-receipt me-2" style="color:#6366f1"></i>Detail Penjualan</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('penjualan.index') }}">Penjualan</a></li><li class="breadcrumb-item active">{{ $transaksi->invoice }}</li></ol>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('penjualan.cetak', $transaksi->invoice) }}" class="btn btn-outline-success" target="_blank"><i class="fas fa-print me-2"></i>Cetak</a>
        <a href="{{ route('penjualan.edit', $transaksi->invoice) }}" class="btn btn-accent"><i class="fas fa-pen me-2"></i>Edit</a>
        <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-list me-2" style="color:#6366f1"></i>Detail Item</div>
            <div class="card-body" style="padding:0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr><th>#</th><th>Barang</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end">Diskon</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($transaksi->detail as $i => $d)
                            <tr>
                                <td style="color:var(--text-muted);font-size:12px">{{ $i+1 }}</td>
                                <td>
                                    <div style="font-weight:500">{{ $d->barang?->namabrg ?? '-' }}</div>
                                    <div style="font-size:11px;color:var(--text-muted)">{{ $d->barang?->kdbrg }}</div>
                                </td>
                                <td class="text-center">{{ number_format($d->qty,0) }}</td>
                                <td class="text-end">Rp {{ number_format($d->hrg,0,',','.') }}</td>
                                <td class="text-end" style="color:#ef4444">{{ $d->disc ? '- Rp '.number_format($d->disc,0,',','.') : '-' }}</td>
                                <td class="text-end" style="font-weight:600;color:#22c55e">Rp {{ number_format($d->total,0,',','.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @if($transaksi->ongkir > 0)
                            <tr><td colspan="5" class="text-end" style="font-size:13px;color:var(--text-muted)">Ongkir</td><td class="text-end">Rp {{ number_format($transaksi->ongkir,0,',','.') }}</td></tr>
                            @endif
                            <tr style="background:rgba(99,102,241,.04)">
                                <td colspan="5" class="text-end" style="font-weight:700;font-size:15px">TOTAL</td>
                                <td class="text-end" style="font-weight:800;font-size:17px;color:#22c55e">Rp {{ number_format($transaksi->jml,0,',','.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-info-circle me-2" style="color:#6366f1"></i>Informasi Transaksi</div>
            <div class="card-body">
                @php $rows = [
                    ['Invoice', $transaksi->invoice],
                    ['Tanggal', \Carbon\Carbon::parse($transaksi->tgl_inv)->isoFormat('D MMMM Y')],
                    ['Customer', $transaksi->anggota?->nama ?? 'Umum'],
                    ['Operator', $transaksi->op ?? '-'],
                    ['Keterangan', $transaksi->ket ?: '-'],
                ]; @endphp
                @foreach($rows as [$label, $val])
                <div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--border-color)">
                    <span style="font-size:13px;color:var(--text-muted)">{{ $label }}</span>
                    <span style="font-size:13px;font-weight:500;text-align:right;max-width:60%">{{ $val }}</span>
                </div>
                @endforeach
                <div class="mt-3 p-3" style="background:rgba(34,197,94,.08);border-radius:8px;text-align:center">
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">Grand Total</div>
                    <div style="font-size:24px;font-weight:800;color:#22c55e">Rp {{ number_format($transaksi->jml,0,',','.') }}</div>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <form method="POST" action="{{ route('penjualan.destroy', $transaksi->invoice) }}" onsubmit="return confirm('Hapus transaksi ini? Tindakan tidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100"><i class="fas fa-trash me-2"></i>Hapus Transaksi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
