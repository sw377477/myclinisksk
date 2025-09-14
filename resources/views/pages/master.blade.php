@extends('layouts.app')

@section('content')
<div class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-3">Master</h2>

    <!-- Tabs -->
    <div class="border-b border-gray-300 mb-4 overflow-x-auto">
        <nav class="flex space-x-2 whitespace-nowrap">
            <button class="tab-link px-2 py-1 text-sm text-gray-600 hover:text-gray-900 border-b-2 border-transparent focus:outline-none" data-tab="poli">Master Poli</button>
            <button class="tab-link px-2 py-1 text-sm text-gray-600 hover:text-gray-900 border-b-2 border-transparent focus:outline-none" data-tab="payment">Master Payment</button>
            <button class="tab-link px-2 py-1 text-sm text-gray-600 hover:text-gray-900 border-b-2 border-transparent focus:outline-none" data-tab="obat">Master Obat</button>
            <button class="tab-link px-2 py-1 text-sm text-gray-600 hover:text-gray-900 border-b-2 border-transparent focus:outline-none" data-tab="kunjungan">Master Kunjungan</button>
            <button class="tab-link px-2 py-1 text-sm text-gray-600 hover:text-gray-900 border-b-2 border-transparent focus:outline-none" data-tab="logo">Master Logo PT</button>
            <button class="tab-link px-2 py-1 text-sm text-gray-600 hover:text-gray-900 border-b-2 border-transparent focus:outline-none" data-tab="satuan">Master Satuan</button>
            <button class="tab-link px-2 py-1 text-sm text-gray-600 hover:text-gray-900 border-b-2 border-transparent focus:outline-none" data-tab="kategori">Master Kategori</button>
            <button class="tab-link px-2 py-1 text-sm text-gray-600 hover:text-gray-900 border-b-2 border-transparent focus:outline-none" data-tab="icd">Master ICD</button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Tab Poli -->
<div class="tab-pane fade show active" id="poli">
    <div class="mb-3 flex justify-between">
        <div></div>
        <!-- Tombol Aksi -->
        <div class="space-x-2">
            <button id="addRow" class="bg-blue-600 text-white px-3 py-1 rounded">➕ Add Row</button>
            <button id="saveRow" class="bg-green-600 text-white px-3 py-1 rounded">💾 Save</button>
            <button id="updateRow" class="bg-yellow-500 text-white px-3 py-1 rounded">✏️ Update</button>
            <button id="deleteRow" class="bg-red-600 text-white px-3 py-1 rounded">🗑️ Delete</button>
        </div>
    </div>

    <table id="tablePoli" class="table table-bordered w-full">
        <thead class="bg-gray-200">
            <tr>
                <th>ID</th>
                <th>Nama Poli</th>
                <th>Nama Medis</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


<!-- Tab Payment -->
<div class="tab-pane fade" id="payment">
    <div class="mb-3 flex justify-between">
        <div></div>
        <!-- Tombol Aksi -->
        <div class="space-x-2">
            <button id="addRowPayment" class="bg-blue-600 text-white px-3 py-1 rounded">➕ Add Row</button>
            <button id="saveRowPayment" class="bg-green-600 text-white px-3 py-1 rounded">💾 Save</button>
            <button id="updateRowPayment" class="bg-yellow-500 text-white px-3 py-1 rounded">✏️ Update</button>
            <button id="deleteRowPayment" class="bg-red-600 text-white px-3 py-1 rounded">🗑️ Delete</button>
        </div>
    </div>

    <table id="tablePayment" class="table table-bordered w-full">
        <thead class="bg-gray-200">
            <tr>
                <th>ID</th>
                <th>Nama Payment</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


        <!-- Tab Lainnya -->
        <div class="tab-pane fade" id="obat">
                <div class="mb-2">
                    <button class="btn btn-primary btn-sm" id="addRowObat">+ Add Row</button>
                    <button class="btn btn-success btn-sm" id="saveObat">💾 Save</button>
                    <button class="btn btn-warning btn-sm" id="updateObat">✏️ Update</button>
                    <button class="btn btn-danger btn-sm" id="deleteObat">🗑️ Delete</button>
                </div>
                <table id="tableObat" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID Obat</th>
                            <th>Kode Obat</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Stok Minimal</th>
                            <th>Golongan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            {{-- MASTER KUNJUNGAN --}}
            <div class="tab-pane fade" id="kunjungan">
                <div class="mb-2">
                    <button class="btn btn-primary btn-sm" id="addRowKunjungan">+ Add Row</button>
                    <button class="btn btn-success btn-sm" id="saveKunjungan">💾 Save</button>
                    <button class="btn btn-warning btn-sm" id="updateKunjungan">✏️ Update</button>
                    <button class="btn btn-danger btn-sm" id="deleteKunjungan">🗑️ Delete</button>
                </div>
                <table id="tableKunjungan" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID Kunjungan</th>
                            <th>Jenis Kunjungan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            {{-- MASTER LOGO --}}
            <div class="tab-pane fade" id="logo">
                <div class="mb-2">
                    <button class="btn btn-primary btn-sm" id="addRowLogo">+ Add Row</button>
                    <button class="btn btn-success btn-sm" id="saveLogo">💾 Save</button>
                    <button class="btn btn-warning btn-sm" id="updateLogo">✏️ Update</button>
                    <button class="btn btn-danger btn-sm" id="deleteLogo">🗑️ Delete</button>
                </div>
                <table id="tableLogo" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID Data</th>
                            <th>Logo</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            {{-- MASTER SATUAN --}}
            <div class="tab-pane fade" id="satuan">
                <div class="mb-2">
                    <button class="btn btn-primary btn-sm" id="addRowSatuan">+ Add Row</button>
                    <button class="btn btn-success btn-sm" id="saveSatuan">💾 Save</button>
                    <button class="btn btn-warning btn-sm" id="updateSatuan">✏️ Update</button>
                    <button class="btn btn-danger btn-sm" id="deleteSatuan">🗑️ Delete</button>
                </div>
                <table id="tableSatuan" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID Satuan</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            {{-- MASTER KATEGORI --}}
            <div class="tab-pane fade" id="kategori">
                <div class="mb-2">
                    <button class="btn btn-primary btn-sm" id="addRowKategori">+ Add Row</button>
                    <button class="btn btn-success btn-sm" id="saveKategori">💾 Save</button>
                    <button class="btn btn-warning btn-sm" id="updateKategori">✏️ Update</button>
                    <button class="btn btn-danger btn-sm" id="deleteKategori">🗑️ Delete</button>
                </div>
                <table id="tableKategori" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID Kategori</th>
                            <th>Kategori</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            {{-- MASTER ICD --}}
            <div class="tab-pane fade" id="icd">
                <div class="mb-2">
                    <button class="btn btn-primary btn-sm" id="addRowICD">+ Add Row</button>
                    <button class="btn btn-success btn-sm" id="saveICD">💾 Save</button>
                    <button class="btn btn-warning btn-sm" id="updateICD">✏️ Update</button>
                    <button class="btn btn-danger btn-sm" id="deleteICD">🗑️ Delete</button>
                </div>
                <table id="tableICD" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode ICD</th>
                            <th>Diagnosis</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Script Tab -->
