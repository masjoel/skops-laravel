@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-chart-pie me-2" style="color:#6366f1"></i>Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Beranda</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary"
                style="font-size:12px;padding:6px 12px;border-radius:20px">
                <i class="fas fa-calendar-alt me-1"></i>{{ now()->isoFormat('D MMMM Y') }}
            </span>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="row g-3 mb-4">
        {{-- Total Barang --}}
        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #6366f1">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Siswa</div>
                            <div class="stat-value text-primary" id="val-barang">{{ number_format($totalBarang) }}</div>
                            <div class="stat-sub">terdaftar</div>
                        </div>
                        <div class="stat-icon" style="background:rgba(99,102,241,.12);color:#6366f1">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #7a5b23">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Guru</div>
                            <div class="stat-value text-primary" id="val-jual">
                                {{ number_format($totalPenjualan, 0, ',', '.') }}
                            </div>
                            <div class="stat-sub">terdaftar</div>
                        </div>
                        <div class="stat-icon" style="background:rgba(34,197,94,.12);color:#7a5b23">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #f59e0b">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Jenis Poin</div>
                            <div class="stat-value" style="color:#f59e0b" id="val-beli">
                                {{ number_format($totalPembelian, 0, ',', '.') }}
                            </div>
                            <div class="stat-sub">item</div>
                        </div>
                        <div class="stat-icon" style="background:rgba(245,158,11,.12);color:#f59e0b">
                            <i class="fas fa-list-ol"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #22c55e">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Reward</div>
                            <div class="stat-value text-success" id="val-jual">
                                {{ number_format($totalPenjualan, 0, ',', '.') }}
                            </div>
                            <div class="stat-sub text-success">poin</div>
                        </div>
                        <div class="stat-icon" style="background:rgba(34,197,94,.12);color:#22c55e">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #63b6f1">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Pemutihan</div>
                            <div class="stat-value text-info" id="val-barang">{{ number_format($totalBarang) }}</div>
                            <div class="stat-sub text-info">poin</div>
                        </div>
                        <div class="stat-icon" style="background:rgba(99,102,241,.12);color:#63b6f1">
                            <i class="fas fa-recycle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stok Kritis --}}
        <div class="col-6 col-xl-4">
            <div class="card stat-card" style="border-left:4px solid #ef4444">
                <div class="card-body" style="padding:18px">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="stat-label">Pelanggaran</div>
                            <div class="stat-value text-danger" id="val-kritis">{{ number_format(17500000) }}</div>
                            <div class="stat-sub text-danger">
                                poin
                            </div>
                        </div>
                        <div class="stat-icon" style="background:rgba(239,68,68,.12);color:#ef4444">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Charts Row ── --}}
    <div class="row g-3 mb-4">
        {{-- Omset Chart --}}
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-chart-area me-2" style="color:#6366f1"></i>
                        Grafik Reward & Pelanggaran
                        <span class="text-muted" style="font-size:12px;font-weight:400"> — Tahun {{ date('Y') }}</span>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <span style="font-size:11px;color:var(--text-muted)">
                            <span
                                style="display:inline-block;width:10px;height:10px;background:#6366f1;border-radius:50%;margin-right:4px"></span>Reward
                        </span>
                        <span style="font-size:11px;color:var(--text-muted)">
                            <span
                                style="display:inline-block;width:10px;height:10px;background:#f59e0b;border-radius:50%;margin-right:4px"></span>Pelanggaran
                        </span>
                    </div>
                </div>
                <div class="card-body" style="padding:20px">
                    <canvas id="omsetChart" height="110"></canvas>
                </div>
            </div>
        </div>

        {{-- Ringkasan Laba --}}
        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-donut me-2" style="color:#6366f1"></i>
                    Ringkasan Point
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center" style="padding:24px">
                    <canvas id="donutChart" width="180" height="180"></canvas>
                    <div class="mt-4 w-100">
                        {{-- @php
                        $totalPend = $chartData->sum('pendapatan');
                        $totalBiaya = $chartData->sum('biaya');
                        $labaBersih = $totalPend - $totalBiaya;
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size:13px;color:var(--text-muted)"><i class="fas fa-arrow-up me-1" style="color:#22c55e"></i>Total Pendapatan</span>
                        <strong style="font-size:13px;color:#22c55e">Rp {{ number_format($totalPend, 0, ',', '.') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span style="font-size:13px;color:var(--text-muted)"><i class="fas fa-arrow-down me-1" style="color:#ef4444"></i>Total Biaya</span>
                        <strong style="font-size:13px;color:#ef4444">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</strong>
                    </div>
                    <hr style="border-color:var(--border-color);margin:10px 0">
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-size:14px;font-weight:600">Laba Bersih</span>
                        <strong style="font-size:14px;color:{{ $labaBersih >= 0 ? '#22c55e' : '#ef4444' }}">
                            Rp {{ number_format($labaBersih, 0, ',', '.') }}
                        </strong>
                    </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Bottom Tables ── --}}
    <div class="row g-3">
        <div class="col-12 col-xl-5">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="text-primary"><i class="fas fa-receipt me-2 text-primary"></i>10 Siswa dengan poin tertinggi</div>
                    <a href="{{ route('master.murid.index') }}" class="btn btn-sm btn-primary"
                        style="font-size:12px;padding:4px 12px">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body" style="padding:0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiTerbaru as $trx)
                                    <tr>
                                        <td>
                                            <a href="{{ route('master.murid.show', $trx->invoice) }}"
                                                style="color:#6366f1;font-weight:600;font-size:12.5px;text-decoration:none">
                                                {{ $trx->invoice }}
                                            </a>
                                        </td>
                                        <td style="font-size:13px;color:var(--text-muted)">
                                            {{ \Carbon\Carbon::parse($trx->tgl_inv)->format('d M Y') }}
                                        </td>
                                        <td style="font-size:13px">
                                            {{ $trx->anggota?->nama ?? '-' }}
                                        </td>
                                        <td class="text-end" style="font-weight:600;font-size:13px;color:#22c55e">
                                            Rp {{ number_format($trx->jml, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center"
                                            style="padding:32px;color:var(--text-muted);font-size:13px">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
                                            Belum ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>10 Pelanggaran/Reward terbanyak
                    </div>
                    <a href="{{ route('master.jenis-poin.index') }}" class="btn btn-sm btn-outline-danger"
                        style="font-size:12px;padding:4px 12px;border-radius:8px">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body" style="padding:0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Deskripsi</th>
                                    <th>Jenis</th>
                                    <th class="text-center">Skor</th>
                                    <th class="text-center">Jumlah Siswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stokKritis as $brg)
                                    <tr>
                                        <td>
                                            <div style="font-size:13px;font-weight:500">{{ $brg->namabrg }}</div>
                                            <div style="font-size:11px;color:var(--text-muted)">
                                                {{ $brg->kategori?->nama ?? '-' }}
                                                · {{ $brg->satuan?->nama ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-status badge-danger">{{ $brg->stok }}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color:var(--text-muted)">
                                            {{ $brg->stok_kritis }}
                                        </td>
                                        <td class="text-center" style="font-size:13px;color:var(--text-muted)">
                                            {{ $brg->stok_kritis }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center"
                                            style="padding:32px;color:var(--text-muted);font-size:13px">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block text-success"
                                                style="opacity:.5"></i>
                                            Belum ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="text-warning"><i class="fas fa-list-ol me-2 text-warning"></i>Jenis Reward/Pelanggaran</div>
                    <a href="{{ route('master.murid.index') }}" class="btn btn-sm btn-warning"
                        style="font-size:12px;padding:4px 12px">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body" style="padding:0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Deskripsi</th>
                                    <th>Jenis</th>
                                    <th class="text-end">Skor</th>
                                    <th>Keterangan</th>
                                    <th>Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiTerbaru as $trx)
                                    <tr>
                                        <td>
                                            <a href="{{ route('master.murid.show', $trx->invoice) }}"
                                                style="color:#f59e0b;font-weight:600;font-size:12.5px;text-decoration:none">
                                                {{ $trx->invoice }}
                                            </a>
                                        </td>
                                        <td style="font-size:13px;color:var(--text-muted)">
                                            {{ \Carbon\Carbon::parse($trx->tgl_inv)->format('d M Y') }}
                                        </td>
                                        <td style="font-size:13px">
                                            {{ $trx->anggota?->nama ?? '-' }}
                                        </td>
                                        <td class="text-end" style="font-weight:600;font-size:13px;color:#22c55e">
                                            Rp {{ number_format($trx->jml, 0, ',', '.') }}
                                        </td>
                                        <td style="font-size:13px">
                                            {{ $trx->anggota?->nama ?? '-' }}
                                        </td>
                                        <td style="font-size:13px">
                                            {{ $trx->anggota?->nama ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center"
                                            style="padding:32px;color:var(--text-muted);font-size:13px">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
                                            Belum ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stat-card {
            border-radius: 12px;
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            /* text-transform: uppercase; */
            letter-spacing: .5px;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .stat-sub {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const isDark = () => document.documentElement.getAttribute('data-theme') === 'dark';
            const gridColor = () => isDark() ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';
            const labelColor = () => isDark() ? '#94a3b8' : '#64748b';

            // ── Chart Data dari Laravel ──
            const chartData = @json($chartData);
            const labels = chartData.map(d => d.namabulan || 'Bln ' + d.bulan);
            const pendData = chartData.map(d => parseFloat(d.pendapatan) || 0);
            const biayaData = chartData.map(d => parseFloat(d.biaya) || 0);

            // ── Omset Area Chart ──
            const omsetCtx = document.getElementById('omsetChart').getContext('2d');

            const gradPend = omsetCtx.createLinearGradient(0, 0, 0, 300);
            gradPend.addColorStop(0, 'rgba(99,102,241,.35)');
            gradPend.addColorStop(1, 'rgba(99,102,241,0)');

            const gradBiaya = omsetCtx.createLinearGradient(0, 0, 0, 300);
            gradBiaya.addColorStop(0, 'rgba(245,158,11,.25)');
            gradBiaya.addColorStop(1, 'rgba(245,158,11,0)');

            const omsetChart = new Chart(omsetCtx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                            label: 'Pendapatan',
                            data: pendData,
                            borderColor: '#6366f1',
                            backgroundColor: gradPend,
                            borderWidth: 2.5,
                            pointBackgroundColor: '#6366f1',
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            fill: true,
                            tension: 0.4,
                        },
                        {
                            label: 'Biaya',
                            data: biayaData,
                            borderColor: '#f59e0b',
                            backgroundColor: gradBiaya,
                            borderWidth: 2.5,
                            pointBackgroundColor: '#f59e0b',
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            fill: true,
                            tension: 0.4,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#94a3b8',
                            bodyColor: '#f8fafc',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: ctx => ' ' + ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString(
                                    'id-ID'),
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor(),
                                drawBorder: false
                            },
                            ticks: {
                                color: labelColor(),
                                font: {
                                    size: 11
                                }
                            },
                        },
                        y: {
                            grid: {
                                color: gridColor(),
                                drawBorder: false
                            },
                            ticks: {
                                color: labelColor(),
                                font: {
                                    size: 11
                                },
                                callback: v => 'Rp ' + (v >= 1e6 ? (v / 1e6).toFixed(1) + 'jt' : v
                                    .toLocaleString('id-ID')),
                            },
                        },
                    },
                },
            });

            // ── Donut Chart ──
            const donutCtx = document.getElementById('donutChart').getContext('2d');
            const totalPend = pendData.reduce((a, b) => a + b, 0);
            const totalBiaya = biayaData.reduce((a, b) => a + b, 0);

            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pendapatan', 'Biaya'],
                    datasets: [{
                        data: [totalPend || 1, totalBiaya || 0],
                        backgroundColor: ['#6366f1', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 6,
                    }],
                },
                options: {
                    responsive: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#94a3b8',
                            bodyColor: '#f8fafc',
                            callbacks: {
                                label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                            },
                        },
                    },
                },
            });

            // Redraw charts on theme change
            const themeBtn = document.getElementById('theme-toggle');
            if (themeBtn) {
                themeBtn.addEventListener('click', () => {
                    setTimeout(() => omsetChart.update(), 100);
                });
            }
        });
    </script>
@endpush
