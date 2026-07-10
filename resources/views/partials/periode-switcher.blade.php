<div class="dropdown">
    <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown"
        aria-expanded="false"
        style="background:rgba(99,102,241,.1);color:#6366f1;border-radius:8px;border:none;padding:6px 12px">
        <i class="fas fa-graduation-cap"></i>
        @if ($periodeAktif)
            <span>{{ $periodeAktif->tahunAjaran->nama }} · Semester
                {{ $periodeAktif->semester == 1 ? 'Ganjil' : 'Genap' }}</span>
        @else
            <span class="text-danger">Belum ada periode aktif</span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-end" style="min-width:280px">
        <li>
            <h6 class="dropdown-header">Pilih Periode Aktif</h6>
        </li>
        @forelse ($tahunAjaranList as $ta)
            @foreach ($ta->periodeAkademik as $periode)
                <li>
                    <form method="POST" action="{{ route('periode-akademik.aktifkan', $periode->id) }}">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center justify-content-between"
                            @disabled($periode->is_aktif)>
                            <span>
                                {{ $ta->nama }} · Semester {{ $periode->semester == 1 ? 'Ganjil' : 'Genap' }}
                            </span>
                            @if ($periode->is_aktif)
                                <i class="fas fa-check-circle text-success"></i>
                            @endif
                        </button>
                    </form>
                </li>
            @endforeach
        @empty
            <li><span class="dropdown-item-text text-muted">Belum ada data.</span></li>
        @endforelse
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('master.tahun-ajaran.index') }}">
                <i class="fas fa-cog me-2"></i>Kelola Tahun Ajaran
            </a>
        </li>
    </ul>
</div>