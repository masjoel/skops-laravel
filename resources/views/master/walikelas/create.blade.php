@extends('layouts.app')
@section('title', 'Tambah ' . $title)
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-pen me-2" style="color:#6366f1"></i>Tambah {{ $title }}</h1>
        </div>
        <a href="{{ route('master.walikelas.index') }}" class="btn btn-outline-secondary"><i
                class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-5">
            <div class="card bg-light">
                <div class="card-header"><i class="fas fa-ruler me-2" style="color:#6366f1"></i>Form Tambah
                    {{ $title }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('master.walikelas.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Guru <span class="text-danger">*</span></label>
                            @php
                                $selectedGuruId = old('guru_id');
                                $selectedGuruName = '';
                                if($selectedGuruId) {
                                    $sg = $guru->firstWhere('id', $selectedGuruId);
                                    if($sg) {
                                        $selectedGuruName = $sg->personil->nama . ' (' . ($sg->nip ?? '-') . ')';
                                    }
                                }
                            @endphp
                            <div class="input-group">
                                <input type="hidden" name="guru_id" id="guru_id" value="{{ $selectedGuruId }}">
                                <input type="text" class="form-control @error('guru_id') is-invalid @enderror" id="guru_name_display" placeholder="Pilih Guru..." value="{{ $selectedGuruName }}" readonly style="background-color: #f8f9fa; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalPilihGuru">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalPilihGuru"><i class="fas fa-search"></i> Cari</button>
                            </div>
                            @error('guru_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Kelas <span class="text-danger">*</span></label>
                                <select name="kelas_id" class="form-control @error('kelas_id') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}"
                                            {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }} {{ $k->jurusan->nama ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                <select name="tahun_ajaran_id" class="form-select">
                                    @foreach ($tahunAjaranList as $ta)
                                        <option value="{{ $ta->id }}"
                                            {{ request('tahun_ajaran_id', $tahunAjaranAktifId) == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-accent flex-fill"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                            <a href="{{ route('master.walikelas.index') }}" class="btn btn-outline-secondary flex-fill"><i
                                    class="fas fa-times me-2"></i>Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Guru -->
    <div class="modal fade" id="modalPilihGuru" tabindex="-1" aria-labelledby="modalPilihGuruLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPilihGuruLabel">Pilih Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="searchGuruInput" placeholder="Cari Nama atau NIP Guru...">
                    </div>
                    <div class="list-group" id="guruList">
                        @foreach ($guru as $g)
                            <button type="button" class="list-group-item list-group-item-action guru-item" 
                                data-id="{{ $g->id }}" 
                                data-name="{{ $g->personil->nama }}">
                                <strong>{{ $g->personil->nama }}</strong>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchGuruInput');
            const guruItems = document.querySelectorAll('.guru-item');
            
            // Filter pencarian
            searchInput.addEventListener('input', function(e) {
                const search = e.target.value.toLowerCase();
                guruItems.forEach(function(item) {
                    const text = item.innerText.toLowerCase();
                    if(text.includes(search)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Pemilihan item guru
            guruItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    
                    document.getElementById('guru_id').value = id;
                    document.getElementById('guru_name_display').value = name;
                    
                    // Menutup modal menggunakan API Bootstrap
                    const modalEl = document.getElementById('modalPilihGuru');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                });
            });
            
            // Auto focus ke input search saat modal muncul
            document.getElementById('modalPilihGuru').addEventListener('shown.bs.modal', function () {
                searchInput.focus();
            });
        });
    </script>
    @endpush
@endsection
