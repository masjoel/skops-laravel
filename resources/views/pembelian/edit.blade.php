@extends('layouts.app')
@section('title', 'Edit Pembelian — '.$transaksi->invoice)
@section('content')
<div class="page-header">
    <div>
        <h1><i class="fas fa-pen me-2" style="color:#6366f1"></i>Edit Pembelian</h1>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('pembelian.index') }}">Pembelian</a></li><li class="breadcrumb-item active">Edit</li></ol>
    </div>
    <a href="{{ route('pembelian.show', $transaksi->invoice) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
</div>
<form method="POST" action="{{ route('pembelian.update', $transaksi->invoice) }}" id="form-pembelian">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fas fa-file-invoice me-2" style="color:#6366f1"></i>Header Pembelian</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label">No. Invoice</label><input type="text" class="form-control" value="{{ $transaksi->invoice }}" readonly style="background:rgba(99,102,241,.05);font-weight:600;color:#6366f1;font-family:monospace"></div>
                        <div class="col-md-3"><label class="form-label">Tanggal <span class="text-danger">*</span></label><input type="date" name="tgl_inv" class="form-control" value="{{ old('tgl_inv',$transaksi->tgl_inv?->format('Y-m-d')??'') }}" required></div>
                        <div class="col-md-3"><label class="form-label">Jatuh Tempo</label><input type="date" name="tgl_jt" class="form-control" value="{{ old('tgl_jt',$transaksi->tgl_jt?->format('Y-m-d')??'') }}"></div>
                        <div class="col-md-3"><label class="form-label">Ongkir</label><input type="number" name="ongkir" class="form-control" value="{{ old('ongkir',$transaksi->ongkir??0) }}" min="0"></div>
                        <div class="col-md-6"><label class="form-label">Suplier</label><select name="kdsup" class="form-select"><option value="">-- Pilih Suplier --</option>@foreach($supliers as $s)<option value="{{ $s->id }}" {{ $transaksi->kdsup==$s->id?'selected':'' }}>{{ $s->nama }}</option>@endforeach</select></div>
                        <div class="col-md-6"><label class="form-label">Keterangan</label><input type="text" name="ket" class="form-control" value="{{ old('ket',$transaksi->ket) }}"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between"><div><i class="fas fa-list me-2" style="color:#6366f1"></i>Item Pembelian</div><button type="button" class="btn btn-sm btn-accent" onclick="addRow()"><i class="fas fa-plus me-1"></i>Tambah Baris</button></div>
                <div class="card-body" style="padding:0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th style="width:30px">#</th><th style="min-width:280px">Barang</th><th style="width:90px">Qty</th><th style="width:150px">Harga Beli</th><th style="width:140px">Subtotal</th><th style="width:40px"></th></tr></thead>
                            <tbody id="item-body"></tbody>
                            <tfoot><tr style="background:rgba(99,102,241,.03)"><td colspan="4" class="text-end" style="font-weight:600;padding:12px 14px">Total</td><td style="font-weight:700;color:#f59e0b;font-size:15px;padding:12px 14px" id="grand-total">Rp 0</td><td></td></tr></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 d-flex justify-content-end gap-2">
            <a href="{{ route('pembelian.show',$transaksi->invoice) }}" class="btn btn-outline-secondary px-4">Batal</a>
            <button type="submit" class="btn btn-accent px-5"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
        </div>
    </div>
</form>
<style>.item-row td{padding:8px 10px;vertical-align:middle}.barang-search{position:relative}.barang-dropdown{position:absolute;top:100%;left:0;right:0;background:var(--card-bg);border:1px solid var(--border-color);border-radius:8px;z-index:1000;max-height:200px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,.15);display:none}.barang-dropdown-item{padding:8px 12px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--border-color);transition:background .15s}.barang-dropdown-item:hover{background:rgba(99,102,241,.08)}</style>
@push('scripts')
<script>
let rowCount=0;
const existingItems=@json($transaksi->detail->map(fn($d)=>['kdbrg'=>$d->kdbrg,'namabrg'=>$d->barang?->namabrg??'-','qty'=>$d->qty,'hrg'=>$d->hrg]));
function fmt(n){return 'Rp '+Number(n).toLocaleString('id-ID');}
function addRow(data={}){rowCount++;const idx=rowCount;const tr=document.createElement('tr');tr.className='item-row';tr.id='row-'+idx;tr.innerHTML=`<td style="color:var(--text-muted);font-size:12px">${idx}</td><td><div class="barang-search"><input type="text" class="form-control barang-input" placeholder="Ketik nama barang..." data-row="${idx}" autocomplete="off" value="${data.namabrg||''}" oninput="searchBarang(this)"><input type="hidden" name="items[${idx}][kdbrg]" class="kdbrg-input" value="${data.kdbrg||''}"><div class="barang-dropdown" id="dd-${idx}"></div></div></td><td><input type="number" name="items[${idx}][qty]" class="form-control qty-input" value="${data.qty||1}" min="1" oninput="calcRow(${idx})"></td><td><input type="number" name="items[${idx}][hrg]" class="form-control hrg-input" value="${data.hrg||0}" min="0" oninput="calcRow(${idx})"></td><td><span class="subtotal-display fw-bold" style="color:#f59e0b;font-size:13px">${fmt(0)}</span></td><td><button type="button" class="btn btn-sm" style="padding:4px 8px;background:rgba(239,68,68,.1);color:#ef4444;border-radius:6px" onclick="removeRow(${idx})"><i class="fas fa-times"></i></button></td>`;document.getElementById('item-body').appendChild(tr);if(data.kdbrg)calcRow(idx);}
function removeRow(idx){document.getElementById('row-'+idx)?.remove();calcGrand();}
function calcRow(idx){const row=document.getElementById('row-'+idx);const sub=Math.max(0,(parseFloat(row.querySelector('.qty-input').value)||0)*(parseFloat(row.querySelector('.hrg-input').value)||0));row.querySelector('.subtotal-display').textContent=fmt(sub);calcGrand();}
function calcGrand(){let t=0;document.querySelectorAll('.subtotal-display').forEach(el=>t+=parseFloat(el.textContent.replace(/[^0-9]/g,''))||0);document.getElementById('grand-total').textContent=fmt(t);}
let st;
function searchBarang(input){const idx=input.dataset.row,q=input.value.trim(),dd=document.getElementById('dd-'+idx);if(q.length<2){dd.style.display='none';return;}clearTimeout(st);st=setTimeout(()=>fetch(`/api/barang/search?q=${encodeURIComponent(q)}`).then(r=>r.json()).then(data=>{dd.innerHTML='';if(!data.length){dd.style.display='none';return;}data.forEach(b=>{const el=document.createElement('div');el.className='barang-dropdown-item';el.innerHTML=`<strong>${b.namabrg}</strong><span style="color:var(--text-muted);font-size:11px"> ${b.kdbrg}</span><span style="float:right;color:#f59e0b;font-size:12px">Rp ${Number(b.hrg_beli).toLocaleString('id-ID')}</span>`;el.onclick=()=>{const row=document.getElementById('row-'+idx);row.querySelector('.barang-input').value=b.namabrg;row.querySelector('.kdbrg-input').value=b.id;row.querySelector('.hrg-input').value=b.hrg_beli;dd.style.display='none';calcRow(idx);};dd.appendChild(el);});dd.style.display='block';}),250);}
document.addEventListener('click',e=>{if(!e.target.classList.contains('barang-input'))document.querySelectorAll('.barang-dropdown').forEach(d=>d.style.display='none');});
existingItems.forEach(item=>addRow(item));
</script>
@endpush
@endsection