<script>
    const tabs = document.querySelectorAll('.tab-link');
    const panes = document.querySelectorAll('.tab-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('border-blue-500', 'text-blue-600', 'font-medium'));
            panes.forEach(p => p.classList.add('hidden'));
            tab.classList.add('border-blue-500', 'text-blue-600', 'font-medium');
            const pane = document.getElementById(tab.dataset.tab);
            pane.classList.remove('hidden');
        });
    });

    // Default tab active
    if(tabs.length > 0) tabs[0].click();
</script>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    console.log("Init DataTables...");

    function initDataTable(selector, url, columns) {
        $(selector).DataTable({
            ajax: url,
            columns: columns,
            dom: 
                "<'flex flex-col md:flex-row justify-between items-center mb-2'" +
                    "<'flex items-center space-x-2'l>" +
                    "<'flex items-center'f>" +
                ">" +
                "tr" +
                "<'flex flex-col md:flex-row justify-between items-center mt-2'" +
                    "<'text-sm text-gray-600'i>" +
                    "<'flex space-x-2'p>" +
                ">",
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "←",
                    next: "→"
                }
            }
        });
    }

    // Master Poli
    initDataTable('#tablePoli', "{{ route('master.poliData') }}", [
        { data: 'id_poli' },
        { data: 'poli' },
        { data: 'nama_medis' }
    ]);

    // Master Payment
    initDataTable('#tablePayment', "{{ route('master.paymentData') }}", [
        { data: 'id_pay' },
        { data: 'payment' }
    ]);
});

// === ACTION BUTTONS MASTER POLI ===
$('#addRow').on('click', function () {
    var table = $('#tablePoli').DataTable();
    table.row.add({
        "id_poli": "",
        "poli": "",
        "nama_medis": ""
    }).draw(false);

    // opsional: pindah baris baru ke atas
    var last = table.rows().nodes().last();
    $(last).prependTo(table.table().body());
});

$('#deleteRow').on('click', function () {
    let table = $('#tablePoli').DataTable();
    table.row('.selected').remove().draw(false);
});

// === ACTION BUTTONS MASTER PAYMENT ===
$('#addRow').on('click', function () {
    var table = $('#tablePayment').DataTable();
    table.row.add({
        "id_ay": "",
        "payment": ""
    }).draw(false);

    // opsional: pindah baris baru ke atas
    var last = table.rows().nodes().last();
    $(last).prependTo(table.table().body());
});

$('#deleteRowPayment').on('click', function () {
    let table = $('#tablePayment').DataTable();
    table.row('.selected').remove().draw(false);
});

//baris bisa di klik
$('#tablePoli tbody').on('click', 'tr', function () {
    $(this).toggleClass('selected');
});

$('#tablePayment tbody').on('click', 'tr', function () {
    $(this).toggleClass('selected');
});


</script>

<style>
/* Samakan tinggi semua row */
table.dataTable tbody tr {
    height: 45px; /* atur sesuai selera, misal 40-45px */
}

/* Supaya sel dalam row tidak beda padding */
table.dataTable td, 
table.dataTable th {
    padding: 0.6rem 1rem !important;
    vertical-align: middle !important;
    font-size: 0.9rem;
}
</style>


@endsection
