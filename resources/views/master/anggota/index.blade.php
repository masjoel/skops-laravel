@extends('layouts.app')
@section('title', 'Data Anggota / Customer')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-users me-2" style="color:#6366f1"></i>Anggota / Customer</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Anggota</li></ol>
    </div>
    <a href="{{ route('master.anggota.create') }}" class="btn btn-accent"><i class="fas fa-plus me-2"></i>Tambah Anggota</a>
</div>

<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, telepon, email, kota..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="gol" class="form-select">
                    <option value="">Semua Golongan</option>
                    <option value="Umum" {{ request('gol') === 'Umum' ? 'selected' : '' }}>Umum</option>
                    <option value="Member" {{ request('gol') === 'Member' ? 'selected' : '' }}>Member</option>
                    <option value="VIP" {{ request('gol') === 'VIP' ? 'selected' : '' }}>VIP</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-accent">Filter</button>
                @if(request()->hasAny(['search','gol']))<a href="{{ route('master.anggota.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>@endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Daftar Anggota <span class="text-muted fw-normal" style="font-size:13px">({{ $anggota->total() }} data)</span></div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th style="width:40px">#</th><th>Nama</th><th>Kota</th><th>Telepon</th><th>Email</th><th class="text-center">Golongan</th><th class="text-center" style="width:100px">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($anggota as $i => $a)
                    <tr>
                        <td style="color:var(--text-muted);font-size:12px">{{ $anggota->firstItem() + $i }}</td>
                        <td><div style="font-weight:500">{{ $a->nama }}</div><div style="font-size:11px;color:var(--text-muted)">{{ $a->alamat ?: '' }}</div></td>
                        <td style="font-size:13px">{{ $a->kota ?: '-' }}</td>
                        <td style="font-size:13px">{{ $a->telepon ?: '-' }}</td>
                        <td style="font-size:13px">{{ $a->email ?: '-' }}</td>
                        <td class="text-center">
                            @php $golColors = ['VIP' => 'rgba(245,158,11,.12)|#d97706','Member' => 'rgba(34,197,94,.12)|#16a34a','Umum' => 'rgba(99,102,241,.1)|#6366f1']; $gc = $golColors[$a->gol] ?? 'rgba(100,116,139,.1)|#64748b'; [$bg,$clr] = explode('|',$gc); @endphp
                            <span style="background:{{ $bg }};color:{{ $clr }};padding:2px 10px;border-radius:20px;font-size:11.5px;font-weight:600">{{ $a->gol ?: 'Umum' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('master.anggota.edit', $a->id) }}" class="btn btn-sm" style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i class="fas fa-pen"></i></a>
                                <form method="POST" action="{{ route('master.anggota.destroy', $a->id) }}" onsubmit="return confirm('Hapus anggota {{ addslashes($a->nama) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-users fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada data anggota</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($anggota->hasPages())<div class="card-body border-top" style="padding:14px 20px">{{ $anggota->links() }}</div>@endif
</div>
@endsection
