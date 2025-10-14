@extends('layouts.app')

@section('styles')
<style>
/* ================= Table Styling ================= */
.table-satuan {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
    table-layout: auto;
    line-height: 1.2;
}

/* Table Header */
.table-satuan th {
    position: sticky;
    top: 0;
    z-index: 10;
    padding: 8px 6px;
    background-color: #dde5ecff;
    font-weight: 700;
    white-space: nowrap;
    border-bottom: 1px solid #ccc;
    border-right: 1px solid #ccc;
    text-align: left;
}
.table-satuan th.aksi-col {
    text-align: center; /* khusus kolom Aksi */
}

/* Table Body */
.table-satuan td {
    padding: 4px 6px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    border-right: 1px solid #eee;
}

/* Zebra Rows */
.table-satuan tbody tr:nth-child(odd) {
    background-color: #fafafa;
}
.table-satuan tbody tr:nth-child(even) {
    background-color: #f4f4f4;
}

/* Hover row highlight */
.table-satuan tbody tr:hover {
    background-color: #e0f7fa;
}

/* ================= satuan Cell & Button Group ================= */
.satuan-cell > div {
    display: flex;
    align-items: center;
}

.satuan-text {
    flex-grow: 1;
}

.satuan-input {
    flex-grow: 1;
    display: none; /* hidden default */
}

.btn-group-satuan {
    flex-shrink: 0;
    margin-left: 8px;
}

/* Optional hover colors */
.btn-edit-satuan:hover { background-color: #fff3cd; }
.btn-save-satuan:hover { background-color: #d4edda; }
.btn-cancel-satuan:hover { background-color: #e2e3e5; }
</style>
@endsection

@section('content')
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-3">Master Data Satuan</h2>
    
    <div class="mb-3 flex items-center gap-3">
        <input type="text" id="searchSatuan" placeholder="Cari berdasarkan nama satuan..." 
               class="border p-2 rounded w-1/3">

        <button id="btnTambahSatuan" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">
            + Tambah Satuan
        </button>

        <button id="btnRefresh" class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600">
            &#x21bb; Refresh
        </button>
    </div>

    <!-- Modal Tambah Satuan -->
    <div id="modalTambahSatuan" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow w-96">
            <h2 class="text-lg font-semibold mb-4">Tambah Satuan</h2>
            <form id="formTambahSatuan">
                @csrf
                <input type="text" name="id_satuan" placeholder="ID Satuan" class="w-full mb-2 border p-2 rounded" required>
                <input type="text" name="satuan" placeholder="Satuan" class="w-full mb-2 border p-2 rounded">
                <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">Simpan</button>
                <button type="button" id="btnCloseModal" class="ml-2 bg-gray-400 text-white px-3 py-2 rounded">Batal</button>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-auto" style="max-height:550px;">
        <table id="tableSatuan" class="table-satuan">
            <thead>
                <tr>
                    <th>ID SATUAN</th>
                    <th>SATUAN</th>
                    <th class="aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $p)
                <tr data-id="{{ $p->id_satuan }}">
                    <td>{{ $p->id_satuan }}</td>
                    <td class="satuan-cell">
                        <div class="flex items-center justify-between">
                            <span class="satuan-text">{{ $p->satuan }}</span>
                            <input type="text" class="satuan-input hidden border p-1" data-id="{{ $p->id_satuan }}" value="{{ $p->satuan }}">
                            <div class="btn-group-satuan flex gap-1">
                                <button type="button" class="btn-edit-satuan text-yellow-600 px-2 py-1 rounded hover:bg-yellow-100">✏️</button>
                                <button type="button" class="btn-save-satuan text-green-600 px-2 py-1 rounded hover:bg-green-100 hidden">💾Yes</button>
                                <button type="button" class="btn-cancel-satuan text-gray-600 px-2 py-1 rounded hover:bg-gray-100 hidden">| ❌No</button>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn-hapus bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
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
$(function(){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Search manual
    $('#searchSatuan').on('keyup', function(){
        var filter = $(this).val().toLowerCase();
        $('#tableSatuan tbody tr').each(function(){
            var nama = $(this).find('.satuan-text').text().toLowerCase();
            $(this).toggle(nama.includes(filter));
        });
    });

    // Modal Tambah
    $('#btnTambahSatuan').click(()=>$('#modalTambahSatuan').removeClass('hidden'));
    $('#btnCloseModal').click(()=>$('#modalTambahSatuan').addClass('hidden'));

    // Submit Tambah
    $('#formTambahSatuan').submit(function(e){
        e.preventDefault();
        $.post("{{ route('master.satuan.store') }}", $(this).serialize(), function(res){
            location.reload(); // refresh tabel setelah simpan
        }).fail(function(){
            alert("Gagal simpan data");
        });
    });

    // Edit Satuan
    $(document).on('click','.btn-edit-satuan', function(){        
        var td = $(this).closest('td');
        td.find('.satuan-text').hide();
        td.find('.satuan-input').show().focus();
        td.find('.btn-edit-satuan').hide();
        td.find('.btn-save-satuan, .btn-cancel-satuan').show();
    });
    $(document).on('click','.btn-cancel-satuan', function(){        
        var td = $(this).closest('td');
        td.find('.satuan-input').val(td.find('.satuan-text').text());
        td.find('.satuan-input').hide();
        td.find('.satuan-text').show();
        td.find('.btn-save-satuan, .btn-cancel-satuan').hide();
        td.find('.btn-edit-satuan').show();
    });
    $(document).on('click','.btn-save-satuan', function(){
        var td = $(this).closest('td');
        var input = td.find('.satuan-input');
        var val = input.val().trim();
        var id = input.data('id');
        if(val.length===0){ alert('Satuan tidak boleh kosong'); return; }
        $.post("{{ route('master.satuan.updateField') }}",{id:id,field:'satuan',value:val}, function(res){
            if(res.success){
                td.find('.satuan-text').text(val);
                td.find('.satuan-input').hide();
                td.find('.satuan-text').show();
                td.find('.btn-save-satuan, .btn-cancel-satuan').hide();
                td.find('.btn-edit-satuan').show();
            } else {
                alert('Gagal memperbarui Satuan!');
            }
        },'json').fail(function(xhr, status, error) {
            console.log('AJAX Error:', status, error);
            alert('Terjadi kesalahan saat menyimpan Satuan.');
        });
    });

    /// Tombol Hapus
    $(document).on('click','.btn-hapus',function(){
        var tr = $(this).closest('tr');
        var id = tr.data('id');
        if(!confirm("Yakin hapus data ini?")) return;

        $.ajax({
            url: "/master/satuan/" + id,
            type: "DELETE",
            dataType: "json",
            success: function(res){
                if(res.success){ tr.remove(); }
                else { alert("Gagal hapus data"); }
            },
            error: function(){ alert("Error hapus data"); }
        });
    });

    // Refresh
    $('#btnRefresh').click(()=>location.reload());
});
</script>
@endsection
