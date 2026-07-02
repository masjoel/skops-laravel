@extends('layouts.app')
@section('title', 'Data Barang')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-boxes me-2" style="color:#6366f1"></i>Data Barang</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Barang</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('master.barang.create') }}" class="btn btn-accent">
        <i class="fas fa-plus me-2"></i>Tambah Barang
    </a>
</div>

{{-- Filter Bar --}}
<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <div class="input-group" style="border-radius:8px;overflow:hidden">
                    <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, kode, barcode..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="kritis" class="form-select">
                    <option value="">Semua Stok</option>
                    <option value="1" {{ request('kritis') === '1' ? 'selected' : '' }}>Stok Kritis</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-accent">Filter</button>
                @if(request()->hasAny(['search','kategori','kritis']))
                    <a href="{{ route('master.barang.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span>Daftar Barang <span class="text-muted" style="font-weight:400;font-size:13px">({{ $barang->total() }} item)</span></span>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-end">Hrg Beli</th>
                        <th class="text-end">Hrg Jual</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center" style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barang as $i => $b)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px">{{ $barang->firstItem() + $i }}</td>
                        <td>
                            <span style="font-family:monospace;font-size:12px;background:rgba(99,102,241,.08);color:#6366f1;padding:2px 8px;border-radius:6px">
                                {{ $b->kdbrg }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:500;font-size:13.5px">{{ $b->namabrg }}</div>
                            @if($b->barcode)
                                <div style="font-size:11px;color:var(--text-muted)"><i class="fas fa-barcode me-1"></i>{{ $b->barcode }}</div>
                            @endif
                        </td>
                        <td style="font-size:13px">{{ $b->kategori?->nama ?? '-' }}</td>
                        <td style="font-size:13px">{{ $b->satuan?->nama ?? '-' }}</td>
                        <td class="text-end" style="font-size:13px">Rp {{ number_format($b->hrg_beli, 0, ',', '.') }}</td>
                        <td class="text-end" style="font-size:13px">Rp {{ number_format($b->hrg1, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($b->stok <= $b->stok_kritis)
                                <span class="badge-status badge-danger">{{ $b->stok }}</span>
                            @else
                                <span class="badge-status badge-success">{{ $b->stok }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('master.barang.edit', $b->id) }}"
                                   class="btn btn-sm" style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('master.barang.destroy', $b->id) }}"
                                      onsubmit="return confirm('Hapus barang {{ addslashes($b->namabrg) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center" style="padding:48px;color:var(--text-muted)">
                            <i class="fas fa-box-open fa-3x mb-3 d-block" style="opacity:.2"></i>
                            Belum ada data barang
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($barang->hasPages())
    <div class="card-body border-top" style="padding:14px 20px">
        {{ $barang->links() }}
    </div>
    @endif
</div>
@endsection
