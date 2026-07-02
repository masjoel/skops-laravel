@extends('layouts.app')
@section('title', 'Keuangan')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-landmark me-2" style="color:#6366f1"></i>Keuangan</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Keuangan</li></ol>
    </div>
</div>

<div class="row g-3 mb-4">
    @php $cards = [
        ['Pendapatan '.$tahun, $totalPend, '#22c55e', 'fa-arrow-trend-up'],
        ['Biaya '.$tahun, $totalBiaya, '#ef4444', 'fa-arrow-trend-down'],
        ['Laba Bersih', $labaBersih, $labaBersih >= 0 ? '#6366f1' : '#ef4444', 'fa-scale-balanced'],
    ]; @endphp
    @foreach($cards as [$label,$val,$color,$icon])
    <div class="col-12 col-md-4">
        <div class="stat-card" style="border-left:4px solid {{ $color }}">
            <div class="stat-icon" style="background:{{ $color }}20"><i class="fas {{ $icon }}" style="color:{{ $color }}"></i></div>
            <div class="stat-info">
                <div class="stat-label">{{ $label }}</div>
                <div class="stat-value" style="color:{{ $color }}">Rp {{ number_format(abs($val),0,',','.') }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-12 col-md-4">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-book me-2" style="color:#6366f1"></i>Menu Keuangan</div>
            <div class="card-body" style="padding:8px">
                @php $menus = [
                    ['route'=>'keuangan.jurnal','icon'=>'fa-journal-whills','label'=>'Jurnal Umum','color'=>'#6366f1'],
                    ['route'=>'keuangan.coa','icon'=>'fa-list-ol','label'=>'Chart of Accounts','color'=>'#8b5cf6'],
                ]; @endphp
                @foreach($menus as $m)
                <a href="{{ route($m['route']) }}" class="d-flex align-items-center gap-3 p-3 rounded mb-1" style="background:rgba(99,102,241,.04);text-decoration:none;transition:background .2s" onmouseover="this.style.background='rgba(99,102,241,.1)'" onmouseout="this.style.background='rgba(99,102,241,.04)'">
                    <div style="width:36px;height:36px;background:{{ $m['color'] }}20;border-radius:8px;display:flex;align-items:center;justify-content:center">
                        <i class="fas {{ $m['icon'] }}" style="color:{{ $m['color'] }}"></i>
                    </div>
                    <span style="font-weight:500;color:var(--text-primary)">{{ $m['label'] }}</span>
                    <i class="fas fa-chevron-right ms-auto" style="color:var(--text-muted);font-size:11px"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-clock-rotate-left me-2" style="color:#6366f1"></i>Jurnal Terbaru</div>
            <div class="card-body" style="padding:0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Invoice</th><th>Tanggal</th><th>Keterangan</th></tr></thead>
                    <tbody>
                        @forelse($jurnalTerbaru as $j)
                        <tr>
                            <td style="font-family:monospace;font-size:12px;color:#6366f1">{{ $j->invoice }}</td>
                            <td style="font-size:13px">{{ \Carbon\Carbon::parse($j->tanggal)->format('d M Y') }}</td>
                            <td style="font-size:13px">{{ $j->keterangan ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center" style="padding:32px;color:var(--text-muted)">Belum ada jurnal</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($jurnalTerbaru->isNotEmpty())
            <div class="card-body border-top" style="padding:10px 16px"><a href="{{ route('keuangan.jurnal') }}" style="font-size:13px;color:#6366f1;text-decoration:none">Lihat semua jurnal →</a></div>
            @endif
        </div>
    </div>
</div>
@endsection
