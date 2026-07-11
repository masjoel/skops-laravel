@extends('layouts.app')
@section('title', 'Input Massal Kartu Kontrol')
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-layer-group me-2" style="color:#6366f1"></i>Input Massal Kartu Kontrol</h1>
            <small class="text-muted">Pilih jenis poin sekali, centang siswa yang terkena.</small>
        </div>
        <a href="{{ route('transaksi.kartu-kontrol.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('transaksi.kartu-kontrol.bulk-store') }}" id="bulkForm">
        @csrf
        <div class="row g-3">
            {{-- Kolom Kiri: Form shared --}}
            <div class="col-12 col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-sliders-h me-2" style="color:#6366f1"></i>Pengaturan Umum
                    </div>
                    <div class="card-body d-flex flex-column gap-3">

                        {{-- Tanggal --}}
                        <div>
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tgl" class="form-control @error('tgl') is-invalid @enderror"
                                value="{{ old('tgl', date('Y-m-d')) }}" required>
                            @error('tgl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Periode Akademik --}}
                        <div>
                            <label class="form-label">Periode Akademik <span class="text-danger">*</span></label>
                            <select name="periode_akademik_id"
                                class="form-control @error('periode_akademik_id') is-invalid @enderror" required>
                                <option value="">Pilih Periode</option>
                                @foreach ($periodeAkademikList as $pa)
                                    <option value="{{ $pa->id }}"
                                        {{ old('periode_akademik_id', $periodeAkademikAktifId) == $pa->id ? 'selected' : '' }}>
                                        {{ $pa->tahunAjaran?->nama }} — Semester
                                        {{ $pa->semester == 1 ? 'Ganjil' : 'Genap' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('periode_akademik_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Jenis Poin --}}
                        <div>
                            <label class="form-label">Jenis Poin <span class="text-danger">*</span></label>
                            <input type="hidden" name="jenis_poin_id" id="jenis_poin_id" value="{{ old('jenis_poin_id') }}">
                            <div class="input-group">
                                <input type="text" class="form-control @error('jenis_poin_id') is-invalid @enderror"
                                    id="jenis_poin_name_display" placeholder="Pilih Jenis Poin..." readonly
                                    style="background-color:#f8f9fa;cursor:pointer"
                                    data-bs-toggle="modal" data-bs-target="#modalPilihJenisPoin">
                                <button class="btn btn-outline-secondary" type="button"
                                    data-bs-toggle="modal" data-bs-target="#modalPilihJenisPoin">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            @error('jenis_poin_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        {{-- Skor --}}
                        <div>
                            <label class="form-label">Skor
                                <small class="text-muted">(kosongkan = pakai skor default jenis poin)</small>
                            </label>
                            <input type="number" step="1" name="skor" id="skor_input"
                                class="form-control @error('skor') is-invalid @enderror"
                                value="{{ old('skor') }}" placeholder="Otomatis dari jenis poin">
                            @error('skor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Guru --}}
                        <div>
                            <label class="form-label">Guru (opsional)</label>
                            <input type="hidden" name="guru_id" id="guru_id" value="{{ old('guru_id') }}">
                            <div class="input-group">
                                <input type="text" class="form-control" id="guru_name_display"
                                    placeholder="Pilih Guru..." readonly
                                    style="background-color:#f8f9fa;cursor:pointer"
                                    data-bs-toggle="modal" data-bs-target="#modalPilihGuru">
                                <button class="btn btn-outline-secondary" type="button"
                                    data-bs-toggle="modal" data-bs-target="#modalPilihGuru">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-outline-danger" type="button" id="clearGuru" title="Hapus pilihan guru">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Tindakan --}}
                        <div>
                            <label class="form-label">Catatan Tindakan</label>
                            <textarea name="tindakan" rows="2"
                                class="form-control @error('tindakan') is-invalid @enderror"
                                placeholder="Contoh: Dipanggil BK, diberi peringatan tertulis, dsb.">{{ old('tindakan') }}</textarea>
                            @error('tindakan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Daftar Siswa --}}
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span><i class="fas fa-users me-2" style="color:#6366f1"></i>Pilih Siswa</span>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-primary" id="selectedCount">0 dipilih</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSelectAll">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="btnClearAll">Reset</button>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Search --}}
                        <div class="mb-3 input-group">
                            <input type="text" class="form-control" id="searchSiswaInline"
                                placeholder="&#xF002; Cari nama siswa atau kelas..." style="font-family:inherit">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearchSiswa" title="Kosongkan pencarian">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        @error('murid_kelas_ids')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        {{-- Filter Kelas --}}
                        {{-- <div class="mb-3 d-flex flex-wrap gap-2" id="kelasFilterBtns">
                            <button type="button" class="btn btn-sm btn-outline-secondary active kelas-filter-btn"
                                data-kelas="">Semua Kelas</button>
                            @foreach ($muridKelasList->groupBy('kelas.nama_kelas') as $kelasNama => $items)
                                <button type="button" class="btn btn-sm btn-outline-secondary kelas-filter-btn"
                                    data-kelas="{{ $kelasNama }}">{{ $kelasNama }}</button>
                            @endforeach
                        </div> --}}

                        {{-- Siswa List --}}
                        <div style="max-height:420px;overflow-y:auto;border:1px solid var(--border-color);border-radius:8px;">
                            <table class="table table-hover mb-0" id="siswaTable">
                                <thead style="position:sticky;top:0;background:var(--card-bg);z-index:1">
                                    <tr>
                                        <th style="width:36px"></th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>NIS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($muridKelasList as $mk)
                                        <tr class="siswa-row"
                                            data-nama="{{ strtolower($mk->murid?->personil?->nama) }}"
                                            data-kelas="{{ $mk->kelas?->nama_kelas }}">
                                            <td class="text-center">
                                                <input type="checkbox" name="murid_kelas_ids[]"
                                                    value="{{ $mk->id }}"
                                                    class="form-check-input siswa-checkbox"
                                                    style="width:18px;height:18px;cursor:pointer"
                                                    {{ is_array(old('murid_kelas_ids')) && in_array($mk->id, old('murid_kelas_ids')) ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <label class="mb-0" style="cursor:pointer"
                                                    for="mk_{{ $mk->id }}">
                                                    <strong>{{ $mk->murid?->personil?->nama ?? '-' }}</strong>
                                                </label>
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $mk->kelas?->nama_kelas ?? '-' }}</span></td>
                                            <td><small class="text-muted">{{ $mk->murid?->nis ?? '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-accent flex-fill" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Simpan (<span id="countLabel">0</span> Siswa)
                            </button>
                            <a href="{{ route('transaksi.kartu-kontrol.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal Jenis Poin --}}
    <div class="modal fade" id="modalPilihJenisPoin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Jenis Poin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" id="searchJenisPoinInput" placeholder="Cari...">
                    <div class="list-group" id="jenisPoinList">
                        @foreach ($jenisPoinList as $jp)
                            <button type="button" class="list-group-item list-group-item-action jenis-poin-item"
                                data-id="{{ $jp->id }}" data-skor="{{ $jp->skor }}"
                                data-tindakan="{{ $jp->tindakan }}"
                                data-name="{{ $jp->kode }} — {{ $jp->deskripsi }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $jp->deskripsi }}</strong>
                                    <span class="badge {{ match($jp->jenis) {
                                        'reward' => 'bg-success',
                                        'pelanggaran' => 'bg-danger',
                                        default => 'bg-info'
                                    } }}">{{ ucfirst($jp->jenis) }}</span>
                                </div>
                                <small class="text-muted">Kode: {{ $jp->kode }} | Skor: {{ $jp->skor }}</small>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Guru --}}
    <div class="modal fade" id="modalPilihGuru" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control mb-3" id="searchGuruInput" placeholder="Cari nama atau NIP...">
                    <div class="list-group" id="guruList">
                        @foreach ($guruList as $g)
                            <button type="button" class="list-group-item list-group-item-action guru-item"
                                data-id="{{ $g->id }}" data-name="{{ $g->personil?->nama }}">
                                <strong>{{ $g->personil?->nama }}</strong>
                                <br><small class="text-muted">NIP: {{ $g->nip ?? '-' }}</small>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Jenis Poin Modal ──
        const jpItems = document.querySelectorAll('.jenis-poin-item');
        const jpSearch = document.getElementById('searchJenisPoinInput');
        const jpModal  = document.getElementById('modalPilihJenisPoin');

        jpSearch.addEventListener('input', () => {
            const q = jpSearch.value.toLowerCase();
            jpItems.forEach(el => el.style.display = el.innerText.toLowerCase().includes(q) ? '' : 'none');
        });
        jpItems.forEach(el => el.addEventListener('click', function () {
            document.getElementById('jenis_poin_id').value = this.dataset.id;
            document.getElementById('jenis_poin_name_display').value = this.dataset.name;
            document.getElementById('skor_input').value = this.dataset.skor;
            if (this.dataset.tindakan) {
                document.querySelector('textarea[name="tindakan"]').value = this.dataset.tindakan;
            }
            bootstrap.Modal.getInstance(jpModal).hide();
        }));
        jpModal.addEventListener('shown.bs.modal', () => jpSearch.focus());

        // ── Guru Modal ──
        const guruItems  = document.querySelectorAll('.guru-item');
        const guruSearch = document.getElementById('searchGuruInput');
        const guruModal  = document.getElementById('modalPilihGuru');

        guruSearch.addEventListener('input', () => {
            const q = guruSearch.value.toLowerCase();
            guruItems.forEach(el => el.style.display = el.innerText.toLowerCase().includes(q) ? '' : 'none');
        });
        guruItems.forEach(el => el.addEventListener('click', function () {
            document.getElementById('guru_id').value = this.dataset.id;
            document.getElementById('guru_name_display').value = this.dataset.name;
            bootstrap.Modal.getInstance(guruModal).hide();
        }));
        guruModal.addEventListener('shown.bs.modal', () => guruSearch.focus());

        document.getElementById('clearGuru').addEventListener('click', () => {
            document.getElementById('guru_id').value = '';
            document.getElementById('guru_name_display').value = '';
        });

        // ── Siswa Checkbox Counter ──
        const checkboxes = document.querySelectorAll('.siswa-checkbox');
        const countLabel  = document.getElementById('countLabel');
        const selectedBadge = document.getElementById('selectedCount');

        function updateCount() {
            const n = document.querySelectorAll('.siswa-checkbox:checked').length;
            countLabel.textContent = n;
            selectedBadge.textContent = n + ' dipilih';
        }
        checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
        updateCount();

        // ── Row click to toggle checkbox ──
        document.querySelectorAll('.siswa-row').forEach(row => {
            row.addEventListener('click', function (e) {
                if (e.target.tagName === 'INPUT') return;
                const cb = row.querySelector('.siswa-checkbox');
                cb.checked = !cb.checked;
                updateCount();
            });
            row.style.cursor = 'pointer';
        });

        // ── Select All / Clear ──
        document.getElementById('btnSelectAll').addEventListener('click', () => {
            document.querySelectorAll('.siswa-row:not([style*="display: none"]) .siswa-checkbox').forEach(cb => cb.checked = true);
            updateCount();
        });
        document.getElementById('btnClearAll').addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
            updateCount();
        });

        // ── Inline Search ──
        const searchInput = document.getElementById('searchSiswaInline');
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.siswa-row').forEach(row => {
                const match = row.dataset.nama.includes(q) || row.dataset.kelas.toLowerCase().includes(q);
                // Also respect kelas filter
                const kelasMatch = activeKelas === '' || row.dataset.kelas === activeKelas;
                row.style.display = (match && kelasMatch) ? '' : 'none';
            });
        });

        // ── Clear Search Siswa ──
        document.getElementById('clearSearchSiswa').addEventListener('click', () => {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });

        // ── Filter Kelas ──
        let activeKelas = '';
        document.querySelectorAll('.kelas-filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.kelas-filter-btn').forEach(b => b.classList.remove('active','btn-primary'));
                document.querySelectorAll('.kelas-filter-btn').forEach(b => b.classList.add('btn-outline-secondary'));
                this.classList.add('active', 'btn-primary');
                this.classList.remove('btn-outline-secondary');

                activeKelas = this.dataset.kelas;
                const q = searchInput.value.toLowerCase();
                document.querySelectorAll('.siswa-row').forEach(row => {
                    const kelasMatch = activeKelas === '' || row.dataset.kelas === activeKelas;
                    const namaMatch  = row.dataset.nama.includes(q) || row.dataset.kelas.toLowerCase().includes(q);
                    row.style.display = (kelasMatch && namaMatch) ? '' : 'none';
                });
            });
        });

        // Highlight checked rows
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                this.closest('tr').style.background = this.checked ? 'rgba(99,102,241,.08)' : '';
            });
            if (cb.checked) cb.closest('tr').style.background = 'rgba(99,102,241,.08)';
        });
    });
    </script>
    @endpush
@endsection
