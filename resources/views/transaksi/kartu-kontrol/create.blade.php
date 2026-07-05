@extends('layouts.app')
@section('title', 'Tambah ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-pen me-2" style="color:#6366f1"></i>Tambah {{ $title }}</h1>
        </div>
        <a href="{{ route('transaksi.kartu-kontrol.index') }}" class="btn btn-outline-secondary"><i
                class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-6">
            <div class="card bg-light">
                <div class="card-header"><i class="fas fa-ruler me-2" style="color:#6366f1"></i>Form Tambah
                    {{ $title }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('transaksi.kartu-kontrol.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tgl" class="form-control @error('tgl') is-invalid @enderror"
                                    value="{{ old('tgl', date('Y-m-d')) }}" required>
                                @error('tgl')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Periode Akademik <span class="text-danger">*</span></label>
                                <select name="periode_akademik_id"
                                    class="form-control @error('periode_akademik_id') is-invalid @enderror" required>
                                    <option value="">Pilih Periode</option>
                                    @foreach ($periodeAkademikList as $pa)
                                        <option value="{{ $pa->id }}"
                                            {{ old('periode_akademik_id', $periodeAkademikAktifId) == $pa->id ? 'selected' : '' }}>
                                            {{ $pa->tahunAjaran?->nama }} - Semester
                                            {{ $pa->semester == 1 ? 'Ganjil' : 'Genap' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('periode_akademik_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru</label>
                            @php
                                $selectedGuruId = old('guru_id');
                                $selectedGuruName = '';
                                if ($selectedGuruId) {
                                    $sg = $guruList->firstWhere('id', $selectedGuruId);
                                    if ($sg) {
                                        $selectedGuruName =
                                            ($sg->personil?->nama ?? '');
                                    }
                                }
                            @endphp
                            <div class="input-group">
                                <input type="hidden" name="guru_id" id="guru_id" value="{{ $selectedGuruId }}">
                                <input type="text" class="form-control @error('guru_id') is-invalid @enderror"
                                    id="guru_name_display" placeholder="Pilih Guru..." value="{{ $selectedGuruName }}"
                                    readonly style="background-color: #f8f9fa; cursor: pointer;" data-bs-toggle="modal"
                                    data-bs-target="#modalPilihGuru">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#modalPilihGuru"><i class="fas fa-search"></i> Cari</button>
                            </div>
                            @error('guru_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Siswa <span class="text-danger">*</span></label>
                            @php
                                $selectedMuridKelasId = old('murid_kelas_id');
                                $selectedMuridKelasName = '';
                                if ($selectedMuridKelasId) {
                                    $smk = $muridKelasList->firstWhere('id', $selectedMuridKelasId);
                                    if ($smk) {
                                        $selectedMuridKelasName =
                                            ($smk->murid?->personil?->nama ?? '') .
                                            ' - ' .
                                            ($smk->kelas?->nama_kelas ?? '');
                                    }
                                }
                            @endphp
                            <div class="input-group">
                                <input type="hidden" name="murid_kelas_id" id="murid_kelas_id"
                                    value="{{ $selectedMuridKelasId }}">
                                <input type="text" class="form-control @error('murid_kelas_id') is-invalid @enderror"
                                    id="murid_kelas_name_display" placeholder="Pilih Siswa..."
                                    value="{{ $selectedMuridKelasName }}" readonly
                                    style="background-color: #f8f9fa; cursor: pointer;" data-bs-toggle="modal"
                                    data-bs-target="#modalPilihSiswa">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#modalPilihSiswa"><i class="fas fa-search"></i> Cari</button>
                            </div>
                            @error('murid_kelas_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-9 mb-3">
                                <label class="form-label">Jenis Poin <span class="text-danger">*</span></label>
                                @php
                                    $selectedJenisPoinId = old('jenis_poin_id');
                                    $selectedJenisPoinName = '';
                                    if ($selectedJenisPoinId) {
                                        $sjp = $jenisPoinList->firstWhere('id', $selectedJenisPoinId);
                                        if ($sjp) {
                                            $selectedJenisPoinName =
                                                $sjp->kode . ' - ' . $sjp->deskripsi;
                                        }
                                    }
                                @endphp
                                <div class="input-group">
                                    <input type="hidden" name="jenis_poin_id" id="jenis_poin_id"
                                        value="{{ $selectedJenisPoinId }}">
                                    <input type="text" class="form-control @error('jenis_poin_id') is-invalid @enderror"
                                        id="jenis_poin_name_display" placeholder="Pilih Jenis Poin..."
                                        value="{{ $selectedJenisPoinName }}" readonly
                                        style="background-color: #f8f9fa; cursor: pointer;" data-bs-toggle="modal"
                                        data-bs-target="#modalPilihJenisPoin">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                                        data-bs-target="#modalPilihJenisPoin"><i class="fas fa-search"></i> Cari</button>
                                </div>
                                @error('jenis_poin_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Skor</label>
                                <input type="number" step="1" name="skor" id="skor_input"
                                    class="form-control @error('skor') is-invalid @enderror" value="{{ old('skor') }}">
                                @error('skor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                {{-- <small class="text-muted">Kosongkan untuk pakai skor standar jenis poin.</small> --}}
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Catatan Tindakan</label>
                            <textarea name="tindakan" rows="2" class="form-control @error('tindakan') is-invalid @enderror"
                                placeholder="Contoh: Dipanggil BK, diberi peringatan tertulis, dsb.">{{ old('tindakan') }}</textarea>
                            @error('tindakan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-accent flex-fill"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                            <a href="{{ route('transaksi.kartu-kontrol.index') }}"
                                class="btn btn-outline-secondary flex-fill"><i class="fas fa-times me-2"></i>Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Siswa -->
    <div class="modal fade" id="modalPilihSiswa" tabindex="-1" aria-labelledby="modalPilihSiswaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPilihSiswaLabel">Pilih Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="searchSiswaInput"
                            placeholder="Cari Nama atau Kelas Siswa...">
                    </div>
                    <div class="list-group" id="siswaList">
                        @foreach ($muridKelasList as $mk)
                            <button type="button" class="list-group-item list-group-item-action siswa-item"
                                data-id="{{ $mk->id }}"
                                data-name="{{ $mk->murid?->personil?->nama }} - {{ $mk->kelas?->nama_kelas }}">
                                <strong>{{ $mk->murid?->personil?->nama }}</strong>
                                <br><small class="text-muted">Kelas: {{ $mk->kelas?->nama_kelas }} | NIS:
                                    {{ $mk->murid?->nis ?? '-' }}</small>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Jenis Poin -->
    <div class="modal fade" id="modalPilihJenisPoin" tabindex="-1" aria-labelledby="modalPilihJenisPoinLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPilihJenisPoinLabel">Pilih Jenis Poin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="searchJenisPoinInput"
                            placeholder="Cari Kode atau Deskripsi...">
                    </div>
                    <div class="list-group" id="jenisPoinList">
                        @foreach ($jenisPoinList as $jp)
                            <button type="button" class="list-group-item list-group-item-action jenis-poin-item"
                                data-id="{{ $jp->id }}" data-skor="{{ $jp->skor }}"
                                data-tindakan="{{ $jp->tindakan }}"
                                data-name="{{ $jp->kode }} - {{ $jp->deskripsi }}">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $jp->deskripsi }}</strong>
                                    <span
                                        class="badge {{ $jp->jenis === 'reward' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($jp->jenis) }}</span>
                                </div>
                                <small class="text-muted">Kode: {{ $jp->kode }} | Skor: {{ $jp->skor }}</small>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Guru -->
    <div class="modal fade" id="modalPilihGuru" tabindex="-1" aria-labelledby="modalPilihGuruLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPilihGuruLabel">Pilih Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="searchGuruInput"
                            placeholder="Cari Nama atau NIP Guru...">
                    </div>
                    <div class="list-group" id="guruList">
                        @foreach ($guruList as $g)
                            <button type="button" class="list-group-item list-group-item-action guru-item"
                                data-id="{{ $g->id }}"
                                data-name="{{ $g->personil?->nama }}">
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
            function setupModalSearch(searchInputId, itemClass, idInputId, nameDisplayId, modalId, onSelectCallback = null) {
                const searchInput = document.getElementById(searchInputId);
                const items = document.querySelectorAll('.' + itemClass);

                // Filter pencarian
                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        const search = e.target.value.toLowerCase();
                        items.forEach(function(item) {
                            const text = item.innerText.toLowerCase();
                            if (text.includes(search)) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    });
                }

                // Pemilihan item
                items.forEach(function(item) {
                    item.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');

                        document.getElementById(idInputId).value = id;
                        document.getElementById(nameDisplayId).value = name;

                        if (onSelectCallback) {
                            onSelectCallback(this);
                        }

                        const modalEl = document.getElementById(modalId);
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    });
                });

                // Auto focus ke input search saat modal muncul
                const modalEl = document.getElementById(modalId);
                if (modalEl && searchInput) {
                    modalEl.addEventListener('shown.bs.modal', function() {
                        searchInput.focus();
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                setupModalSearch('searchSiswaInput', 'siswa-item', 'murid_kelas_id', 'murid_kelas_name_display',
                    'modalPilihSiswa');

                setupModalSearch('searchJenisPoinInput', 'jenis-poin-item', 'jenis_poin_id', 'jenis_poin_name_display',
                    'modalPilihJenisPoin',
                    function(item) {
                        const skor = item.getAttribute('data-skor');
                        const tindakan = item.getAttribute('data-tindakan');
                        const skorInput = document.getElementById('skor_input');
                        const tindakanInput = document.querySelector('textarea[name="tindakan"]');

                        if (skor !== null) {
                            skorInput.value = skor;
                        }
                        if (tindakan !== null && tindakan !== '') {
                            tindakanInput.value = tindakan;
                        }
                    });

                setupModalSearch('searchGuruInput', 'guru-item', 'guru_id', 'guru_name_display', 'modalPilihGuru');
            });
        </script>
    @endpush
@endsection
