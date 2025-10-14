@extends('layouts.app')

@section('styles')
<style>
/* ================= Table Styling ================= */
.table-payment {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
    table-layout: auto;
    line-height: 1.2;
}

/* Table Header */
.table-payment th {
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
.table-payment th.aksi-col {
    text-align: center; /* khusus kolom Aksi */
}

/* Table Body */
.table-payment td {
    padding: 4px 6px;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
    border-right: 1px solid #eee;
}

/* Zebra Rows */
.table-payment tbody tr:nth-child(odd) {
    background-color: #fafafa;
}
.table-payment tbody tr:nth-child(even) {
    background-color: #f4f4f4;
}

/* Hover row highlight */
.table-payment tbody tr:hover {
    background-color: #e0f7fa;
}

/* ================= Payment Cell & Button Group ================= */
.payment-cell > div {
    display: flex;
    align-items: center;
}

.payment-text {
    flex-grow: 1;
}

.payment-input {
    flex-grow: 1;
    display: none; /* hidden default */
}

.btn-group-payment {
    flex-shrink: 0;
    margin-left: 8px;
}

/* Optional hover colors */
.btn-edit-payment:hover { background-color: #fff3cd; }
.btn-save-payment:hover { background-color: #d4edda; }
.btn-cancel-payment:hover { background-color: #e2e3e5; }
</style>

@endsection

@section('content')
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-3">Master Data Payment</h2>
    
    <div class="mb-3 flex items-center gap-3">
        <input type="text" id="searchPayment" placeholder="Cari berdasarkan Jenis Payment..." 
               class="border p-2 rounded w-1/3">

        <button id="btnTambahPayment" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">
            + Tambah Payment
        </button>

        <button id="btnRefresh" class="bg-yellow-500 text-white px-3 py-2 rounded hover:bg-yellow-600">
            &#x21bb; Refresh
        </button>
    </div>

    <!-- Modal Tambah Payment -->
    <div id="modalTambahPayment" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow w-96">
            <h2 class="text-lg font-semibold mb-4">Tambah Payment</h2>
            <form id="formTambahPayment">
                @csrf
                <input type="text" name="id_pay" placeholder="ID Payment" class="w-full mb-2 border p-2 rounded" required>
                <input type="text" name="payment" placeholder="Payment" class="w-full mb-2 border p-2 rounded">
                <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded hover:bg-green-600">Simpan</button>
                <button type="button" id="btnCloseModal" class="ml-2 bg-gray-400 text-white px-3 py-2 rounded">Batal</button>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto overflow-y-auto" style="max-height:600px;">
        <table id="tablePayment" class="table-payment">
            <thead>
                <tr>
                    <th>ID PAY</th>
                    <th>PAYMENT</th>
                    <th class="aksi-col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $p)
                <tr data-id="{{ $p->id_pay }}">
                    <td>{{ $p->id_pay }}</td>

                    <!-- Payment Editable -->
                    <td class="payment-cell relative">
			            <div class="flex items-center justify-between">
                        	    <span class="payment-text">{{ $p->payment }}</span>
                        	    <input type="text" class="payment-input hidden border p-1 rounded" data-id="{{ $p->id_pay }}" value="{{ $p->payment }}">
                        		<div class="btn-group-payment flex gap-1">
                            		<button type="button" class="btn-edit-payment text-yellow-600 px-2 py-1 rounded hover:bg-yellow-100">✏️</button>
                            		<button type="button" class="btn-save-payment text-green-600 px-2 py-1 rounded hover:bg-green-100 hidden">💾Yes</button>
                            		<button type="button" class="btn-cancel-payment text-gray-600 px-2 py-1 rounded hover:bg-gray-100 hidden">❌No</button>
                        	    </div>
			            </div>
                    </td>


                    <!-- Hapus -->
                    <td class="text-center">
                        <button type="button" class="btn-hapus bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Hapus</button>
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
    $('#searchPayment').on('keyup', function(){
        var filter = $(this).val().toLowerCase();
        $('#tablePayment tbody tr').each(function(){
            var nama = $(this).find('.payment-text').text().toLowerCase();
            $(this).toggle(nama.includes(filter));
        });
    });

    // Modal Tambah
    $('#btnTambahPayment').click(()=>$('#modalTambahPayment').removeClass('hidden'));
    $('#btnCloseModal').click(()=>$('#modalTambahPayment').addClass('hidden'));

    // Submit Tambah
    $('#formTambahPayment').submit(function(e){
        e.preventDefault();
        $.post("{{ route('master.payment.store') }}", $(this).serialize(), function(res){
            location.reload(); // refresh tabel setelah simpan
        }).fail(function(){
            alert("Gagal simpan data");
        });
    });

    // Edit Payment
    $(document).on('click', '.btn-edit-payment', function() {
        var td = $(this).closest('td');
        td.find('.payment-text').hide();
        td.find('.payment-input').show().focus();
        td.find('.btn-edit-payment').hide();
        td.find('.btn-save-payment, .btn-cancel-payment').show();
    });

    // Cancel Payment
    $(document).on('click', '.btn-cancel-payment', function() {
        var td = $(this).closest('td');
        td.find('.payment-input').val(td.find('.payment-text').text());
        td.find('.payment-input').hide();
        td.find('.payment-text').show();
        td.find('.btn-save-payment, .btn-cancel-payment').hide();
        td.find('.btn-edit-payment').show();
    });

    // Save Payment
    $(document).on('click', '.btn-save-payment', function() {
        var td = $(this).closest('td');
        var input = td.find('.payment-input');
        var val = input.val().trim();
        var id = input.data('id');

        if (val.length === 0) {
            alert('Payment tidak boleh kosong');
            return;
        }

        $.post("{{ route('master.payment.updateField') }}", {
            id: id,
            field: 'payment',
            value: val
        }, function(res) {
            if (res.success) {
                td.find('.payment-text').text(val);
                td.find('.payment-input').hide();
                td.find('.payment-text').show();
                td.find('.btn-save-payment, .btn-cancel-payment').hide();
                td.find('.btn-edit-payment').show();
            } else {
                alert('Gagal memperbarui payment!');
            }
        }, 'json').fail(function(xhr, status, error) {
            console.log('AJAX Error:', status, error);
            alert('Terjadi kesalahan saat menyimpan payment.');
        });
    }); 


    /// Tombol Hapus
    $(document).on('click','.btn-hapus',function(){
        var tr = $(this).closest('tr');
        var id = tr.data('id');
        if(!confirm("Yakin hapus data ini?")) return;

        $.ajax({
            url: "/master/payment/" + id,
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
