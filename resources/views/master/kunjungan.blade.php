@extends('layouts.app')

@section('styles')
<style>
/* ================= Table Styling ================= */
.table-kunjungan {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
    table-layout: auto;
    line-height: 1.2;
}

/* Table Header */
.table-kunjungan th {
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
.table-kunjungan th.aksi-col {
    text-align: center; /* khusus kolom Aksi */
}

/* Table Body */
.table-kunjungan td {
    padding: 4px 6px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    border-right: 1px solid #eee;
}

/* Zebra Rows */
.table-kunjungan tbody tr:nth-child(odd) {
    background-color: #fafafa;
}
.table-kunjungan tbody tr:nth-child(even) {
    background-color: #f4f4f4;
}

/* Hover row highlight */
.table-kunjungan tbody tr:hover {
    background-color: #e0f7fa;
}

/* ================= Kunjungan Cell & Button Group ================= */
.kunjungan-cell > div {
    display: flex;
    align-items: center;
}

.kunjungan-text {
    flex-grow: 1;
}

.kunjungan-input {
    flex-grow: 1;
    display: none; /* hidden default */
}

.btn-group-kunjungan {
    flex-shrink: 0;
    margin-left: 8px;
}

/* Optional hover colors */
.btn-edit-kunjungan:hover { background-color: #fff3cd; }
.btn-save-kunjungan:hover { background-color: #d4edda; }
.btn-cancel-kunjungan:hover { background-color: #e2e3e5; }
</style>
@endsection

@section('content')
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-3">Master Data Kunjungan</h2>
    
    <div class="mb-3 flex items-center gap-3">
        <input type="text" id="searchKunjungan" placeholder="Cari berdasarkan Jenis Kunjungan..." 
               class="border p-2 rounded w-1/3">

        <button id="btnTambahKunjungan" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">
            + Tambah Kunjungan
        </button>

        <button id="btnRefresh" class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600">
            &#x21bb; Refresh
        </button>
    </div>

    <!-- Modal Tambah Kunjungan -->
    <div id="modalTambahKunjungan" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow w-96">
            <h2 class="text-lg font-semibold mb-4">Tambah Kunjungan</h2>
            <form id="formTambahKunjungan">
                @csrf
                <input type="text" name="id_kunjungan" placeholder="ID Kunjungan" class="w-full mb-2 border p-2 rounded" required>
                <input type="text" name="jenis_kunjungan" placeholder="jenis Kunjungan" class="w-full mb-2 border p-2 rounded">
                <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">Simpan</button>
                <button type="button" id="btnCloseModal" class="ml-2 bg-gray-400 text-white px-3 py-2 rounded">Batal</button>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-auto" style="max-height:600px;">
        <table id="tableKunjungan" class="table-kunjungan">
            <thead>
                <tr>
                    <th>ID KUNJUNGAN</th>
                    <th>JENIS KUNJUNGAN</th>
                    <th class="aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $p)
                <tr data-id="{{ $p->id_kunjungan }}">
                    <td>{{ $p->id_kunjungan }}</td>
                    <td class="kunjungan-cell relative">
			        <div class="flex items-center justify-between">
                        <span class="kunjungan-text">{{ $p->jenis_kunjungan }}</span>
                        <input type="text" class="kunjungan-input hidden border p-1 " data-id="{{ $p->id_kunjungan }}" value="{{ $p->jenis_kunjungan }}">
                        <div class="btn-group-kunjungan flex gap-1">
                            <button type="button" class="btn-edit-kunjungan text-yellow-600 px-2 py-1 rounded hover:bg-yellow-100">✏️</button>
                            <button type="button" class="btn-save-kunjungan text-green-600 px-2 py-1 rounded hover:bg-green-100 hidden">💾Yes</button>
                            <button type="button" class="btn-cancel-kunjungan text-gray-600 px-2 py-1 rounded hover:bg-gray-100 hidden">❌No</button>
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
    $('#searchKunjungan').on('keyup', function(){
        var filter = $(this).val().toLowerCase();
        $('#tableKunjungan tbody tr').each(function(){
            var nama = $(this).find('.kunjungan-text').text().toLowerCase();
            $(this).toggle(nama.includes(filter));
        });
    });

    // Modal Tambah
    $('#btnTambahKunjungan').click(()=>$('#modalTambahKunjungan').removeClass('hidden'));
    $('#btnCloseModal').click(()=>$('#modalTambahKunjungan').addClass('hidden'));

    // Submit Tambah
    $('#formTambahKunjungan').submit(function(e){
        e.preventDefault();
        $.post("{{ route('master.kunjungan.store') }}", $(this).serialize(), function(res){
            location.reload(); // refresh tabel setelah simpan
        }).fail(function(){
            alert("Gagal simpan data");
        });
    });

    // Edit Kunjungan
    $(document).on('click','.btn-edit-kunjungan', function() {        
        var td = $(this).closest('td');
        td.find('.kunjungan-text').hide();
        td.find('.kunjungan-input').show().focus();
        td.find('.btn-edit-kunjungan').hide();
        td.find('.btn-save-kunjungan, .btn-cancel-kunjungan').show();
    });
    $(document).on('click','.btn-cancel-kunjungan', function(){        
        var td = $(this).closest('td');
        td.find('.kunjungan-input').val(td.find('.kunjungan-text').text());
        td.find('.kunjungan-input').hide();
        td.find('.kunjungan-text').show();
        td.find('.btn-save-kunjungan, .btn-cancel-kunjungan').hide();
        td.find('.btn-edit-kunjungan').show();
    });
    $(document).on('click','.btn-save-kunjungan', function(){
        var td = $(this).closest('td');
        var input = td.find('.kunjungan-input');
        var val = input.val().trim();
        var id = input.data('id');

        if(val.length===0){ 
            alert('Jenis Kunjungan tidak boleh kosong'); 
            return; 
        }
        $.post("{{ route('master.kunjungan.updateField') }}",{
            id:id,
            field:'jenis_kunjungan',
            value:val
        }, function(res){
            if (res.success) {
                td.find('.kunjungan-text').text(val);
                td.find('.kunjungan-input').hide();
                td.find('.kunjungan-text').show();
                td.find('.btn-save-kunjungan, .btn-cancel-kunjungan').hide();
                td.find('.btn-edit-kunjungan').show();
            } else {
                alert('Gagal memperbarui Jenis Kunjungan!');
            }
        }, 'json').fail(function(xhr, status, error) {
            console.log('AJAX Error:', status, error);
            alert('Terjadi kesalahan saat menyimpan Jenis Kunjungan.');
        });

    });

    /// Tombol Hapus
    $(document).on('click','.btn-hapus',function(){
        var tr = $(this).closest('tr');
        var id = tr.data('id');
        if(!confirm("Yakin hapus data ini?")) return;

        $.ajax({
            url: "/master/kunjungan/" + id,
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
