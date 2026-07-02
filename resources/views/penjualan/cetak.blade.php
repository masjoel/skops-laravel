<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur {{ $transaksi->invoice }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; background: #fff; }
        .page { max-width: 800px; margin: 0 auto; padding: 24px; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #111; }
        .company-name { font-size: 22px; font-weight: 800; color: #4f46e5; }
        .company-info { font-size: 11px; color: #555; margin-top: 4px; line-height: 1.6; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { font-size: 20px; font-weight: 700; color: #111; }
        .invoice-title .invoice-no { font-size: 14px; font-weight: 600; color: #4f46e5; margin-top: 4px; font-family: monospace; }

        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .meta-box { background: #f8f9fa; padding: 12px; border-radius: 6px; }
        .meta-box label { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #888; font-weight: 600; }
        .meta-box .val { font-size: 13px; font-weight: 600; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead th { background: #4f46e5; color: white; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; }
        thead th:last-child, thead th:nth-last-child(2), thead th:nth-last-child(3) { text-align: right; }
        tbody tr:nth-child(even) { background: #f8f8ff; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 12px; }
        tbody td:last-child, tbody td:nth-last-child(2), tbody td:nth-last-child(3) { text-align: right; }

        .total-section { display: flex; justify-content: flex-end; }
        .total-table { width: 280px; }
        .total-table tr td { padding: 4px 10px; font-size: 12px; }
        .total-table .grand-row td { border-top: 2px solid #111; font-size: 15px; font-weight: 800; padding-top: 8px; color: #4f46e5; }
        .total-table td:last-child { text-align: right; }

        .footer { margin-top: 40px; display: flex; justify-content: flex-end; }
        .sign-box { text-align: center; width: 180px; }
        .sign-box .sign-title { font-size: 11px; margin-bottom: 60px; }
        .sign-box .sign-name { border-top: 1px solid #111; padding-top: 6px; font-size: 12px; font-weight: 600; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="no-print" style="padding:12px 24px;background:#1e293b;display:flex;gap:8px">
    <button onclick="window.print()" style="background:#6366f1;color:white;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:13px"><i>🖨</i> Cetak</button>
    <a href="{{ route('penjualan.show', $transaksi->invoice) }}" style="background:#334155;color:white;border:none;padding:8px 20px;border-radius:6px;cursor:pointer;font-size:13px;text-decoration:none">← Kembali</a>
</div>

<div class="page">
    <div class="header">
        <div>
            <div class="company-name">{{ $perusahaan?->NamaClient ?? 'Superstore' }}</div>
            <div class="company-info">
                {{ $perusahaan?->Alamat ?? '' }}<br>
                Telp: {{ $perusahaan?->Telp ?? '-' }} | Email: {{ $perusahaan?->Email ?? '-' }}
            </div>
        </div>
        <div class="invoice-title">
            <h2>FAKTUR PENJUALAN</h2>
            <div class="invoice-no">{{ $transaksi->invoice }}</div>
        </div>
    </div>

    <div class="meta">
        <div class="meta-box">
            <label>Tanggal</label>
            <div class="val">{{ \Carbon\Carbon::parse($transaksi->tgl_inv)->isoFormat('D MMMM Y') }}</div>
        </div>
        <div class="meta-box">
            <label>Customer</label>
            <div class="val">{{ $transaksi->anggota?->nama ?? 'Umum' }}</div>
        </div>
        <div class="meta-box">
            <label>Operator</label>
            <div class="val">{{ $transaksi->op ?? '-' }}</div>
        </div>
        <div class="meta-box">
            <label>Keterangan</label>
            <div class="val">{{ $transaksi->ket ?: '-' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr><th>#</th><th>Nama Barang</th><th>Qty</th><th>Harga</th><th>Diskon</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
            @foreach($transaksi->detail as $i => $d)
            <tr>
                <td>{{ $i+1 }}</td>
                <td><strong>{{ $d->barang?->namabrg ?? '-' }}</strong></td>
                <td style="text-align:center">{{ number_format($d->qty,0) }}</td>
                <td>Rp {{ number_format($d->hrg,0,',','.') }}</td>
                <td>{{ $d->disc ? 'Rp '.number_format($d->disc,0,',','.') : '-' }}</td>
                <td>Rp {{ number_format($d->total,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table class="total-table">
            @if($transaksi->ongkir > 0)
            <tr><td>Ongkir</td><td>Rp {{ number_format($transaksi->ongkir,0,',','.') }}</td></tr>
            @endif
            <tr class="grand-row"><td>TOTAL</td><td>Rp {{ number_format($transaksi->jml,0,',','.') }}</td></tr>
        </table>
    </div>

    <div class="footer">
        <div class="sign-box">
            <div class="sign-title">Hormat kami,</div>
            <div class="sign-name">{{ $transaksi->op ?? '_______________' }}</div>
        </div>
    </div>
</div>
</body>
</html>
