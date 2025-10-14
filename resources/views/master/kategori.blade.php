@extends('layouts.app')

@section('styles')
<style>
/* ================= Table Styling ================= */
.table-kategori {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
    table-layout: auto;
    line-height: 1.2;
}

/* Table Header */
.table-kategori th {
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
.table-kategori th.aksi-col {
    text-align: center; /* khusus kolom Aksi */
}

/* Table Body */
.table-kategori td {
    padding: 4px 6px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    border-right: 1px solid #eee;
}

/* Zebra Rows */
.table-kategori tbody tr:nth-child(odd) {
    background-color: #fafafa;
}
.table-kategori tbody tr:nth-child(even) {
    background-color: #f4f4f4;
}

/* Hover row highlight */
.table-kategori tbody tr:hover {
    background-color: #e0f7fa;
}

/* ================= kategori Cell & Button Group ================= */
.kategori-cell > div {
    display: flex;
    align-items: center;
}

.kategori-text {
    flex-grow: 1;
}

.kategori-input {
    flex-grow: 1;
    display: none; /* hidden default */
}

.btn-group-kategori {
    flex-shrink: 0;
    margin-left: 8px;
}

/* Optional hover colors */
.btn-edit-kategori:hover { background-color: #fff3cd; }
.btn-save-kategori:hover { background-color: #d4edda; }
.btn-cancel-kategori:hover { background-color: #e2e3e5; }
</style>
@endsection

@section('content')
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-3">Master Data Kategori</h2>
    
    <div class="mb-3 flex items-center gap-3">
        <input type="text" id="searchKategori" placeholder="Cari berdasarkan nama kategori..." 
               class="border p-2 rounded w-1/3">

        <button id="btnTambahKategori" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">
            + Tambah Kategori
        </button>

        <button id="btnRefresh" class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600">
            &#x21bb; Refresh
        </button>
    </div>

    <!-- Modal Tambah Kategori -->
    <div id="modalTambahKategori" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow w-96">
            <h2 class="text-lg font-semibold mb-4">Tambah Kategori</h2>
            <form id="formTambahKategori">
                @csrf
                <input type="text" name="id_kategori" placeholder="ID Kategori" class="w-full mb-2 border p-2 rounded" required>
                <input type="text" name="kategori" placeholder="Kategori" class="w-full mb-2 border p-2 rounded">
                <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">Simpan</button>
                <button type="button" id="btnCloseModal" class="ml-2 bg-gray-400 text-white px-3 py-2 rounded">Batal</button>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-auto" style="max-height:550px;">
        <table id="tableKategori" class="table-kategori">
            <thead>
                <tr>
                    <th>ID KATEGORI</th>
                    <th>KATEGORI</th>
                    <th class="aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $p)
                <tr data-id="{{ $p->id_kategori }}">
                    <td>{{ $p->id_kategori }}</td>
                    <td class="kategori-cell ralative">
                        <div class="flex items-center justify-between">
                        <span class="kategori-text">{{ $p->kategori }}</span>
                        <input type="text" class="kategori-input hidden border p-1 " data-id="{{ $p->id_kategori }}" value="{{ $p->kategori }}">
                            <div class="btn-group-kategori flex gap-1">
                                <button type="button" class="btn-edit-kategori text-yellow-600 px-2 py-1 rounded hover:bg-yellow-100">✏️</button>
                                <button type="button" class="btn-save-kategori text-green-600 px-2 py-1 rounded hover:bg-green-100 hidden">💾Yes</button>
                                <button type="button" class="btn-cancel-kategori text-gray-600 px-2 py-1 rounded hover:bg-gray-100 hidden">❌No</button>
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
    $('#searchKategori').on('keyup', function(){
        var filter = $(this).val().toLowerCase();
        $('#tableKategori tbody tr').each(function(){
            var nama = $(this).find('.kategori-text').text().toLowerCase();
            $(this).toggle(nama.includes(filter));
        });
    });

    // Modal Tambah
    $('#btnTambahKategori').click(()=>$('#modalTambahKategori').removeClass('hidden'));
    $('#btnCloseModal').click(()=>$('#modalTambahKategori').addClass('hidden'));

    // Submit Tambah
    $('#formTambahKategori').submit(function(e){
        e.preventDefault();
        $.post("{{ route('master.kategori.store') }}", $(this).serialize(), function(res){
            location.reload(); // refresh tabel setelah simpan
        }).fail(function(){
            alert("Gagal simpan data");
        });
    });

    // Edit Kategori
    $(document).on('click','.btn-edit-kategori', function(){
        var td = $(this).closest('td');
        td.find('.kategori-text').hide();
        td.find('.kategori-input').show().focus();
        td.find('.btn-edit-kategori').hide();
        td.find('.btn-save-kategori, .btn-cancel-kategori').show();
    });
    $(document).on('click','.btn-cancel-kategori', function(){
        var td = $(this).closest('td');
        td.find('.kategori-input').val(td.find('.kategori-text').text());
        td.find('.kategori-input').hide();
        td.find('.kategori-text').show();
        td.find('.btn-save-kategori, .btn-cancel-kategori').hide();
        td.find('.btn-edit-kategori').show();
    });
    $(document).on('click','.btn-save-kategori', function(){
        var td = $(this).closest('td');
        var input = td.find('.kategori-input');
        var val = input.val().trim();
        var id = input.data('id');
        if(val.length===0){ alert('Kategori tidak boleh kosong'); return; }
        $.post("{{ route('master.kategori.updateField') }}",{id:id,field:'kategori',value:val}, function(res){
            if(res.success){
                td.find('.kategori-text').text(val);
                td.find('.kategori-input').hide();
                td.find('.kategori-text').show();
                td.find('.btn-save-kategori, .btn-cancel-kategori').hide();
                td.find('.btn-edit-kategori').show();
            } else {
                alert('Gagal memperbarui kategori!');
            }
        }, 'json').fail(function(xhr, status, error) {
            console.log('AJAX Error:', status, error);
            alert('Terjadi kesalahan saat menyimpan Kategori.');
        });

    });

    /// Tombol Hapus
    $(document).on('click','.btn-hapus',function(){
        var tr = $(this).closest('tr');
        var id = tr.data('id');
        if(!confirm("Yakin hapus data ini?")) return;

        $.ajax({
            url: "/master/kategori/" + id,
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
