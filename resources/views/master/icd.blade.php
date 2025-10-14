@extends('layouts.app')

@section('styles')
<style>
/* ================= Table Styling ================= */
.table-icd {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
    table-layout: auto;
    line-height: 1.2;
}

/* Table Header */
.table-icd th {
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
.table-icd th.aksi-col {
    text-align: center; /* khusus kolom Aksi */
}

/* Table Body */
.table-icd td {
    padding: 4px 6px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    border-right: 1px solid #eee;
}

/* Zebra Rows */
.table-icd tbody tr:nth-child(odd) {
    background-color: #fafafa;
}
.table-icd tbody tr:nth-child(even) {
    background-color: #f4f4f4;
}

/* Hover row highlight */
.table-icd tbody tr:hover {
    background-color: #e0f7fa;
}

/* ================= icd Cell & Button Group ================= */
.icd-cell > div {
    display: flex;
    align-items: center;
}

.icd-text {
    flex-grow: 1;
}

.icd-input {
    flex-grow: 1;
    display: none; /* hidden default */
}

.btn-group-icd {
    flex-shrink: 0;
    margin-left: 8px;
}

/* Optional hover colors */
.btn-edit-icd:hover { background-color: #fff3cd; }
.btn-save-icd:hover { background-color: #d4edda; }
.btn-cancel-icd:hover { background-color: #e2e3e5; }
</style>
@endsection

@section('content')
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-3">Master Data ICD</h2>
    
    <div class="mb-3 flex items-center gap-3">
        <input type="text" id="searchIcd" placeholder="Cari berdasarkan Keterangan..." 
               class="border p-2 rounded w-1/3">

        <button id="btnTambahIcd" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">
            + Tambah ICD
        </button>

        <button id="btnRefresh" class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600">
            &#x21bb; Refresh
        </button>
    </div>

    <!-- Modal Tambah ICD -->
    <div id="modalTambahIcd" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow w-96">
            <h2 class="text-lg font-semibold mb-4">Tambah ICD</h2>
            <form id="formTambahIcd">
                @csrf
                <input type="text" name="kode_icd" placeholder="Kode ICD" class="w-full mb-2 border p-2 rounded" required>
                <input type="text" name="diagnosis" placeholder="Keterangan" class="w-full mb-2 border p-2 rounded">
                <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">Simpan</button>
                <button type="button" id="btnCloseModal" class="ml-2 bg-gray-400 text-white px-3 py-2 rounded">Batal</button>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-auto" style="max-height:550px;">
        <table id="tableIcd" class="table-icd">
            <thead>
                <tr>
                    <th>KODE ICD</th>
                    <th>KETERANGAN</th>
                    <th class="aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $p)
                <tr data-id="{{ $p->kode_icd }}">
                    <td>{{ $p->kode_icd }}</td>
                    <td class="icd-cell">
                        <div class="flex items-center justify-between">
                        <span class="icd-text">{{ $p->diagnosis }}</span>
                        <input type="text" class="icd-input hidden border p-1 " data-id="{{ $p->kode_icd }}" value="{{ $p->diagnosis }}">
                            <div class="btn-group-icd flex gap-1">
                                <button type="button" class="btn-edit-icd text-yellow-600 px-2 py-1 rounded hover:bg-yellow-100">✏️</button>
                                <button type="button" class="btn-save-icd text-green-600 px-2 py-1 rounded hover:bg-green-100 hidden">💾Yes</button>
                                <button type="button" class="btn-cancel-icd text-gray-600 px-2 py-1 rounded hover:bg-gray-100 hidden">| ❌No</button>
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
    $('#searchIcd').on('keyup', function(){
        var filter = $(this).val().toLowerCase();
        $('#tableIcd tbody tr').each(function(){
            var nama = $(this).find('.icd-text').text().toLowerCase();
            $(this).toggle(nama.includes(filter));
        });
    });

    // Modal Tambah
    $('#btnTambahIcd').click(()=>$('#modalTambahIcd').removeClass('hidden'));
    $('#btnCloseModal').click(()=>$('#modalTambahIcd').addClass('hidden'));

    // Submit Tambah
    $('#formTambahIcd').submit(function(e){
        e.preventDefault();
        $.post("{{ route('master.icd.store') }}", $(this).serialize(), function(res){
            location.reload(); // refresh tabel setelah simpan
        }).fail(function(){
            alert("Gagal simpan data");
        });
    });

    // Edit Icd
    $(document).on('click','.btn-edit-icd', function(){
        var td = $(this).closest('td');
        td.find('.icd-text').hide();
        td.find('.icd-input').show().focus();
        td.find('.btn-edit-icd').hide();
        td.find('.btn-save-icd, .btn-cancel-icd').show();
    });
    $(document).on('click','.btn-cancel-icd', function(){
        var td = $(this).closest('td');
        td.find('.icd-input').val(td.find('.icd-text').text());
        td.find('.icd-input').hide();
        td.find('.icd-text').show();
        td.find('.btn-save-icd, .btn-cancel-icd').hide();
        td.find('.btn-edit-icd').show();
    });
    $(document).on('click','.btn-save-icd', function(){
        var td = $(this).closest('td');
        var input = td.find('.icd-input');
        var val = input.val().trim();
        var id = input.data('id');
        if(val.length===0){ alert('Keterangan tidak boleh kosong'); return; }
        $.post("{{ route('master.icd.updateField') }}",{id:id,field:'diagnosis',value:val}, function(res){
            if(res.success){
                td.find('.icd-text').text(val);
                td.find('.icd-input').hide();
                td.find('.icd-text').show();
                td.find('.btn-save-icd, .btn-cancel-icd').hide();
                td.find('.btn-edit-icd').show();
            }
        },'json').fail(function(xhr, status, error) {
            console.log('AJAX Error:', status, error);
            alert('Terjadi kesalahan saat menyimpan ICD.');
        });
    });

    /// Tombol Hapus
    $(document).on('click','.btn-hapus',function(){
        var tr = $(this).closest('tr');
        var id = tr.data('id');
        if(!confirm("Yakin hapus data ini?")) return;

        $.ajax({
            url: "/master/icd/" + id,
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
