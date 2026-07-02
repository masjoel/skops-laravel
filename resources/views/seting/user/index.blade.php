@extends('layouts.app')
@section('title', 'Manajemen User')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-users-gear me-2" style="color:#6366f1"></i>Manajemen User</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('seting.index') }}">Seting</a></li><li class="breadcrumb-item active">User</li></ol>
    </div>
    <a href="{{ route('seting.user.create') }}" class="btn btn-accent"><i class="fas fa-plus me-2"></i>Tambah User</a>
</div>

<div class="card mb-3">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--card-bg);border-color:var(--border-color);color:var(--text-muted)"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama / username..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="level" class="form-select">
                    <option value="">Semua Level</option>
                    @foreach(['administrator','operator','kasir'] as $lvl)
                        <option value="{{ $lvl }}" {{ request('level') === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-accent">Filter</button>
                @if(request()->hasAny(['search','level']))<a href="{{ route('seting.user.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>@endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Daftar User <span class="text-muted fw-normal" style="font-size:13px">({{ $users->total() }} user)</span></div>
    <div class="card-body" style="padding:0">
        <table class="table table-hover mb-0">
            <thead><tr><th style="width:40px">#</th><th>Nama</th><th>Username</th><th>Email</th><th class="text-center">Level</th><th class="text-center">Status</th><th class="text-center" style="width:100px">Aksi</th></tr></thead>
            <tbody>
                @forelse($users as $i => $u)
                <tr>
                    <td style="color:var(--text-muted);font-size:12px">{{ $users->firstItem()+$i }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:12px;flex-shrink:0">
                                {{ strtoupper(substr($u->nama,0,1)) }}
                            </div>
                            <span style="font-weight:500;font-size:13.5px">{{ $u->nama }}
                                @if(session('IDuser') == $u->idx) <span style="font-size:10px;background:rgba(34,197,94,.15);color:#16a34a;padding:1px 6px;border-radius:10px">Anda</span> @endif
                            </span>
                        </div>
                    </td>
                    <td style="font-family:monospace;font-size:13px;color:#6366f1">{{ $u->username }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ $u->email ?: '-' }}</td>
                    <td class="text-center">
                        @php $levelColors=['administrator'=>'rgba(239,68,68,.12)|#ef4444','operator'=>'rgba(245,158,11,.12)|#d97706','kasir'=>'rgba(99,102,241,.1)|#6366f1']; [$bg,$clr]=explode('|',$levelColors[$u->level]??'rgba(100,116,139,.1)|#64748b'); @endphp
                        <span style="background:{{ $bg }};color:{{ $clr }};padding:2px 10px;border-radius:20px;font-size:11.5px;font-weight:600">{{ ucfirst($u->level) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge-status {{ ($u->status??'Aktif') === 'Aktif' ? 'badge-success' : 'badge-danger' }}">{{ $u->status ?? 'Aktif' }}</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('seting.user.edit', $u->idx ?? $u->id) }}" class="btn btn-sm" style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i class="fas fa-pen"></i></a>
                            @if(session('IDuser') != ($u->idx ?? $u->id))
                            <form method="POST" action="{{ route('seting.user.destroy', $u->idx ?? $u->id) }}" onsubmit="return confirm('Hapus user {{ addslashes($u->nama) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center" style="padding:48px;color:var(--text-muted)"><i class="fas fa-users fa-2x mb-2 d-block" style="opacity:.2"></i>Belum ada user</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())<div class="card-body border-top" style="padding:14px 20px">{{ $users->links() }}</div>@endif
</div>
@endsection
