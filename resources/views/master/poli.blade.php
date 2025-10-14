@extends('layouts.app')

@section('styles')
<style>
/* Table Styling */
.table-poli {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
    table-layout: auto;
    line-height: 1.2;
}
.table-poli th {
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
.table-poli th.aksi-col {
    text-align: center; /* ✅ khusus kolom Aksi rata tengah */
}
.table-poli td {
    padding: 4px 6px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    border-right: 1px solid #eee;
}

/* Zebra Rows */
.table-poli tbody tr:nth-child(odd) { background-color: #fafafa; }
.table-poli tbody tr:nth-child(even) { background-color: #f4f4f4; }

/* Hover row highlight */
.table-poli tbody tr:hover { background-color: #e0f7fa; }

/* Button group inline */
.nama-poli-cell {
    position: relative;
    padding-right: 115px;
    white-space: nowrap;
}
.btn-group-poli {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    white-space: nowrap;
}
.nama-poli-input, .nama-poli-text,
.nama-medis-input, .nama-medis-text {
    display: inline-block;
    max-width: calc(100% - 115px);
    width: 100%;
    vertical-align: middle;
    box-sizing: border-box;
    height: 26px;
    font-size: 14px;
}
.nama-poli-input,
.nama-medis-input {
    display: none; /* pastikan hidden default */
}

</style>
@endsection

@section('content')
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-3">Master Data Poli</h2>
    
    <div class="mb-3 flex items-center gap-3">
        <input type="text" id="searchPoli" placeholder="Cari berdasarkan Nama Poli..." 
               class="border p-2 rounded w-1/3">

        <button id="btnTambahPoli" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">
            + Tambah Poli
        </button>

        <button id="btnRefresh" class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600">
            &#x21bb; Refresh
        </button>
    </div>

    <!-- Modal Tambah Poli -->
    <div id="modalTambahPoli" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow w-96">
            <h2 class="text-lg font-semibold mb-4">Tambah Poli</h2>
            <form id="formTambahPoli">
                @csrf
                <input type="text" name="id_poli" placeholder="ID Poli" class="w-full mb-2 border p-2 rounded" required>
                <input type="text" name="poli" placeholder="Nama Poli" class="w-full mb-2 border p-2 rounded" required>
                <input type="text" name="nama_medis" placeholder="Nama Medis" class="w-full mb-2 border p-2 rounded">
                <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">Simpan</button>
                <button type="button" id="btnCloseModal" class="ml-2 bg-gray-400 text-white px-3 py-2 rounded">Batal</button>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-auto" style="max-height:600px;">
        <table id="tablePoli" class="table-poli">
            <thead>
                <tr>
                    <th>ID POLI</th>
                    <th>NAMA POLI</th>
                    <th>NAMA MEDIS</th>
                    <th class="aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @foreach($data as $p)
            <tr data-id="{{ $p->id_serial }}">
                <td>{{ $p->id_poli }}</td>

                <!-- Kolom Nama Poli -->
                <td class="nama-poli-cell">
                    <div class="flex items-center justify-between">
                        <span class="nama-poli-text">{{ $p->poli }}</span>
                        <input type="text" class="nama-poli-input hidden border p-1 rounded text-sm" 
                            data-id="{{ $p->id_serial }}" value="{{ $p->poli }}">
                        <div class="btn-group-poli flex gap-1">
                            <button type="button" class="btn-edit-poli text-yellow-600">✏️</button>
                            <button type="button" class="btn-save-poli text-green-600 hidden">💾Yes</button>
                            <button type="button" class="btn-cancel-poli text-gray-600 hidden">❌No</button>
                        </div>
                    </div>
                </td>

                <!-- Kolom Nama Medis -->
                <td class="nama-poli-cell">
                    <div class="flex items-center justify-between">
                        <span class="nama-medis-text">{{ $p->nama_medis }}</span>
                        <input type="text" class="nama-medis-input hidden border p-1 rounded text-sm flex-1 mr-2" 
                            data-id="{{ $p->id_serial }}" value="{{ $p->nama_medis }}">
                        <div class="btn-group-poli flex gap-1">
                            <button type="button" class="btn-edit-medis text-yellow-600">✏️</button>
                            <button type="button" class="btn-save-medis text-green-600 hidden">💾Yes</button>
                            <button type="button" class="btn-cancel-medis text-gray-600 hidden">❌No</button>
                        </div>
                    </div>
                </td>

                <!-- Kolom Aksi Hapus -->
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
    $('#searchPoli').on('keyup', function(){
        var filter = $(this).val().toLowerCase();
        $('#tablePoli tbody tr').each(function(){
            var nama = $(this).find('.nama-poli-text').text().toLowerCase();
            $(this).toggle(nama.includes(filter));
        });
    });

    // Modal Tambah
    $('#btnTambahPoli').click(()=>$('#modalTambahPoli').removeClass('hidden'));
    $('#btnCloseModal').click(()=>$('#modalTambahPoli').addClass('hidden'));

    // Submit Tambah
    $('#formTambahPoli').submit(function(e){
        e.preventDefault();
        $.post("{{ route('master.poli.store') }}", $(this).serialize(), function(res){
            location.reload(); // refresh tabel setelah simpan
        }).fail(function(){
            alert("Gagal simpan data");
        });
    });


    // Edit Nama Poli
    $(document).on('click','.btn-edit-poli', function(){
        var td = $(this).closest('td');
        td.find('.nama-poli-text').hide();
        td.find('.nama-poli-input').show().focus();
        td.find('.btn-edit-poli').hide();
        td.find('.btn-save-poli, .btn-cancel-poli').show();
    });

    // Cancel
    $(document).on('click','.btn-cancel-poli', function(){
        var td = $(this).closest('td');
        td.find('.nama-poli-input').val(td.find('.nama-poli-text').text());
        td.find('.nama-poli-input').hide();
        td.find('.nama-poli-text').show();
        td.find('.btn-save-poli, .btn-cancel-poli').hide();
        td.find('.btn-edit-poli').show();
    });

    // Save Nama Poli
    $(document).on('click', '.btn-save-poli', function() {
        var td = $(this).closest('td');
        var input = td.find('.nama-poli-input');
        var val = input.val().trim();
        var id = input.data('id');

        if (val.length === 0) {
            alert('Nama poli tidak boleh kosong');
            return;
        }

        $.post("{{ route('master.poli.updateField') }}", {
            id: id,
            field: 'poli',
            value: val
        }, function(res) {
            if (res.success) {
                // Update tampilan teks
                td.find('.nama-poli-text').text(val);

                // Sembunyikan input dan tombol save/cancel, tampilkan edit
                td.find('.nama-poli-input').hide();
                td.find('.nama-poli-text').show();
                td.find('.btn-save-poli, .btn-cancel-poli').hide();
                td.find('.btn-edit-poli').show();
            } else {
                alert('Update gagal!'); // untuk debug
            }
        }, 'json').fail(function(xhr, status, error) {
            console.log('AJAX Error:', status, error); // debug
            alert('Terjadi kesalahan saat menyimpan data.');
        });
    });


    // Edit Nama Medis
    $(document).on('click', '.btn-edit-medis', function() {
        var td = $(this).closest('td');
        td.find('.nama-medis-text').hide();
        td.find('.nama-medis-input').show().focus();
        td.find('.btn-edit-medis').hide();
        td.find('.btn-save-medis, .btn-cancel-medis').show();
    });

    // Cancel Nama Medis
    $(document).on('click', '.btn-cancel-medis', function() {
        var td = $(this).closest('td');
        td.find('.nama-medis-input').val(td.find('.nama-medis-text').text());
        td.find('.nama-medis-input').hide();
        td.find('.nama-medis-text').show();
        td.find('.btn-save-medis, .btn-cancel-medis').hide();
        td.find('.btn-edit-medis').show();
    });

    // Save Nama Medis
    $(document).on('click', '.btn-save-medis', function() {
        var td = $(this).closest('td');
        var input = td.find('.nama-medis-input');
        var val = input.val().trim();
        var id = input.data('id');

        if (val.length === 0) {
            alert('Nama medis tidak boleh kosong');
            return;
        }

        $.post("{{ route('master.poli.updateField') }}", {
            id: id,
            field: 'nama_medis',
            value: val
        }, function(res) {
            if (res.success) {
                td.find('.nama-medis-text').text(val);
                td.find('.nama-medis-input').hide();
                td.find('.nama-medis-text').show();
                td.find('.btn-save-medis, .btn-cancel-medis').hide();
                td.find('.btn-edit-medis').show();
            }
        }, 'json');
    });

    /// Tombol Hapus
    $(document).on('click','.btn-hapus',function(){
        var tr = $(this).closest('tr');
        var id = tr.data('id');
        if(!confirm("Yakin hapus data ini?")) return;

        $.ajax({
            url: "/master/poli/" + id,
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
