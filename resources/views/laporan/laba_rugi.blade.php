@extends('layouts.app')
@section('title', 'Laporan Laba Rugi')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-scale-balanced me-2" style="color:#6366f1"></i>Laporan Laba Rugi</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Laba Rugi</li></ol>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary no-print"><i class="fas fa-print me-2"></i>Cetak</button>
</div>

<div class="card mb-3 no-print">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="d-flex gap-2 align-items-end">
            <div>
                <label class="form-label" style="font-size:12px;color:var(--text-muted)">Tahun</label>
                <select name="tahun" class="form-select" style="width:120px">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                    @if($years->isEmpty())
                        <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                    @endif
                </select>
            </div>
            <button type="submit" class="btn btn-accent">Tampilkan</button>
        </form>
    </div>
</div>

<div class="row g-3 mb-4 no-print">
    @php $kpiCards = [['Pendapatan', $totalPend, '#22c55e', 'fa-arrow-trend-up'], ['Total Biaya', $totalBiaya, '#ef4444', 'fa-arrow-trend-down'], ['Laba Bersih', $laba, $laba>=0?'#6366f1':'#ef4444', 'fa-scale-balanced']]; @endphp
    @foreach($kpiCards as [$label, $val, $color, $icon])
    <div class="col-12 col-md-4">
        <div class="card" style="border-left:4px solid {{ $color }}">
            <div class="card-body" style="padding:16px 20px">
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px">{{ $label }} Tahun {{ $tahun }}</div>
                <div style="font-size:22px;font-weight:800;color:{{ $color }};margin-top:4px">Rp {{ number_format(abs($val),0,',','.') }}</div>
                @if($label==='Laba Bersih')<div style="font-size:12px;color:{{ $color }}">{{ $laba>=0 ? '▲ Untung' : '▼ Rugi' }}</div>@endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-chart-bar me-2" style="color:#6366f1"></i>Grafik Pendapatan vs Biaya — {{ $tahun }}</div>
            <div class="card-body"><canvas id="labaChart" height="80"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-table me-2" style="color:#6366f1"></i>Rincian Per Bulan</div>
            <div class="card-body" style="padding:0">
                <table class="table mb-0">
                    <thead><tr><th>Bulan</th><th class="text-end">Pendapatan</th><th class="text-end">Biaya</th><th class="text-end">Laba</th></tr></thead>
                    <tbody>
                        @php $bulanNames = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']; @endphp
                        @forelse($data as $d)
                        @php $lbBln = $d->pendapatan - $d->biaya; @endphp
                        <tr>
                            <td style="font-size:13px;font-weight:500">{{ $bulanNames[$d->bulan] }}</td>
                            <td class="text-end" style="font-size:12px;color:#22c55e">{{ number_format($d->pendapatan/1000,0,',','.').'k' }}</td>
                            <td class="text-end" style="font-size:12px;color:#ef4444">{{ number_format($d->biaya/1000,0,',','.').'k' }}</td>
                            <td class="text-end" style="font-size:12px;font-weight:600;color:{{ $lbBln>=0?'#6366f1':'#ef4444' }}">{{ number_format($lbBln/1000,0,',','.').'k' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center" style="padding:24px;color:var(--text-muted)">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                    @if($data->isNotEmpty())
                    <tfoot>
                        <tr style="background:rgba(99,102,241,.04)">
                            <td style="font-weight:700;font-size:13px">TOTAL</td>
                            <td class="text-end" style="font-weight:700;color:#22c55e;font-size:13px">Rp {{ number_format($totalPend,0,',','.') }}</td>
                            <td class="text-end" style="font-weight:700;color:#ef4444;font-size:13px">Rp {{ number_format($totalBiaya,0,',','.') }}</td>
                            <td class="text-end" style="font-weight:800;font-size:13px;color:{{ $laba>=0?'#6366f1':'#ef4444' }}">Rp {{ number_format($laba,0,',','.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<style>@media print{.no-print,nav,aside,.sidebar{display:none!important;}.card{box-shadow:none;border:1px solid #ddd;}body{background:white;}}</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels  = @json($data->pluck('bulan')->map(fn($b) => ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][$b]));
const pend    = @json($data->pluck('pendapatan'));
const biaya   = @json($data->pluck('biaya'));

new Chart(document.getElementById('labaChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Pendapatan', data: pend, backgroundColor: 'rgba(34,197,94,.7)', borderRadius: 4 },
            { label: 'Biaya', data: biaya, backgroundColor: 'rgba(239,68,68,.5)', borderRadius: 4 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { ticks: { callback: v => 'Rp '+Number(v).toLocaleString('id-ID') }, grid: { color: 'rgba(255,255,255,.05)' } }
        }
    }
});
</script>
@endpush
@endsection
