@extends('layouts.app')
@section('title', 'Pengaturan Sistem')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-cog me-2" style="color:#6366f1"></i>Pengaturan Sistem</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Seting</li></ol>
    </div>
</div>

<div class="row g-3">
    @php $menus = [
        ['route'=>'seting.perusahaan','icon'=>'fa-building','label'=>'Data Perusahaan','desc'=>'Nama, alamat, logo, dan kontak perusahaan','color'=>'#6366f1'],
        ['route'=>'seting.user.index','icon'=>'fa-users-gear','label'=>'Manajemen User','desc'=>'Tambah, edit, dan atur hak akses pengguna','color'=>'#8b5cf6'],
        ['route'=>'master.barang.index','icon'=>'fa-boxes-stacked','label'=>'Master Barang','desc'=>'Kelola data barang, kategori, satuan, lokasi','color'=>'#22c55e'],
        ['route'=>'master.suplier.index','icon'=>'fa-truck','label'=>'Master Suplier','desc'=>'Kelola data pemasok barang','color'=>'#f59e0b'],
    ]; @endphp
    @foreach($menus as $m)
    <div class="col-12 col-md-6">
        <a href="{{ route($m['route']) }}" style="text-decoration:none">
            <div class="card" style="transition:transform .2s,box-shadow .2s;cursor:pointer" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(99,102,241,.15)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="card-body d-flex align-items-center gap-3" style="padding:20px">
                    <div style="width:52px;height:52px;background:{{ $m['color'] }}20;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas {{ $m['icon'] }} fa-lg" style="color:{{ $m['color'] }}"></i>
                    </div>
                    <div class="flex-fill">
                        <div style="font-weight:600;font-size:15px;color:var(--text-primary)">{{ $m['label'] }}</div>
                        <div style="font-size:13px;color:var(--text-muted);margin-top:2px">{{ $m['desc'] }}</div>
                    </div>
                    <i class="fas fa-chevron-right" style="color:{{ $m['color'] }};opacity:.5"></i>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

@if($perusahaan)
<div class="card mt-4">
    <div class="card-header"><i class="fas fa-info-circle me-2" style="color:#6366f1"></i>Info Perusahaan Aktif</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-auto">
                @if($perusahaan->Logo)
                    <img src="{{ asset('storage/'.$perusahaan->Logo) }}" style="height:60px;border-radius:8px;object-fit:contain">
                @else
                    <div style="width:60px;height:60px;background:rgba(99,102,241,.1);border-radius:8px;display:flex;align-items:center;justify-content:center"><i class="fas fa-building fa-lg" style="color:#6366f1"></i></div>
                @endif
            </div>
            <div>
                <div style="font-size:18px;font-weight:700;color:var(--text-primary)">{{ $perusahaan->NamaClient }}</div>
                <div style="font-size:13px;color:var(--text-muted)">{{ $perusahaan->Alamat }}</div>
                <div style="font-size:13px;color:var(--text-muted)">{{ $perusahaan->Telp }} {{ $perusahaan->Email ? '· '.$perusahaan->Email : '' }}</div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
