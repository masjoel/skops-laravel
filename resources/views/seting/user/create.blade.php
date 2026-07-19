@php $isEdit = isset($user); @endphp
@extends('layouts.app')
@section('title', $isEdit ? 'Edit User' : 'Tambah User')
@section('content')
    <div class="page-header">
        <div>
            <h1><i class="fas fa-{{ $isEdit ? 'user-pen' : 'user-plus' }} me-2"
                    style="color:#6366f1"></i>{{ $isEdit ? 'Edit' : 'Tambah' }} User</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('seting.index') }}">Seting</a></li>
                <li class="breadcrumb-item"><a href="{{ route('seting.user.index') }}">User</a></li>
                <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Tambah' }}</li>
            </ol>
        </div>
        <a href="{{ route('seting.user.index') }}" class="btn btn-outline-secondary"><i
                class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header"><i class="fas fa-user-cog me-2" style="color:#6366f1"></i>Form
                    {{ $isEdit ? 'Edit' : 'Tambah' }} User</div>
                <div class="card-body">
                    <form method="POST"
                        action="{{ $isEdit ? route('seting.user.update', $user->idx ?? $user->id) : route('seting.user.store') }}">
                        @csrf
                        @if ($isEdit)
                            @method('PUT')
                        @endif
                        <div class="row g-3">
                            {{-- Nama Lengkap --}}
                            <div class="col-12">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="inputName"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name ?? '') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Username --}}
                            <div class="col-md-6">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username', $user->username ?? '') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label></label>
                                <input type="email" name="email" id="inputEmail"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email ?? '') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="col-md-6">
                                <label class="form-label">Password {{ $isEdit ? '(kosongkan jika tidak diganti)' : '' }}
                                    <span class="text-danger">{{ $isEdit ? '' : '*' }}</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    {{ $isEdit ? '' : 'required' }} autocomplete="new-password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            {{-- Level / Hak Akses --}}
                            <div class="col-md-6">
                                <label class="form-label">Level / Hak Akses <span class="text-danger">*</span></label>
                                <select name="role" id="roleSelect"
                                    class="form-select @error('role') is-invalid @enderror" required>
                                    @foreach ($roles as $lvl)
                                        <option value="{{ $lvl }}"
                                            {{ old('role', $user->role ?? '') === $lvl ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $lvl)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach ([0, 1] as $s)
                                        <option value="{{ $s }}"
                                            {{ old('status', $user->status ?? 0) === $s ? 'selected' : '' }}>
                                            {{ $s == 1 ? 'Aktif' : 'Non Aktif' }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Row pencarian personil — muncul hanya untuk role guru / murid / orang_tua --}}
                            @php
                                $existingPersonilId   = old('linked_personil_id', $isEdit ? ($user->personil_id ?? '') : '');
                                $existingPersonilName = $isEdit && $user->personil ? $user->personil->nama : '';
                            @endphp
                            <div class="col-12" id="rowCariPersonil" style="display:none;">
                                <label class="form-label" id="labelCariPersonil">Cari Data</label>
                                <input type="hidden" name="linked_personil_id" id="linked_personil_id"
                                    value="{{ $existingPersonilId }}">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="personilDisplay"
                                        placeholder="Klik tombol Cari untuk memilih..." readonly
                                        style="background-color:#f8f9fa;cursor:pointer;"
                                        value="{{ $existingPersonilName }}"
                                        data-bs-toggle="modal" data-bs-target="#modalCariPersonil">
                                    <button class="btn btn-outline-secondary" type="button"
                                        data-bs-toggle="modal" data-bs-target="#modalCariPersonil">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                                <small class="text-muted">Pilih data untuk mengisi nama &amp; email secara otomatis.</small>
                            </div>
                        </div>

                        @if (!$isEdit)
                            <div class="alert mt-3"
                                style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);border-radius:8px;padding:12px 16px;font-size:13px">
                                <i class="fas fa-shield-halved me-2" style="color:#6366f1"></i>
                                <strong>Keamanan:</strong> Gunakan password minimal 6 karakter yang kuat.
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-accent flex-fill"><i
                                    class="fas fa-save me-2"></i>Simpan</button>
                            <a href="{{ route('seting.user.index') }}" class="btn btn-outline-secondary flex-fill"><i
                                    class="fas fa-times me-2"></i>Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Cari Personil (guru / murid / orang tua) --}}
    <div class="modal fade" id="modalCariPersonil" tabindex="-1" aria-labelledby="modalCariPersonilLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCariPersonilLabel">Pilih Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="searchPersonilInput"
                            placeholder="Ketik nama untuk mencari...">
                    </div>
                    <div class="list-group" id="personilList"></div>
                    <p class="text-muted text-center mt-3 mb-0" id="personilEmptyMsg"
                        style="display:none;font-size:13px">Tidak ada data ditemukan.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const _DATA = {
                guru:      @json($guruList ?? []),
                murid:     @json($muridList ?? []),
                orang_tua: @json($orangTuaList ?? []),
            };

            const ROLES_NEED_SEARCH = ['guru', 'murid', 'orang_tua'];

            const roleSelect       = document.getElementById('roleSelect');
            const rowCari          = document.getElementById('rowCariPersonil');
            const labelCari        = document.getElementById('labelCariPersonil');
            const modalTitleEl     = document.getElementById('modalCariPersonilLabel');
            const personilDisplay  = document.getElementById('personilDisplay');
            const linkedPersonilId = document.getElementById('linked_personil_id');
            const inputName        = document.getElementById('inputName');
            const inputEmail       = document.getElementById('inputEmail');
            const searchInput      = document.getElementById('searchPersonilInput');
            const personilList     = document.getElementById('personilList');
            const emptyMsg         = document.getElementById('personilEmptyMsg');

            function labelForRole(role) {
                return { guru: 'Guru', murid: 'Murid / Siswa', orang_tua: 'Orang Tua / Wali' }[role] ?? 'Data';
            }

            function subInfoForRole(role, item) {
                if (role === 'guru')      return item.nip  ? 'NIP: ' + item.nip  : 'Guru';
                if (role === 'murid')     return item.nis  ? 'NIS: ' + item.nis  : 'Siswa';
                if (role === 'orang_tua') return 'Orang Tua / Wali';
                return '';
            }

            function renderList(role, query) {
                query = (query ?? '').toLowerCase().trim();
                const data     = _DATA[role] ?? [];
                const filtered = query ? data.filter(d => d.nama.toLowerCase().includes(query)) : data;

                personilList.innerHTML = '';
                emptyMsg.style.display = filtered.length === 0 ? '' : 'none';

                filtered.forEach(function(item) {
                    const btn = document.createElement('button');
                    btn.type      = 'button';
                    btn.className = 'list-group-item list-group-item-action';
                    btn.innerHTML =
                        '<strong>' + item.nama + '</strong>' +
                        '<br><small class="text-muted">' + subInfoForRole(role, item) + '</small>';

                    btn.addEventListener('click', function() {
                        personilDisplay.value  = item.nama;
                        linkedPersonilId.value = item.id;
                        inputName.value        = item.nama;
                        // Isi email kalau ada, kosongkan kalau tidak ada
                        inputEmail.value       = item.email ?? '';

                        bootstrap.Modal.getInstance(
                            document.getElementById('modalCariPersonil')
                        )?.hide();
                    });
                    personilList.appendChild(btn);
                });
            }

            function updateRowVisibility() {
                const role = roleSelect.value;
                if (ROLES_NEED_SEARCH.includes(role)) {
                    const label = labelForRole(role);
                    rowCari.style.display    = '';
                    labelCari.textContent    = 'Cari ' + label;
                    modalTitleEl.textContent = 'Pilih ' + label;
                } else {
                    rowCari.style.display  = 'none';
                    personilDisplay.value  = '';
                    linkedPersonilId.value = '';
                }
            }

            roleSelect.addEventListener('change', function() {
                // Reset pilihan lama saat role berganti
                personilDisplay.value  = '';
                linkedPersonilId.value = '';
                updateRowVisibility();
            });

            searchInput.addEventListener('input', function() {
                renderList(roleSelect.value, this.value);
            });

            document.getElementById('modalCariPersonil').addEventListener('show.bs.modal', function() {
                searchInput.value = '';
                renderList(roleSelect.value, '');
            });
            document.getElementById('modalCariPersonil').addEventListener('shown.bs.modal', function() {
                searchInput.focus();
            });

            document.addEventListener('DOMContentLoaded', updateRowVisibility);
        </script>
    @endpush
@endsection
