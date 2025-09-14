@extends('layouts.app')

@section('styles')
<style>
/* Toggle Switch CSS */
.switch { 
    position: relative; 
    display:inline-block; 
    width:46px; 
    height:24px; 
}
.switch input { display:none; }
.slider { 
    position:absolute; 
    cursor:pointer; 
    top:0; left:0; right:0; bottom:0; 
    background:#ccc; 
    transition:.3s; 
    border-radius:24px; 
}
.slider:before { 
    position:absolute; 
    content:""; 
    height:18px; width:18px; 
    left:3px; bottom:3px; 
    background:white; 
    transition:.3s; 
    border-radius:50%; 
}
input:checked + .slider { background: #f59e0b; }
input:checked + .slider:before { transform: translateX(22px); }

/* Table Styling */
.table-obat {
    width: 100%;
    border-collapse: collapse;
    font-size: 16px;
    table-layout: auto;
    line-height: 1.2;
}
.table-obat th {
    position: sticky;
    top: 0;
    z-index: 10;
    padding: 8px 6px;
    background-color: #dde5ecff;
    font-weight: 700;
    white-space: nowrap;
    border-bottom: 1px solid #ccc;
}
.table-obat td {
    padding: 2px 4px;
    vertical-align: middle;
    white-space: nowrap;
    border-bottom: 1px solid #eee;
}

/* Zebra Rows */
.table-obat tbody tr:nth-child(odd) { background-color: #fafafa; }
.table-obat tbody tr:nth-child(even) { background-color: #f4f4f4; }

/* Hover row highlight */
.table-obat tbody tr:hover { background-color: #e0f7fa; }

/* Compact Select/Input */
.select-compact { padding: .25rem .45rem; font-size: .9rem; }
.input-compact { padding: .25rem .45rem; font-size: .9rem; width: 80px; }

/* Inline editable input */
.inline-input { border: 1px solid #ccc; padding: 2px 4px; width: 150px; }

/* Button CRUD */
.crud-btn { margin-right: 8px; }

.nama-obat-cell {
    position: relative;
    padding-right: 115px; /* ruang untuk tombol kanan */
    white-space: nowrap;
}

.btn-group-obat {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    white-space: nowrap;
}

.nama-obat-input,
.nama-obat-text {
    display: inline-block;
    max-width: calc(100% - 115px);
    width: 100%;
    vertical-align: middle;
    box-sizing: border-box;
}

.nama-obat-input {
    height: 28px;
    font-size: 16px;
}
</style>
@endsection

@section('content')
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-3">Master Data Obat</h2>
    
    <div class="mb-3">
        <input type="text" id="searchObat" placeholder="Cari berdasarkan Nama Obat..." class="border p-2 rounded w-1/3">

        <button id="btnTambahObat" class="bg-green-500 text-white px-3 py-2 rounded mb-3 hover:bg-green-600">
            + Tambah Obat
        </button>

        <button id="btnRefresh" class="bg-yellow-500 text-white px-3 py-2 rounded mb-3 hover:bg-yellow-600">
            Refresh
        </button>

        <!-- Modal Tambah Obat -->
        <div id="modalTambahObat" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow w-96">
                <h2 class="text-lg font-semibold mb-4">Tambah Obat</h2>
                <form id="formTambahObat">
                    <input type="text" name="kode_obat" placeholder="Kode Obat" class="w-full mb-2 border p-2 rounded" required>
                    <input type="text" name="nama_obat" placeholder="Nama Obat" class="w-full mb-2 border p-2 rounded" required>
                    <input type="number" name="stok_minimal" placeholder="Stok Minimal" class="w-full mb-2 border p-2 rounded">
                    <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">Simpan</button>
                    <button type="button" id="btnCloseModal" class="ml-2 bg-gray-400 text-white px-3 py-2 rounded">Batal</button>
                </form>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-auto" style="max-height:700px;">
        <table id="tableObat" class="table-obat">
            <thead>
                <tr>
                    <th>KODE</th>
                    <th>NAMA OBAT</th>
                    <th>KATEGORI</th>
                    <th>SATUAN</th>
                    <th>MINIMUM</th>
                    <th>GOLONGAN</th>
                    <th>Aktif</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($obat as $o)
                <tr data-id="{{ $o->id_obat }}">
                    <td class="kode_obat">{{ $o->kode_obat }}</td>
                    <td class="nama-obat-cell">
                        <span class="nama-obat-text">{{ $o->nama_obat }}</span>
                        <input type="text" class="nama-obat-input hidden" data-id="{{ $o->id_obat }}" value="{{ $o->nama_obat }}">
                        <div class="btn-group-obat">
                            <button class="btn btn-sm btn-warning btn-edit-nama">✏️</button>
                            <button class="btn btn-sm btn-success btn-save-nama hidden">💾Yes</button>
                            <button class="btn btn-sm btn-secondary btn-cancel-nama hidden">| ❌No</button>
                        </div>
                    </td>
                    <td>
                        <select class="kategori-select select-compact" data-id="{{ $o->id_obat }}">
                            <option value="">-</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id_kategori }}" {{ ($o->kategori == $k->id_kategori) ? 'selected' : '' }}>{{ $k->kategori }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="satuan-select select-compact" data-id="{{ $o->id_obat }}">
                            <option value="">-</option>
                            @foreach($satuan as $s)
                                <option value="{{ $s->id_satuan }}" {{ ($o->satuan == $s->id_satuan) ? 'selected' : '' }}>{{ $s->satuan }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" class="stok-minimal input-compact" data-id="{{ $o->id_obat }}" value="{{ $o->stok_minimal }}">
                    </td>
                    <td>
                        <select class="golongan-select select-compact" data-id="{{ $o->id_obat }}">
                            <option value="">-</option>
                            @foreach($golongan as $g)
                                <option value="{{ $g->id_gol }}" {{ ($o->golongan == $g->id_gol) ? 'selected' : '' }}>
                                    {{ $g->nama_golongan ?? $g->golongan ?? $g->id_gol }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" class="toggle-aktif" data-id="{{ $o->id_obat }}" {{ $o->is_aktif ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </td>
                    <td>
                        <button class="btn-hapus bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Toggle Aktif
    $(document).on('change','.toggle-aktif', function() {
        var id = $(this).data('id');
        var val = $(this).is(':checked') ? 1 : 0;
        $.post("{{ route('master.obat.updateField') }}", { id:id, field:'is_aktif', value: val });
    });

    // Kategori/Satuan/Golongan
    $(document).on('change','.kategori-select,.satuan-select,.golongan-select', function(){
        var id = $(this).data('id');
        var field = $(this).hasClass('kategori-select') ? 'kategori' :
                    $(this).hasClass('satuan-select') ? 'satuan' : 'golongan';
        $.post("{{ route('master.obat.updateField') }}", { id:id, field:field, value: $(this).val() });
    });

    // Stok Minimal
    $(document).on('blur','.stok-minimal', function(){
        var id = $(this).data('id');
        $.post("{{ route('master.obat.updateField') }}", { id:id, field:'stok_minimal', value: $(this).val() });
    });

    // Edit Nama Obat
    $(document).on('click', '.btn-edit-nama', function() {
        var td = $(this).closest('td');
        td.find('.nama-obat-text').addClass('hidden');
        td.find('.nama-obat-input').removeClass('hidden').focus();
        td.find('.btn-edit-nama').addClass('hidden');
        td.find('.btn-save-nama, .btn-cancel-nama').removeClass('hidden');
    });

    // Cancel Edit
    $(document).on('click', '.btn-cancel-nama', function() {
        var td = $(this).closest('td');
        td.find('.nama-obat-input').val(td.find('.nama-obat-text').text());
        td.find('.nama-obat-input').addClass('hidden');
        td.find('.nama-obat-text').removeClass('hidden');
        td.find('.btn-save-nama, .btn-cancel-nama').addClass('hidden');
        td.find('.btn-edit-nama').removeClass('hidden');
    });

    // Save Edit
    $(document).on('click', '.btn-save-nama', function() {
        var td = $(this).closest('td');
        var input = td.find('.nama-obat-input');
        var val = input.val().trim();
        var id = input.data('id');
        if(val.length === 0){ alert('Nama obat tidak boleh kosong'); return; }

        $.post("{{ route('master.obat.updateField') }}", { id:id, field:'nama_obat', value:val }, function(res){
            if(res.success){
                td.find('.nama-obat-text').text(val);
                td.find('.nama-obat-input').addClass('hidden');
                td.find('.nama-obat-text').removeClass('hidden');
                td.find('.btn-save-nama, .btn-cancel-nama').addClass('hidden');
                td.find('.btn-edit-nama').removeClass('hidden');
            } else { alert('Gagal update'); }
        }, 'json');
    });

    // Hapus Row
    $(document).on('click', '.btn-hapus', function() {
        var tr = $(this).closest('tr');
        var id = tr.data('id');
        if(!confirm('Yakin ingin hapus data ini?')) return;
        if(id){
            $.ajax({
                url: "/master/obat/" + id,
                type: 'DELETE',
                dataType: 'json',
                success: function(res){ if(res.success) tr.remove(); else alert('Gagal hapus'); },
                error: function(){ alert('Error hapus'); }
            });
        } else { tr.remove(); }
    });

    // Modal Tambah Obat
    $('#btnTambahObat').click(function(){ $('#modalTambahObat').removeClass('hidden'); });
    $('#btnCloseModal').click(function(){ $('#modalTambahObat').addClass('hidden'); });
    $('#formTambahObat').submit(function(e){
        e.preventDefault();
        $.post("{{ route('master.obat.store') }}", $(this).serialize(), function(res){
            if(res.success){ alert(res.message); location.reload(); }
        });
    });

    // Refresh
    $('#btnRefresh').click(function(){ location.reload(); });

    // ---------- Manual Search Nama Obat ----------
    $('#searchObat').on('keyup', function() {
        var filter = $(this).val().toLowerCase();
        $('#tableObat tbody tr').each(function() {
            var nama = $(this).find('.nama-obat-text').text().toLowerCase();
            $(this).toggle(nama.includes(filter));
        });
    });
});
</script>
@endsection
