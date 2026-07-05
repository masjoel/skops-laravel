@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
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
                            <div class="stat-value text-primary" id="val-barang">{{ number_format($totalMurid) }}</div>
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
                                {{ number_format($totalGuru, 0, ',', '.') }}
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
                                {{ number_format($totalJenisPoin, 0, ',', '.') }}
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
                                {{ number_format($totalReward, 0, ',', '.') }}
                            </div>
                            <div class="stat-sub text-success">{{ number_format($skorReward, 0, ',', '.') }} poin</div>
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
                            <div class="stat-value text-info" id="val-barang">
                                {{ number_format($totalPemutihan, 0, ',', '.') }}</div>
                            <div class="stat-sub text-info">{{ number_format($skorPemutihan, 0, ',', '.') }} poin</div>
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
                            <div class="stat-value text-danger" id="val-kritis">
                                {{ number_format($totalPelanggaran, 0, ',', '.') }}</div>
                            <div class="stat-sub text-danger">
                                {{ number_format($skorPelanggaran, 0, ',', '.') }} poin
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
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
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
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size:13px;color:var(--text-muted)"><i class="fas fa-star me-1"
                                    style="color:#22c55e"></i>Skor Reward</span>
                            <strong
                                style="font-size:13px;color:#22c55e">{{ number_format($skorReward, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size:13px;color:var(--text-muted)"><i
                                    class="fas fa-exclamation-triangle me-1" style="color:#ef4444"></i>Skor
                                Pelanggaran</span>
                            <strong
                                style="font-size:13px;color:#ef4444">{{ number_format($skorPelanggaran, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size:13px;color:var(--text-muted)"><i class="fas fa-recycle me-1"
                                    style="color:#63b6f1"></i>Skor Pemutihan</span>
                            <strong
                                style="font-size:13px;color:#63b6f1">{{ number_format($skorPemutihan, 0, ',', '.') }}</strong>
                        </div>
                        <hr style="border-color:var(--border-color);margin:10px 0">
                        @php
                            $totalSkorNet = $skorReward - $skorPelanggaran + $skorPemutihan;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size:14px;font-weight:600">Net Skor Total</span>
                            <strong style="font-size:14px;color:{{ $totalSkorNet >= 0 ? '#22c55e' : '#ef4444' }}">
                                {{ number_format($totalSkorNet, 0, ',', '.') }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Bottom Tables ── --}}
    <div class="row g-3">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <div class="text-primary"><i class="fas fa-receipt me-2 mb-2 text-primary"></i>10 Siswa dengan poin
                        tertinggi</div>
                    <a href="{{ route('laporan.rekapitulasi') }}" class="btn btn-sm btn-primary"
                        style="font-size:12px;padding:4px 12px">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body" style="padding:0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th class="text-center">Pelanggaran</th>
                                    <th class="text-center">Reward</th>
                                    <th class="text-end">Total Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswaTertinggi as $index => $siswa)
                                    <tr>
                                        <td>
                                            {{ $siswa->muridKelas?->murid?->personil?->nama ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $siswa->muridKelas?->kelas?->nama_kelas ?? '-' }}
                                        </td>
                                        <td class="text-center text-danger">
                                            {{ $siswa->total_pelanggaran ?: '0' }}
                                        </td>
                                        <td class="text-center text-success">
                                            {{ $siswa->total_reward ?: '0' }}
                                        </td>
                                        <td class="text-end" style="font-weight:600;">
                                            {{ $siswa->total_reward + $siswa->total_pelanggaran }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center"
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

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle me-2 mb-2 text-danger"></i>10 Pelanggaran/Reward terbanyak
                    </div>
                    <a href="{{ route('transaksi.kartu-kontrol.index') }}" class="btn btn-sm btn-outline-danger"
                        style="font-size:12px;padding:4px 12px;border-radius:8px">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body" style="padding:0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Deskripsi</th>
                                    <th class="text-center">Jenis</th>
                                    <th class="text-center">Skor</th>
                                    <th class="text-center">Jumlah Siswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($poinTerbanyak as $poin)
                                    <tr>
                                        <td>
                                            <div>{{ $poin->jenisPoin?->deskripsi ?? '-' }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if ($poin->jenisPoin?->jenis === 'pelanggaran')
                                                <span class="badge bg-danger bg-opacity-10 text-danger"
                                                    style="font-size:11px;padding:4px 8px;border-radius:20px">Pelanggaran</span>
                                            @elseif($poin->jenisPoin?->jenis === 'reward')
                                                <span class="badge bg-success bg-opacity-10 text-success"
                                                    style="font-size:11px;padding:4px 8px;border-radius:20px">Reward</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary"
                                                    style="font-size:11px;padding:4px 8px;border-radius:20px">{{ ucfirst($poin->jenisPoin?->jenis) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center"
                                            style="color:{{ $poin->jenisPoin?->jenis === 'reward' ? 'green' : 'red' }}">
                                            {{ $poin->jenisPoin?->skor ?? '-' }}
                                        </td>
                                        <td class="text-center" style="color:var(--text-muted)">
                                            {{ $poin->jumlah_kejadian }}
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
                const rewardData = chartData.map(d => parseFloat(d.reward) || 0);
                const pelanggaranData = chartData.map(d => parseFloat(d.pelanggaran) || 0);

                // ── Omset Area Chart (now Reward vs Pelanggaran) ──
                const omsetCtx = document.getElementById('omsetChart').getContext('2d');

                const gradReward = omsetCtx.createLinearGradient(0, 0, 0, 300);
                gradReward.addColorStop(0, 'rgba(99,102,241,.35)');
                gradReward.addColorStop(1, 'rgba(99,102,241,0)');

                const gradPelanggaran = omsetCtx.createLinearGradient(0, 0, 0, 300);
                gradPelanggaran.addColorStop(0, 'rgba(245,158,11,.25)');
                gradPelanggaran.addColorStop(1, 'rgba(245,158,11,0)');

                const omsetChart = new Chart(omsetCtx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                                label: 'Reward',
                                data: rewardData,
                                borderColor: '#6366f1',
                                backgroundColor: gradReward,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#6366f1',
                                pointRadius: 4,
                                pointHoverRadius: 7,
                                fill: true,
                                tension: 0.4,
                            },
                            {
                                label: 'Pelanggaran',
                                data: pelanggaranData,
                                borderColor: '#f59e0b',
                                backgroundColor: gradPelanggaran,
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
                                    label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.raw.toLocaleString(
                                        'id-ID') + ' poin',
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
                                    callback: v => v.toLocaleString('id-ID') + ' poin',
                                },
                            },
                        },
                    },
                });

                // ── Donut Chart ──
                const donutCtx = document.getElementById('donutChart').getContext('2d');
                const totalReward = rewardData.reduce((a, b) => a + b, 0);
                const totalPelanggaran = pelanggaranData.reduce((a, b) => a + b, 0);

                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Reward', 'Pelanggaran'],
                        datasets: [{
                            data: [totalReward || 1, totalPelanggaran || 0],
                            backgroundColor: ['#22c55e', '#ef4444'],
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
                                    label: ctx => ' ' + ctx.raw.toLocaleString('id-ID') + ' poin'
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
