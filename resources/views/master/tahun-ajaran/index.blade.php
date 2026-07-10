@extends('layouts.app')
@section('title', 'Data ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-calendar-alt me-2" style="color:#6366f1"></i>{{ $title }}</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="card bg-light">
                <div class="card-header"><i class="fas fa-plus me-2" style="color:#6366f1"></i>Tambah Tahun Ajaran
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('master.tahun-ajaran.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                value="{{ old('nama') }}" placeholder="Contoh: 2026/2027" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Semester 1 & 2 akan otomatis dibuat.</small>
                        </div>
                        <button type="submit" class="btn btn-accent w-100"><i class="fas fa-save me-2"></i>Simpan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card bg-light">
                <div class="card-header">Daftar Tahun Ajaran & Semester</div>
                <div class="card-body table-responsive" style="padding:0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tahun Ajaran</th>
                                <th class="text-center">Semester 1</th>
                                <th class="text-center">Semester 2</th>
                                {{-- <th class="text-center" style="width:100px">Status Tahun</th> --}}
                                <th class="text-center" style="width:90px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tahunAjaranList as $ta)
                                <tr>
                                    <td class="fw-bold">{{ $ta->nama }}</td>
                                    @foreach ([1, 2] as $sem)
                                        @php $periode = $ta->periodeAkademik->firstWhere('semester', $sem); @endphp
                                        <td class="text-center">
                                            @if ($periode)
                                                @if ($periode->is_aktif)
                                                    <span class="badge"
                                                        style="background:rgba(34,197,94,.15);color:#16a34a"><i
                                                            class="fas fa-check-circle me-1"></i>Aktif</span>
                                                @else
                                                    <form method="POST"
                                                        action="{{ route('periode-akademik.aktifkan', $periode->id) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    {{-- <td class="text-center">
                                        @if ($ta->is_aktif)
                                            <span class="badge" style="background:rgba(99,102,241,.15);color:#6366f1">
                                                Tahun Aktif</span>
                                        @else
                                            <form method="POST"
                                                action="{{ route('master.tahun-ajaran.aktifkan', $ta->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @endif
                                    </td> --}}
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('master.tahun-ajaran.edit', $ta->id) }}" class="btn btn-sm"
                                                style="padding:4px 8px;background:rgba(99,102,241,.1);color:#6366f1;border-radius:6px"><i
                                                    class="fas fa-pen"></i></a>
                                            <form method="POST"
                                                action="{{ route('master.tahun-ajaran.destroy', $ta->id) }}"
                                                onsubmit="return confirm('Hapus tahun ajaran {{ $ta->nama }}? Data semester terkait juga ikut terhapus.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm"
                                                    style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px"><i
                                                        class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center" style="padding:40px;color:var(--text-muted)">
                                        Belum ada tahun ajaran. Tambahkan dulu di form sebelah kiri.
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