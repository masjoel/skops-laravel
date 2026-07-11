{{-- Taruh @include('partials.status-murid', ['murid' => $murid]) di halaman detail/edit murid, di mana pun kamu mau tampilkan status murid --}}

<div class="card bg-light mb-3">
    <div class="card-header"><i class="fas fa-user-check me-2" style="color:#6366f1"></i>Status Murid</div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" style="font-size:13px">{{ session('success') }}</div>
        @endif
        @error('status')
            <div class="alert alert-danger" style="font-size:13px">{{ $message }}</div>
        @enderror

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <span class="text-muted" style="font-size:13px">Status saat ini:</span><br>
                @switch($murid->status)
                    @case('aktif')
                        <span class="badge" style="background:rgba(34,197,94,.15);color:#16a34a">Aktif</span>
                    @break

                    @case('lulus')
                        <span class="badge" style="background:rgba(99,102,241,.15);color:#6366f1">Lulus</span>
                    @break

                    @case('keluar')
                        <span class="badge" style="background:rgba(239,68,68,.15);color:#ef4444">Keluar</span>
                    @break

                    @case('pindah')
                        <span class="badge" style="background:rgba(234,179,8,.15);color:#ca8a04">Pindah Sekolah</span>
                    @break
                @endswitch

                @if ($murid->status !== 'aktif')
                    <div class="text-muted mt-1" style="font-size:12px">
                        {{ $murid->tgl_status?->format('d/m/Y') }} -- {{ $murid->keterangan_status }}
                    </div>
                @endif
            </div>

            @if ($murid->status === 'aktif')
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                        data-bs-target="#modalPindah{{ $murid->id }}">
                        <i class="fas fa-arrow-right-from-bracket me-1"></i>Pindah
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#modalKeluar{{ $murid->id }}">
                        <i class="fas fa-user-slash me-1"></i>Keluar
                    </button>
                </div>
            @else
                <form method="POST" action="{{ route('master.murid.aktifkan-kembali', $murid->id) }}"
                    onsubmit="return confirm('Kembalikan status murid ini jadi Aktif?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i>Batalkan, Aktifkan Kembali
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- Modal Keluar --}}
<div class="modal fade" id="modalKeluar{{ $murid->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('master.murid.keluar', $murid->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tandai Murid Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" style="font-size:13px">Murid akan ditandai keluar dari sekolah. Riwayat kelas & poin tetap tersimpan, tidak dihapus.</p>
                    <label class="form-label">Alasan Keluar <span class="text-danger">*</span></label>
                    <textarea name="keterangan_status" class="form-control" rows="3" required
                        placeholder="Contoh: mengundurkan diri karena alasan ekonomi"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tandai Keluar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Pindah --}}
<div class="modal fade" id="modalPindah{{ $murid->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('master.murid.pindah', $murid->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tandai Murid Pindah Sekolah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" style="font-size:13px">Murid akan ditandai pindah ke sekolah lain. Riwayat kelas & poin tetap tersimpan, tidak dihapus.</p>
                    <label class="form-label">Sekolah Tujuan <span class="text-danger">*</span></label>
                    <input type="text" name="keterangan_status" class="form-control" required
                        placeholder="Contoh: SMP Negeri 5">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Tandai Pindah</button>
                </div>
            </form>
        </div>
    </div>
</div>
