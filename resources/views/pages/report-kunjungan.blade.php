@extends('layouts.app')

@section('content')
<div class="bg-white rounded-2xl shadow p-5">
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-3">
        <div class="flex items-center gap-2">
            <!-- Dropdown Bulan -->
            <select id="filterBulan" class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-blue-300">
                @php
                    $bulanList = [
                        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
                        5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
                        9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                    ];
                @endphp
                @foreach ($bulanList as $key => $val)
                    <option value="{{ $key }}" {{ $key == date('n') ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
            </select>

            <!-- Dropdown Tahun -->
            <select id="filterTahun" class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-blue-300">
                @for ($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div class="flex items-center gap-2">
            <input type="text" id="searchBox" placeholder="🔍 Cari data..."
                class="border rounded-lg px-3 py-2 text-sm focus:ring focus:ring-blue-300 w-[500px]">

            <button id="btnExcel" class="bg-green-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-green-600">Export Excel</button>
            <button id="btnPDF" class="bg-red-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-red-600">Export PDF</button>
            <button id="btnPrintPrev" class="bg-indigo-500 text-white px-3 py-2 rounded-lg text-sm hover:bg-indigo-600">Print Preview</button>
        </div>
    </div>

    <table id="tabelKunjungan" class="min-w-full text-sm border border-gray-300 rounded-lg">
        <thead class="bg-blue-50 text-gray-700">
            <tr>
                <th class="border px-2 py-2">Tanggal</th>
                <th class="border px-2 py-2">Jam</th>
                <th class="border px-2 py-2">No. Kunjungan</th>
                <th class="border px-2 py-2">Nama Member</th>
                <th class="border px-2 py-2">No. RM</th>
                <th class="border px-2 py-2">Jenis</th>
                <th class="border px-2 py-2">Poli</th>
                <th class="border px-2 py-2">Dokter</th>
                <th class="border px-2 py-2">Payment</th>
                <th class="border px-2 py-2">Diagnosa</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- DataTables & JS -->
@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.tailwindcss.css">
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const table = $('#tabelKunjungan').DataTable({
        ajax: {
            url: "{{ route('report.kunjungan.data') }}",
            data: function (d) {
                d.bulan = $('#filterBulan').val();
                d.tahun = $('#filterTahun').val();
            },
            dataSrc: ""
        },
        columns: [
            { data: 'tgl_kunjungan' },
            { data: 'jam_kunjungan' },
            { data: 'no_kunjungan' },
            { data: 'nm_member' },
            { data: 'no_rm' },
            { data: 'jenis_kunjungan' },
            { data: 'poli' },
            { data: 'nama_medis' },
            { data: 'payment' },
            { data: 'diagnosa' },
        ],
        paging: false,
        searching: true,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
        // === EXCEL EXPORT ===
        {
        extend: 'excelHtml5',
        text: '💾 Simpan Excel',
        className: 'hidden',
        title:"",        
        customize: function (xlsx) {
            let bulanText = $('#filterBulan option:selected').text();
            let tahunText = $('#filterTahun').val();
            let lokasi = @json(session('lokasi'));

            let sheet = xlsx.xl.worksheets['sheet1.xml'];
            let downrows = 3;
                  
            // Geser isi tabel ke bawah
            $('row', sheet).each(function () {
                let r = parseInt($(this).attr('r'));
                $(this).attr("r", r + downrows);
            });
            $('row c', sheet).each(function () {
                let attr = $(this).attr('r');
                let pre = attr.substring(0, 1);
                let ind = parseInt(attr.substring(1));
                $(this).attr("r", pre + (ind + downrows));
            });

            // Tambahkan header teks (judul laporan)
            function addRow(index, text, mergeCols) {
                let mergeRef = `A${index}:${mergeCols}${index}`;
                let row = `<row r="${index}">
                    <c t="inlineStr" r="A${index}" s="60"><is><t>${text}</t></is></c>
                </row>`;
                $('mergeCells', sheet).append(`<mergeCell ref="${mergeRef}"/>`);
                return row;
            }

            if ($('mergeCells', sheet).length === 0) {
                $('worksheet', sheet).prepend('<mergeCells count="0"/>');
            }

            $('sheetData', sheet).prepend(
                addRow(1, 'Laporan Diagnosa Pasien', 'J') +
                addRow(2, 'Klinik : ' + lokasi, 'J') +
                addRow(3, 'Periode : ' + bulanText + ' ' + tahunText, 'J')
            );

            let mergeCount = $('mergeCells mergeCell', sheet).length;
            $('mergeCells', sheet).attr('count', mergeCount);

            // === Tambahkan style XML baru (gridline + warna) ===
            let styles = xlsx.xl['styles.xml'];
            let fills = $('fills', styles);
            let borders = $('borders', styles);
            let cellXfs = $('cellXfs', styles);

            // Tambah warna dan border
            fills.append('<fill><patternFill patternType="none"/></fill>'); // 0: polos
            fills.append('<fill><patternFill patternType="solid"><fgColor rgb="FF1F4E78"/></patternFill></fill>'); // 1: header biru
            fills.append('<fill><patternFill patternType="solid"><fgColor rgb="FFF3F6FA"/></patternFill></fill>'); // 2: baris ganjil
            fills.append('<fill><patternFill patternType="solid"><fgColor rgb="FFE9EEF5"/></patternFill></fill>'); // 3: baris genap

            borders.append('<border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/></border>'); // border biasa
            borders.append('<border><left/><right/><top/><bottom/></border>'); // border kosong (untuk judul)

            let startId = $('xf', cellXfs).length;

            // ====== Style Index ======
            // startId + 0 → Judul putih polos tanpa border
            cellXfs.append(`<xf applyFont="1" applyFill="0" borderId="${borders.children().length - 1}" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>`);
            // startId + 1 → Header biru, font putih tebal
            cellXfs.append(`<xf applyFont="1" applyFill="1" borderId="${borders.children().length - 2}" xfId="0" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>`);
            // startId + 2 → Baris ganjil
            cellXfs.append(`<xf applyFill="1" borderId="${borders.children().length - 2}" xfId="0"/>`);
            // startId + 3 → Baris genap
            cellXfs.append(`<xf applyFill="1" borderId="${borders.children().length - 2}" xfId="0"/>`);

            // Terapkan style sesuai baris
            $('row', sheet).each(function (i) {
                if (i < 3) {
                    $(this).find('c').attr('s', startId); // judul putih polos
                } else if (i === 3) {
                    $(this).find('c').attr('s', startId + 1); // header biru
                } else {
                    let style = (i % 2 === 0) ? startId + 2 : startId + 3;
                    $(this).find('c').attr('s', style);
                }
            });

            // === Format kolom A sebagai tanggal (dd/mm/yyyy)
            $('row c[r^="A"] v', sheet).each(function () {
                let val = $(this).text();
                // Excel date serial → biarkan, nanti Excel baca otomatis
                if (val && !isNaN(val)) {
                    $(this).text(val);
                }
            });

            // === Ganti koma jadi titik di kolom C ===
            $('row c[r^="C"] t', sheet).each(function () {
                $(this).text($(this).text().replace(',', '.'));
            });

            // === Ubah font header jadi putih ===
            let fonts = $('fonts', styles);
            fonts.append('<font><b val="true"/><color rgb="FFFFFFFF"/><name val="Calibri"/><sz val="11"/></font>');
        }},

        // === PDF EXPORT ===
        {
            extend: 'pdfHtml5',
            text: '📄 Simpan PDF',
            className: 'hidden',
            title: '',
            orientation: 'landscape',
            pageSize: 'A4',
            customize: function (doc) {
                let bulanText = $('#filterBulan option:selected').text();
                let tahunText = $('#filterTahun').val();
                let lokasi = @json(session('lokasi'));

                // Header di atas tabel
                doc.content.splice(0, 0, {
                    text: [
                        { text: 'LAPORAN DIAGNOSA PASIEN\n', style: 'title' },
                        { text: `Klinik : ${lokasi}\n`, style: 'subtitle' },
                        { text: `Periode : ${bulanText} ${tahunText}\n\n`, style: 'subtitle' }
                    ],
                    margin: [0, 0, 0, 12]
                });

                // Style header
                doc.styles.title = { fontSize: 14, bold: true, alignment: 'center' };
                doc.styles.subtitle = { fontSize: 10, alignment: 'center' };
                doc.styles.tableHeader = { fillColor: '#dbeafe', bold: true };

                // Buat tabel baris ganjil-genap lembut
                let objLayout = {};
                objLayout['hLineWidth'] = function(i) { return 0.5; };
                objLayout['vLineWidth'] = function(i) { return 0.5; };
                objLayout['hLineColor'] = function(i) { return '#aaa'; };
                objLayout['vLineColor'] = function(i) { return '#aaa'; };
                objLayout['fillColor'] = function (rowIndex, node, columnIndex) {
                    return (rowIndex % 2 === 0) ? null : '#f9fafb';
                };
                doc.content[1].layout = objLayout;
            }
        },

        // === PRINT ===
        {
            extend: 'print',
            text: '🖨️ Cetak',
            className: 'hidden',
            title: '',
            customize: function (win) {
                let bulanText = $('#filterBulan option:selected').text();
                let tahunText = $('#filterTahun').val();
                let lokasi = @json(session('lokasi'));

                $(win.document.body)
                    .prepend(`
                        <div style="text-align:center; margin-bottom:20px;">
                            <h2 style="margin:0;">LAPORAN DIAGNOSA PASIEN</h2>
                            <p style="margin:0;">Klinik : ${lokasi}</p>
                            <p style="margin:0;">Periode : ${bulanText} ${tahunText}</p>
                        </div>
                    `);
            }
        }
    ]
    });

    // Filter bulan/tahun
    $('#filterBulan, #filterTahun').on('change', function () {
        table.ajax.reload();
    });

    $('.dataTables_filter').hide();

    // Searching manual
    $('#searchBox').on('keyup', function () {
        table.search(this.value).draw();
    });
    // Tombol export
    $('#btnExcel').click(() => table.button(0).trigger());
    $('#btnPDF').click(() => table.button(1).trigger());
    $('#btnPrintPrev').click(() => table.button(2).trigger());
});
</script>

@endpush
@endsection
