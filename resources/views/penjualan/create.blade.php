@extends('layouts.app')
@section('title', 'Transaksi Penjualan Baru')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-plus-circle me-2" style="color:#6366f1"></i>Transaksi Penjualan Baru</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('penjualan.index') }}">Penjualan</a></li><li class="breadcrumb-item active">Baru</li></ol>
    </div>
    <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
</div>

<form method="POST" action="{{ route('penjualan.store') }}" id="form-penjualan">
    @csrf
    <div class="row g-3">
        {{-- Header --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fas fa-file-invoice me-2" style="color:#6366f1"></i>Header Transaksi</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">No. Invoice</label>
                            <input type="text" class="form-control" value="{{ $invoice }}" readonly style="background:rgba(99,102,241,.05);font-weight:600;color:#6366f1;font-family:monospace">
                            <input type="hidden" name="invoice" value="{{ $invoice }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_inv" class="form-control @error('tgl_inv') is-invalid @enderror" value="{{ old('tgl_inv', date('Y-m-d')) }}" required>
                            @error('tgl_inv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Customer / Anggota</label>
                            <select name="kdsup" class="form-select">
                                <option value="">-- Umum / Tanpa Member --</option>
                                @foreach($anggota as $a)
                                    <option value="{{ $a->id }}" {{ old('kdsup') == $a->id ? 'selected' : '' }}>
                                        {{ $a->nama }} ({{ $a->gol ?? 'Umum' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Ongkir</label>
                            <input type="number" name="ongkir" class="form-control" value="{{ old('ongkir', 0) }}" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="ket" class="form-control" value="{{ old('ket') }}" placeholder="Keterangan opsional...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Items --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div><i class="fas fa-list me-2" style="color:#6366f1"></i>Item Barang</div>
                    <button type="button" class="btn btn-sm btn-accent" onclick="addRow()">
                        <i class="fas fa-plus me-1"></i>Tambah Baris
                    </button>
                </div>
                <div class="card-body" style="padding:0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="item-table">
                            <thead>
                                <tr>
                                    <th style="width:30px">#</th>
                                    <th style="min-width:300px">Barang</th>
                                    <th style="width:100px">Qty</th>
                                    <th style="width:150px">Harga Jual</th>
                                    <th style="width:120px">Diskon</th>
                                    <th style="width:140px">Subtotal</th>
                                    <th style="width:40px"></th>
                                </tr>
                            </thead>
                            <tbody id="item-body">
                                {{-- Rows added by JS --}}
                            </tbody>
                            <tfoot>
                                <tr style="background:rgba(99,102,241,.03)">
                                    <td colspan="5" class="text-end" style="font-weight:600;padding:12px 14px">Total</td>
                                    <td style="font-weight:700;color:#22c55e;font-size:15px;padding:12px 14px" id="grand-total">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @error('items')<div class="text-danger p-3" style="font-size:13px"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
            <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-accent px-5" id="btn-submit">
                <i class="fas fa-save me-2"></i>Simpan Transaksi
            </button>
        </div>
    </div>
</form>

<style>
.item-row td { padding: 8px 10px; vertical-align: middle; }
.item-row input, .item-row select { font-size: 13px; }
.barang-search { position: relative; }
.barang-dropdown {
    position: absolute; top: 100%; left: 0; right: 0;
    background: var(--card-bg); border: 1px solid var(--border-color);
    border-radius: 8px; z-index: 1000; max-height: 200px; overflow-y: auto;
    box-shadow: 0 4px 12px rgba(0,0,0,.15); display: none;
}
.barang-dropdown-item {
    padding: 8px 12px; cursor: pointer; font-size: 13px;
    transition: background .15s;
    border-bottom: 1px solid var(--border-color);
}
.barang-dropdown-item:hover { background: rgba(99,102,241,.08); }
.barang-dropdown-item:last-child { border-bottom: none; }
</style>

@push('scripts')
<script>
let rowCount = 0;

function fmt(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function addRow(data = {}) {
    rowCount++;
    const idx = rowCount;
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.id = 'row-' + idx;
    tr.innerHTML = `
        <td style="color:var(--text-muted);font-size:12px">${idx}</td>
        <td>
            <div class="barang-search">
                <input type="text" class="form-control barang-input" placeholder="Ketik nama barang..."
                       data-row="${idx}" autocomplete="off"
                       value="${data.namabrg || ''}"
                       oninput="searchBarang(this)">
                <input type="hidden" name="items[${idx}][kdbrg]" class="kdbrg-input" value="${data.kdbrg || ''}">
                <div class="barang-dropdown" id="dd-${idx}"></div>
            </div>
        </td>
        <td><input type="number" name="items[${idx}][qty]" class="form-control qty-input" value="${data.qty || 1}" min="1" oninput="calcRow(${idx})"></td>
        <td><input type="number" name="items[${idx}][hrg]" class="form-control hrg-input" value="${data.hrg || 0}" min="0" oninput="calcRow(${idx})"></td>
        <td><input type="number" name="items[${idx}][disc]" class="form-control disc-input" value="${data.disc || 0}" min="0" oninput="calcRow(${idx})"></td>
        <td><span class="subtotal-display fw-bold" style="color:#22c55e;font-size:13px">${fmt(0)}</span></td>
        <td>
            <button type="button" class="btn btn-sm" style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px" onclick="removeRow(${idx})">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;
    document.getElementById('item-body').appendChild(tr);
    if (data.kdbrg) calcRow(idx);
}

function removeRow(idx) {
    const row = document.getElementById('row-' + idx);
    if (row) row.remove();
    calcGrand();
}

function calcRow(idx) {
    const row   = document.getElementById('row-' + idx);
    const qty   = parseFloat(row.querySelector('.qty-input').value) || 0;
    const hrg   = parseFloat(row.querySelector('.hrg-input').value) || 0;
    const disc  = parseFloat(row.querySelector('.disc-input').value) || 0;
    const sub   = Math.max(0, qty * hrg - disc);
    row.querySelector('.subtotal-display').textContent = fmt(sub);
    calcGrand();
}

function calcGrand() {
    let total = 0;
    document.querySelectorAll('.subtotal-display').forEach(el => {
        const val = el.textContent.replace(/[^0-9]/g, '');
        total += parseFloat(val) || 0;
    });
    document.getElementById('grand-total').textContent = fmt(total);
}

let searchTimer;
function searchBarang(input) {
    const idx = input.dataset.row;
    const q   = input.value.trim();
    const dd  = document.getElementById('dd-' + idx);

    if (q.length < 2) { dd.style.display = 'none'; return; }

    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        fetch(`/api/barang/search?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
                dd.innerHTML = '';
                if (!data.length) { dd.style.display = 'none'; return; }
                data.forEach(b => {
                    const item = document.createElement('div');
                    item.className = 'barang-dropdown-item';
                    item.innerHTML = `<strong>${b.namabrg}</strong> <span style="color:var(--text-muted);font-size:11px">${b.kdbrg}</span>
                                      <span style="float:right;color:#22c55e;font-size:12px">Rp ${Number(b.hrg1).toLocaleString('id-ID')}</span>`;
                    item.onclick = () => selectBarang(idx, b);
                    dd.appendChild(item);
                });
                dd.style.display = 'block';
            });
    }, 250);
}

function selectBarang(idx, b) {
    const row = document.getElementById('row-' + idx);
    row.querySelector('.barang-input').value = b.namabrg;
    row.querySelector('.kdbrg-input').value  = b.id;
    row.querySelector('.hrg-input').value    = b.hrg1;
    document.getElementById('dd-' + idx).style.display = 'none';
    calcRow(idx);
}

// Close dropdowns on outside click
document.addEventListener('click', e => {
    if (!e.target.classList.contains('barang-input')) {
        document.querySelectorAll('.barang-dropdown').forEach(d => d.style.display = 'none');
    }
});

// Init with 1 row
addRow();

// Submit validation
document.getElementById('form-penjualan').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('.item-row');
    let valid  = true;
    rows.forEach(row => {
        if (!row.querySelector('.kdbrg-input').value) valid = false;
    });
    if (!valid || !rows.length) {
        e.preventDefault();
        alert('Pilih barang untuk setiap baris item.');
    }
});
</script>
@endpush
@endsection
