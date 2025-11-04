@extends('layouts.app')

@section('content')
@php
    // Pastikan variabel tidak undefined
    $dataObatMasuk = $dataObatMasuk ?? [];
@endphp

<div 
    x-data="{ tab: 'masuk', subtab: 'daftar', subtabKeluar: 'harian' }"
    class="p-6 bg-gray-50 min-h-screen"
>
    <!--<h1 class="text-3xl font-bold mb-6 text-gray-800 flex items-center gap-2">
        <span>📦</span> <span>Laporan Obat</span>
    </h1>-->

    {{-- ====== TAB UTAMA ====== --}}
    <div class="flex justify-center space-x-2 sm:space-x-4 border-b mb-6">
        <template x-for="(item, key) in [
            { id: 'masuk', label: 'Laporan Obat Masuk' },
            { id: 'keluar', label: 'Laporan Obat Keluar' },
            { id: 'monitoring', label: 'Laporan Monitoring Obat' }
        ]" :key="key">
            <button 
                class="px-4 py-2 text-sm sm:text-lg font-semibold rounded-t-lg transition-all duration-300"
                :class="tab === item.id 
                    ? 'bg-blue-600 text-white shadow-md' 
                    : 'bg-white text-gray-600 hover:bg-gray-100 border border-transparent'"
                @click="tab = item.id"
                x-text="item.label"
            ></button>
        </template>
    </div>

    {{-- ====== TAB OBAT MASUK ====== --}}
    <div 
        x-show="tab === 'masuk'" 
        x-transition 
        class="h-[580px] bg-white p-5 rounded-2xl shadow-md border border-gray-100"
    >
        <div class="flex justify-center space-x-4 border-b mb-4">
            <button 
                class="px-3 py-1.5 rounded-t-lg font-medium transition"
                :class="subtab === 'daftar' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                @click="subtab = 'daftar'"
            >
                Daftar Obat Masuk
            </button>
        </div>

        {{-- ====== SUBTAB DAFTAR OBAT MASUK ====== --}}
        <div x-show="subtab === 'daftar'" class="mt-4" x-transition>
            <div class="flex justify-between items-center mb-4 flex-wrap gap-4">
                {{-- KIRI: FILTER --}}
                <form method="GET" action="{{ route('laporan.obat') }}" class="flex space-x-2 items-center">
                    <select id="bulan" name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Pilih Bulan</option>
                        @foreach ([
                            '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                            '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                        ] as $key => $bulan)
                            <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>{{ $bulan }}</option>
                        @endforeach
                    </select>

                    <select id="tahun" name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Pilih Tahun</option>
                        @for ($i = 2025; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-semibold">
                        🔄 Tampilkan
                    </button>
                </form>

                {{-- KANAN: SEARCH + BUTTON --}}
                <div class="flex space-x-2 items-center">
                    <input 
                        type="text" 
                        id="search" 
                        placeholder="🔍 Cari obat..."
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-[400px] focus:ring-2 focus:ring-blue-400"
                    >
                    <button id="btnExcel" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg text-sm font-semibold">
                        📗 Excel
                    </button>
                    <button id="btnPdf" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-semibold">
                        📕 PDF
                    </button>
                    <button id="btnPrint" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm font-semibold">
                        🖨️ Print
                    </button>
                </div>
            </div>

            {{-- ====== TABEL DATA OBAT MASUK ====== --}}
            <div class="overflow-y-auto border rounded-lg shadow-sm max-h-[490px]">
                <table id="tblObatMasuk" class="min-w-full border-collapse text-sm text-left">
                    <thead class="bg-blue-100 text-gray-700 sticky top-0 z-10">
                        <tr>
                            <th class="border px-3 py-2 sticky top-0 bg-blue-100">No</th>
                            <th class="border px-3 py-2 sticky top-0 bg-blue-100">Tanggal</th>
                            <th class="border px-3 py-2 sticky top-0 bg-blue-100">Kode Obat</th>
                            <th class="border px-3 py-2 sticky top-0 bg-blue-100">Nama Obat</th>
                            <th class="border px-3 py-2 sticky top-0 bg-blue-100">Satuan</th>
                            <th class="border px-3 py-2 text-right sticky top-0 bg-blue-100">Jumlah</th>
                            <th class="border px-3 py-2 sticky top-0 bg-blue-100">Expired</th>
                            <th class="border px-3 py-2 sticky top-0 bg-blue-100">No. Batch</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataObatMasuk as $i => $row)
                            <tr class="{{ $i % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 transition">
                                <td class="border px-3 py-2">{{ $i + 1 }}</td>
                                <td class="border px-3 py-2">
                                    {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="border px-3 py-2">{{ $row->kode }}</td>
                                <td class="border px-3 py-2">{{ $row->nama_obat }}</td>
                                <td class="border px-3 py-2">{{ $row->satuan }}</td>
                                <td class="border px-3 py-2 text-right">
                                    {{ number_format($row->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="border px-3 py-2">{{ $row->expired }}</td>
                                <td class="border px-3 py-2">{{ $row->no_batch }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-gray-500 py-3">
                                    Tidak ada data ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ====== TAB OBAT KELUAR ====== --}}
    <div 
        x-show="tab === 'keluar'" 
        x-transition 
        class="bg-white p-5 rounded-2xl shadow-md border border-gray-100"
    >
        <div class="flex justify-center space-x-4 border-b mb-4">
            <button 
                class="px-3 py-1.5 rounded-t-lg font-medium transition"
                :class="subtabKeluar === 'harian' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                @click="subtabKeluar = 'harian'"
            >
                Laporan Harian Pengeluaran Obat
            </button>
            <button 
                class="px-3 py-1.5 rounded-t-lg font-medium transition"
                :class="subtabKeluar === 'rekap' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                @click="subtabKeluar = 'rekap'"
            >
                Rekap Pengeluaran Obat
            </button>
        </div>

        <div x-show="subtabKeluar === 'harian'" x-transition class="mt-4">
            <!--<h2 class="text-xl font-semibold mb-2">🕒 Laporan Harian Pengeluaran Obat</h2>-->

            <!-- Filter -->
            <div class="flex flex-wrap justify-between items-center mb-3 gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Dropdown Bulan -->
                    <select id="bulanHarian" name="bulanHarian" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Pilih Bulan</option>
                        @foreach ([
                            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
                        ] as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>

                    <!-- Dropdown Tahun -->
                    <select id="tahunHarian" name="tahunHarian" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Pilih Tahun</option>
                        @for ($i = 2025; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>

                    <!-- Tombol Tampilkan -->
                    <button id="btnTampilkanHarian" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        🔍 Tampilkan
                    </button>
                </div>

                <!-- Search + Export -->
                <div class="flex flex-wrap gap-2">
                    <input type="text" id="searchInput" placeholder="Cari..."
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 w-[400px]">
                    <button onclick="exportExcel()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                        📗 Excel
                    </button>
                    <button onclick="exportPDF()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                        📕 PDF
                    </button>
                    <button id="btnPrintLaporan" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Print View</button>
                </div>
            </div>

            <!-- Tabel -->
            <div class="h-[450px] overflow-x-auto border rounded-lg shadow-sm">
                <table id="tabelHarian" class=" min-w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="border px-2 py-1">Kode Obat</th>
                            <th class="border px-2 py-1">Nama Obat</th>
                            <th class="border px-2 py-1">Satuan</th>
                            <th class="border px-2 py-1">Saldo Awal</th>
                            <th class="border px-2 py-1">Penerimaan</th>
                            <th class="border px-2 py-1">Persediaan</th>
                            <th class="border px-2 py-1"> 1</th>
                            <th class="border px-2 py-1"> 2</th>
                            <th class="border px-2 py-1"> 3</th>
                            <th class="border px-2 py-1"> 4</th>
                            <th class="border px-2 py-1"> 5</th>
                            <th class="border px-2 py-1"> 6</th>
                            <th class="border px-2 py-1"> 7</th>
                            <th class="border px-2 py-1"> 8</th>
                            <th class="border px-2 py-1"> 9</th>
                            <th class="border px-2 py-1"> 10</th>
                            <th class="border px-2 py-1"> 11</th>
                            <th class="border px-2 py-1"> 12</th>
                            <th class="border px-2 py-1"> 13</th>
                            <th class="border px-2 py-1"> 14</th>
                            <th class="border px-2 py-1"> 15</th>
                            <th class="border px-2 py-1"> 16</th>
                            <th class="border px-2 py-1"> 17</th>
                            <th class="border px-2 py-1"> 18</th>
                            <th class="border px-2 py-1"> 19</th>
                            <th class="border px-2 py-1"> 20</th>
                            <th class="border px-2 py-1"> 21</th>
                            <th class="border px-2 py-1"> 22</th>
                            <th class="border px-2 py-1"> 23</th>
                            <th class="border px-2 py-1"> 24</th>
                            <th class="border px-2 py-1"> 25</th>
                            <th class="border px-2 py-1"> 26</th>
                            <th class="border px-2 py-1"> 27</th>
                            <th class="border px-2 py-1"> 28</th>
                            <th class="border px-2 py-1"> 29</th>
                            <th class="border px-2 py-1"> 30</th>
                            <th class="border px-2 py-1"> 31</th>
                            <th class="border px-2 py-1">Pemakaian</th>
                            <th class="border px-2 py-1">Sisa</th>
                        </tr>
                    </thead>
                    <tbody id="dataHarian">
                        <!-- Data akan diisi via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="subtabKeluar === 'rekap'" x-transition class="mt-4">
            <!--<h2 class="text-xl font-semibold mb-2">📊 Rekap Pengeluaran Obat</h2>-->

            <!-- Filter dan Action Bar -->
            <div class="flex justify-between items-center mb-3">
                <div class="flex space-x-2 text-sm">
                    <select id="bulanRekap" class="border rounded-lg p-2">
                        <option value="">Pilih Bulan</option>
                        @foreach ([
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ] as $num => $nama)
                            <option value="{{ $num }}">{{ $nama }}</option>
                        @endforeach
                    </select>

                    <select id="tahunRekap" class="border rounded-lg p-2">
                        <option value="">Pilih Tahun</option>
                        @for ($t = 2025; $t <= now()->year + 1; $t++)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endfor
                    </select>

                    <button id="btnTampilRekap" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        🔍 Tampilkan
                    </button>
                </div>

                <div class="flex space-x-2">
                    <input type="text" id="searchRekap" placeholder="Cari..." class="border rounded-lg p-2 w-[400px]">

                    <button id="btnExcelRekap" class="bg-green-500 text-white px-3 py-2 rounded-lg hover:bg-green-600">
                        📗 Excel
                    </button>

                    <button id="btnPdfRekap" class="bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600">
                        📕 PDF
                    </button>

                    <button id="btnPrintRekap" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600">
                        🖨️ Print
                    </button>
                </div>
            </div>

            <!-- Tabel Rekap -->
            <div id="rekapContainer" class="mt-4 p-4 border rounded-lg bg-gray-50 overflow-x-auto">
                <p class="text-gray-500 italic">Pilih bulan dan tahun, lalu klik "Tampilkan".</p>
            </div>
        </div>
    </div>

    {{-- ====== TAB MONITORING ====== --}}
    <div 
        x-show="tab === 'monitoring'" 
        x-transition 
        class="bg-white p-5 rounded-2xl shadow-md border border-gray-100">

        <h2 class="text-xl font-semibold mb-4">🩺 Laporan Monitoring Obat</h2>

        <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-2">
                <select id="bulanMonitoring" class="border rounded-lg p-1">
                    <option value="">Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>

                <select id="tahunMonitoring" class="border rounded-lg p-1">
                    <option value="">Tahun</option>
                    @for ($i = date('Y'); $i >= 2022; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>

                <button id="btnTampilMonitoring" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg">
                    🔍 Tampilkan
                </button>
            </div>

            <div class="flex space-x-2">
                <input id="searchMonitoring" type="text" placeholder="Cari obat..." 
                    class="border rounded-lg p-1 w-40">
                <button id="btnExcelMonitoring" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg">📗 Excel</button>
                <button id="btnPdfMonitoring" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg">📕 PDF</button>
                <button id="btnPrintMonitoring" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-lg">🖨️ Print</button>
            </div>
        </div>

        <div id="monitoringContainer" class="overflow-auto border rounded-lg p-2 text-sm">
            <p class="text-gray-500 italic">Pilih bulan dan tahun, lalu klik <b>Tampilkan</b>.</p>
        </div>
    </div>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
document.getElementById('search').addEventListener('keyup', function() {
    const keyword = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const namaObat = row.cells[3]?.textContent.toLowerCase() || '';
        row.style.display = namaObat.includes(keyword) ? '' : 'none';
    });
});
</script>

<script>
    // === Export Excel HTML ===
    document.querySelector('.bg-green-500').addEventListener('click', function () {
        const table = document.getElementById('tblObatMasuk').cloneNode(true);
        const today = new Date().toISOString().slice(0, 10);
        const filename = 'Laporan_Obat_Masuk_' + today + '.xls';
        const lokasi = "{{ session('lokasi') }}"; // ✅ ambil dari Laravel session

        // Judul yang lebih rapi
        const title = `
            <div style="text-align:center; font-family:Arial, sans-serif;">
                <h3 style="margin:0; font-weight:bold;">LAPORAN OBAT MASUK</h3>
                <p style="margin:2px 0; font-size:14px;">Klinik : ${lokasi}</p>
                <p style="margin:2px 0 10px 0; font-size:13px;">
                    Periode: <strong>{{ request('bulan') }}/{{ request('tahun') }}</strong>
                </p>
            </div>
        `;

        // HTML export lengkap
        const html = `
            <html xmlns:x="urn:schemas-microsoft-com:office:excel">
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; }
                    table, th, td { border:1px solid #888; border-collapse:collapse; }
                    th { background:#dbeafe; font-weight:bold; text-align:center; }
                    td { padding:4px; white-space:nowrap; }
                    tr:nth-child(odd) { background:#ffffff; }
                    tr:nth-child(even) { background:#f9fafb; }
                    table { width:100%; table-layout:auto; } /* ✅ best-fit */
                </style>
            </head>
            <body>${title}${table.outerHTML}</body>
            </html>`;

        // Buat blob dan jalankan download
        const blob = new Blob(['\ufeff', html], { type: 'application/vnd.ms-excel' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // === Search (filter tabel realtime) ===
    document.getElementById('search').addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#tblObatMasuk tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });
    });
</script>

<script>
    // === Export PDF ===
    document.getElementById('btnPdf').addEventListener('click', function () {
        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'pt', 'a4');

            const lokasi = "{{ session('lokasi') }}";
            const bulan = "{{ request('bulan') }}";
            const tahun = "{{ request('tahun') }}";
            const today = new Date().toLocaleDateString('id-ID');

            doc.setFont('helvetica', 'bold');
            doc.setFontSize(14);
            doc.text('LAPORAN OBAT MASUK', doc.internal.pageSize.getWidth() / 2, 40, { align: 'center' });

            doc.setFontSize(11);
            doc.setFont('helvetica', 'normal');
            doc.text(`Klinik: ${lokasi}`, doc.internal.pageSize.getWidth() / 2, 60, { align: 'center' });
            doc.text(`Periode: ${bulan}/${tahun}`, doc.internal.pageSize.getWidth() / 2, 75, { align: 'center' });
            //doc.text(`Dicetak: ${today}`, doc.internal.pageSize.getWidth() / 2, 90, { align: 'center' });

            doc.autoTable({
                html: '#tblObatMasuk',
                startY: 110,
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 3,
                },
                headStyles: {
                    fillColor: [59, 130, 246],
                    textColor: 255,
                    halign: 'center',
                },
                alternateRowStyles: { fillColor: [245, 247, 250] },
                margin: { left: 20, right: 20 },
            });

            const filename = `Laporan_Obat_Masuk_${today.replaceAll('/', '-')}.pdf`;
            doc.save(filename);
        } catch (err) {
            alert("Gagal export PDF. Pastikan koneksi internet aktif & jsPDF terload.");
            console.error(err);
        }
    });
</script>

<script>
document.getElementById('btnPrint').addEventListener('click', function() {
    const printWindow = window.open('', '', 'width=1000,height=800');
    printWindow.document.write(`
        <html>
            <head>
                <title>Print Laporan Obat Masuk</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
                    th { background-color: #f0f0f0; }
                    h2 { text-align: center; }
                </style>
            </head>
            <body>
                <h2>Laporan Obat Masuk</h2>
                ${document.querySelector('table').outerHTML}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
});
</script>

<script>
document.getElementById('btnTampilkanHarian').addEventListener('click', async () => {
    const bulan = document.getElementById('bulanHarian').value;
    const tahun = document.getElementById('tahunHarian').value;

    if (!bulan || !tahun) {
        alert('Pilih bulan dan tahun terlebih dahulu.');
        return;
    }

    try {
        const response = await fetch(`/laporan/obat-harian?bulan=${bulan}&tahun=${tahun}`);
        if (!response.ok) throw new Error('Data tidak ditemukan');

        const data = await response.json();
        const tbody = document.getElementById('dataHarian');
        tbody.innerHTML = '';

        data.forEach((row, i) => {
            const tr = document.createElement('tr');
            tr.className = i % 2 === 0 ? 'bg-white' : 'bg-gray-50'; // warna ganjil-genap

            tr.innerHTML = `
                <td class="border px-2 py-1">${row.kode_obat ?? ''}</td>
                <td class="border px-2 py-1">${row.nama_obat ?? ''}</td>
                <td class="border px-2 py-1">${row.satuan ?? ''}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.saldo_awal)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.penerimaan)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.persediaan)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_1)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_2)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_3)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_4)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_5)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_6)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_7)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_8)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_9)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_10)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_11)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_12)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_13)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_14)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_15)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_16)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_17)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_18)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_19)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_20)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_21)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_22)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_23)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_24)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_25)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_26)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_27)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_28)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_29)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_30)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.tgl_31)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.total_pemakaian)}</td>
                <td class="border px-2 py-1 text-right">${formatAngka(row.sisa)}</td>
            `;
            tbody.appendChild(tr);
        });
    } catch (err) {
        console.error(err);
        alert('Gagal memuat data laporan.');
    }

    
});
// fungsi format angka
function formatAngka(val) {
    if (val === null || val === undefined || val === '' || Number(val) === 0) return '';
    const num = parseFloat(val);
    return isNaN(num) ? '' : num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

// 🔍 Live search
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function(){
    const term = this.value.toLowerCase();
    document.querySelectorAll('#dataHarian tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});

// 📗 Excel Export
function exportExcel(){
    const bulan = document.getElementById('bulanHarian').value;
    const tahun = document.getElementById('tahunHarian').value;
    if(!bulan || !tahun){ alert('Pilih bulan dan tahun terlebih dahulu.'); return; }

    const table = document.getElementById('tabelHarian').outerHTML;
    const html = `
        <html><head><meta charset="UTF-8"></head><body>
        <h3 style="text-align:center">Laporan Harian Pengeluaran Obat<br>Bulan ${bulan}/${tahun}</h3>
        ${table.replace('<table', '<table border="1"')}
        </body></html>`;
    const blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `Laporan_Obat_Harian_${bulan}_${tahun}.xls`;
    link.click();
}

async function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'pt', 'a4'); // landscape

    const bulan = document.getElementById('bulanHarian').value;
    const tahun = document.getElementById('tahunHarian').value;

    if (!bulan || !tahun) {
        alert('Pilih bulan dan tahun terlebih dahulu.');
        return;
    }

    const table = document.getElementById('tabelHarian');
    if (!table) {
        alert('Tabel belum dimuat.');
        return;
    }

    // Ambil data header dan isi tabel
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => headers.push(th.innerText.trim()));

    const body = [];
    table.querySelectorAll('tbody tr').forEach(tr => {
        const rowData = [];
        tr.querySelectorAll('td').forEach(td => {
            rowData.push(td.innerText.trim());
        });
        body.push(rowData);
    });

    // Judul laporan
    doc.setFontSize(14);
    doc.text(`Laporan Harian Pengeluaran Obat`, doc.internal.pageSize.getWidth() / 2, 40, { align: 'center' });
    doc.setFontSize(11);
    doc.text(`Bulan ${bulan} / ${tahun}`, doc.internal.pageSize.getWidth() / 2, 60, { align: 'center' });

    // Buat tabel dari HTML
    doc.autoTable({
        head: [headers],
        body: body,
        startY: 80,
        theme: 'grid',
        styles: {
            fontSize: 7,
            cellPadding: 2,
            halign: 'right',
        },
        headStyles: {
            fillColor: [230, 230, 230],
            textColor: 20,
            halign: 'center',
        },
        didParseCell: (data) => {
            // Biar kolom pertama rata kiri
            if (data.column.index === 0 || data.column.index === 1) {
                data.cell.styles.halign = 'left';
            }
            // Nilai 0 tampil kosong
            if (data.cell.text[0] === '0' || data.cell.text[0] === '0.00') {
                data.cell.text = [''];
            }
        }
    });

    // Simpan file
    doc.save(`Laporan_Obat_Harian_${bulan}_${tahun}.pdf`);
}

// Tambahkan event ke tombol
document.getElementById('exportExcel').addEventListener('click', exportExcel);
document.getElementById('exportPDF').addEventListener('click', exportPDF);
//document.getElementById('printPreview').addEventListener('click', printPreview);
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const printBtn = document.getElementById("btnPrintLaporan");
    if (!printBtn) {
        console.error("Tombol print tidak ditemukan!");
        return;
    }

    printBtn.addEventListener("click", function() {
        const table = document.getElementById('tabelHarian');
        if (!table) {
            alert("Tidak ada tabel untuk dicetak!");
            return;
        }

        //const bulan = document.getElementById("bulan")?.value || "";
        //const tahun = document.getElementById("tahun")?.value || "";
        const bulan = document.getElementById('bulanHarian').value;
        const tahun = document.getElementById('tahunHarian').value;

        const namaBulan = {
            '1': 'Januari', '2': 'Februari', '3': 'Maret', '4': 'April',
            '5': 'Mei', '6': 'Juni', '7': 'Juli', '8': 'Agustus',
            '9': 'September', '10': 'Oktober', '11': 'November', '12': 'Desember'
        }[bulan] || '-';

        const printWindow = window.open("", "", "width=1000,height=800");
        printWindow.document.write(`
            <html>
                <head>
                    <title>Laporan Obat Harian</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        h2 { text-align: center; margin-bottom: 5px; }
                        h4 { text-align: center; color: #555; margin-top: 0; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
                        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
                        th { background-color: #f0f0f0; }
                        tr:nth-child(odd) { background-color: #fafafa; }
                        tr:nth-child(even) { background-color: #fff; }
                    </style>
                </head>
                <body>
                    <h2>Laporan Obat Harian</h2>
                    <h4>Bulan: ${namaBulan} ${tahun}</h4>
                    ${table.outerHTML}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnTampil = document.getElementById('btnTampilRekap');
    const container = document.getElementById('rekapContainer');
    const searchInput = document.getElementById('searchRekap');

    // =============================
    // BUTTON TAMPILKAN
    // =============================
    btnTampil.addEventListener('click', async () => {
        const bulan = document.getElementById('bulanRekap').value;
        const tahun = document.getElementById('tahunRekap').value;

        if (!bulan || !tahun) {
            alert('Pilih bulan dan tahun terlebih dahulu.');
            return;
        }

        container.innerHTML = "<p class='text-gray-500 italic'>⏳ Memuat data...</p>";

        try {
            const res = await fetch(`/laporan/obat-rekap?bulan=${bulan}&tahun=${tahun}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();

            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = "<p class='text-gray-500 italic'>Tidak ada data ditemukan.</p>";
                return;
            }

            // Buat tabel
            let table = `
                <table id="rekapTable" class="min-w-full border border-gray-400 text-sm">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border px-2 py-1">Tanggal</th>
                            <th class="border px-2 py-1">Pasien</th>
                            <th class="border px-2 py-1">Umur</th>
                            <th class="border px-2 py-1">Gender</th>
                            <th class="border px-2 py-1">Departemen</th>
                            <th class="border px-2 py-1">Diagnosa</th>
                            <th class="border px-2 py-1">Kode</th>
                            <th class="border px-2 py-1">Nama Obat</th>
                            <th class="border px-2 py-1">Satuan</th>
                            <th class="border px-2 py-1 text-right">Qty</th>
                            <th class="border px-2 py-1 text-right">Harga</th>
                            <th class="border px-2 py-1 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach((row, i) => {
                table += `
                    <tr class="${i % 2 === 0 ? 'bg-white' : 'bg-gray-100'}">
                        <td class="border px-2 py-1">${row.tanggal ?? ''}</td>
                        <td class="border px-2 py-1">${row.nama_pasien ?? ''}</td>
                        <td class="border px-2 py-1">${row.umur ?? ''}</td>
                        <td class="border px-2 py-1">${row.gender ?? ''}</td>
                        <td class="border px-2 py-1">${row.departemen ?? ''}</td>
                        <td class="border px-2 py-1">${row.diagnosa ?? ''}</td>
                        <td class="border px-2 py-1">${row.kode ?? ''}</td>
                        <td class="border px-2 py-1">${row.nama_obat ?? ''}</td>
                        <td class="border px-2 py-1">${row.satuan ?? ''}</td>
                        <td class="border px-2 py-1 text-right">
                            ${row.qty ? Number(row.qty).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}) : ''}
                        </td>
                        <td class="border px-2 py-1 text-right">
                            ${row.harga ? Number(row.harga).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}) : ''}
                        </td>
                        <td class="border px-2 py-1 text-right">
                            ${row.jumlah ? Number(row.jumlah).toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0}) : ''}
                        </td>
                    </tr>
                `;
            });

            table += `</tbody></table>`;
            container.innerHTML = table;

            // Filter pencarian
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                document.querySelectorAll("#rekapTable tbody tr").forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
                });
            });

        } catch (err) {
            console.error('Gagal memuat data:', err);
            container.innerHTML = `<p class="text-red-500 italic">Terjadi kesalahan: ${err.message}</p>`;
        }
    }); // ✅ ini menutup event listener tampilkan

    // =============================
    // EXPORT EXCEL
    // =============================
    document.getElementById('btnExcelRekap').addEventListener('click', () => {
        const table = document.getElementById('rekapTable');
        if (!table) return alert('Tampilkan data terlebih dahulu.');

        const bulan = document.getElementById('bulanRekap').value;
        const tahun = document.getElementById('tahunRekap').value;
        const periode = `${bulan}-${tahun}`;
        const lokasi = "{{ session('lokasi') ?? 'Tidak diketahui' }}";

        const html = `
            <html xmlns:x="urn:schemas-microsoft-com:office:excel">
                <head>
                    <meta charset="UTF-8">
                    <style>
                        table, th, td { border:1px solid black; border-collapse:collapse; }
                        th, td { padding:5px; }
                        th { background:#f0f0f0; }
                        h2, h4 { text-align:center; margin:0; }
                    </style>
                </head>
                <body>
                    <h2>Rekap Pengeluaran Obat</h2>
                    <h4>Klinik ${lokasi}</h4>
                    <h4>Periode: ${periode}</h4>
                    <br>
                    ${table.outerHTML}
                </body>
            </html>
        `;

        const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `Rekap_Pengeluaran_Obat_${periode}.xls`;
        link.click();
        URL.revokeObjectURL(url);
    });

    // =============================
    // EXPORT PDF
    // =============================
    document.getElementById('btnPdfRekap').addEventListener('click', () => {
        const table = document.getElementById('rekapTable');
        if (!table) return alert('Tampilkan data terlebih dahulu.');

        const bulan = document.getElementById('bulanRekap').value;
        const tahun = document.getElementById('tahunRekap').value;
        const periode = `${bulan}-${tahun}`;
        const lokasi = "{{ session('lokasi') ?? 'Tidak diketahui' }}";

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape' });

        doc.setFontSize(14);
        doc.text('Rekap Pengeluaran Obat', 14, 10);
        doc.setFontSize(11);
        doc.text(`Klinik ${lokasi}`, 14, 17);
        doc.text(`Periode: ${periode}`, 14, 24);
        doc.autoTable({ html: table, startY: 30 });
        doc.save(`Rekap_Pengeluaran_Obat_${periode}.pdf`);
    });

    // =============================
    // PRINT PREVIEW
    // =============================
    document.getElementById('btnPrintRekap').addEventListener('click', () => {
        const table = document.getElementById('rekapTable');
        if (!table) return alert('Tampilkan data terlebih dahulu.');

        const bulan = document.getElementById('bulanRekap').value;
        const tahun = document.getElementById('tahunRekap').value;
        const periode = `${bulan}-${tahun}`;
        const lokasi = "{{ session('lokasi') ?? 'Tidak diketahui' }}";

        const printWindow = window.open('', '', 'width=1000,height=800');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Rekap Pengeluaran Obat</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                        th { background-color: #f0f0f0; }
                        h2, h4 { text-align: center; margin: 0; }
                    </style>
                </head>
                <body>
                    <h2>Rekap Pengeluaran Obat</h2>
                    <h4>Klinik ${lokasi}</h4>
                    <h4>Periode: ${periode}</h4>
                    <br>
                    ${table.outerHTML}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnTampil = document.getElementById('btnTampilMonitoring');
    const container = document.getElementById('monitoringContainer');
    const searchInput = document.getElementById('searchMonitoring');

    btnTampil.addEventListener('click', async () => {
        const bulan = document.getElementById('bulanMonitoring').value;
        const tahun = document.getElementById('tahunMonitoring').value;

        if (!bulan || !tahun) {
            alert('Pilih bulan dan tahun terlebih dahulu.');
            return;
        }

        container.innerHTML = "<p class='text-gray-500 italic'>⏳ Memuat data...</p>";

        try {
            const res = await fetch(`/laporan/monitoring?bulan=${bulan}&tahun=${tahun}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();

            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = "<p class='text-gray-500 italic'>Tidak ada data ditemukan.</p>";
                return;
            }

            // Buat tabel
            let table = `
                <table id="monitoringTable" class="min-w-full border border-gray-400 text-sm">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border px-2 py-1">Kode</th>
                            <th class="border px-2 py-1">Nama Obat</th>
                            <th class="border px-2 py-1">Satuan</th>
                            <th class="border px-2 py-1 text-right">Qty Awal</th>
                            <th class="border px-2 py-1 text-right">Qty Masuk</th>
                            <th class="border px-2 py-1 text-right">Qty Keluar</th>
                            <th class="border px-2 py-1 text-right">Qty Akhir</th>
                            <th class="border px-2 py-1 text-right">HPP Akhir</th>
                            <th class="border px-2 py-1 text-right">Nilai Akhir</th>
                            <th class="border px-2 py-1">Expired</th>
                            <th class="border px-2 py-1">Batch</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach((row, i) => {
                table += `
                    <tr class="${i % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                        <td class="border px-2 py-1">${row.kode ?? ''}</td>
                        <td class="border px-2 py-1">${row.nama_obat ?? ''}</td>
                        <td class="border px-2 py-1">${row.satuan ?? ''}</td>
                        <td class="border px-2 py-1 text-right">${Number(row.qty_awal ?? 0).toLocaleString()}</td>
                        <td class="border px-2 py-1 text-right">${Number(row.qty_masuk ?? 0).toLocaleString()}</td>
                        <td class="border px-2 py-1 text-right">${Number(row.qty_keluar ?? 0).toLocaleString()}</td>
                        <td class="border px-2 py-1 text-right">${Number(row.qty_akhir ?? 0).toLocaleString()}</td>
                        <td class="border px-2 py-1 text-right">${Number(row.hpp_akhir ?? 0).toLocaleString()}</td>
                        <td class="border px-2 py-1 text-right">${Number(row.nilai_akhir ?? 0).toLocaleString()}</td>
                        <td class="border px-2 py-1">${row.expired ?? ''}</td>
                        <td class="border px-2 py-1">${row.no_batch ?? ''}</td>
                    </tr>
                `;
            });

            table += `</tbody></table>`;
            container.innerHTML = table;

            // Searching
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                document.querySelectorAll("#monitoringTable tbody tr").forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
                });
            });

        } catch (err) {
            console.error(err);
            container.innerHTML = "<p class='text-red-500'>Gagal memuat data.</p>";
        }
    });

    // === EXPORT EXCEL === btnExcelMonitoring
    btnExcelMonitoring.addEventListener("click", () => {
  const wb = XLSX.utils.book_new();

  // Header seperti PDF
  const header = [
    ["LAPORAN SALDO OBAT PER BULAN"],
    ["Periode: Oktober 2025 | Lokasi: CLKSK"],
    [],
    ["Kode", "Nama Obat", "Satuan", "Qty Awal", "Qty Masuk", "Qty Keluar", "Qty Akhir", "HPP Akhir", "Nilai Akhir", "Expired", "Batch"]
  ];

  const body = data.map((r) => [
    r.kode ?? "",
    r.nama_obat ?? "",
    r.satuan ?? "",
    Number(r.qty_awal ?? 0),
    Number(r.qty_masuk ?? 0),
    Number(r.qty_keluar ?? 0),
    Number(r.qty_akhir ?? 0),
    Number(r.hpp_akhir ?? 0),
    Number(r.nilai_akhir ?? 0),
    r.expired ?? "",
    r.no_batch ?? ""
  ]);

  const ws = XLSX.utils.aoa_to_sheet([...header, ...body]);

  // Buat warna ganjil-genap halus
  const range = XLSX.utils.decode_range(ws["!ref"]);
  for (let R = 4; R <= range.e.r; R++) {
    const fillColor = R % 2 === 0 ? "FFFFFFFF" : "FFF7F7F7";
    for (let C = 0; C <= range.e.c; C++) {
      const cellAddr = XLSX.utils.encode_cell({ r: R, c: C });
      if (!ws[cellAddr]) continue;
      ws[cellAddr].s = {
        fill: { fgColor: { rgb: fillColor } },
        border: {
          top: { style: "thin", color: { auto: 1 } },
          bottom: { style: "thin", color: { auto: 1 } },
          left: { style: "thin", color: { auto: 1 } },
          right: { style: "thin", color: { auto: 1 } },
        },
        alignment: { horizontal: "center", vertical: "center" },
        numFmt: C >= 3 && C <= 8 ? "#,##0" : undefined, // Format angka ribuan
      };
    }
  }

  XLSX.utils.book_append_sheet(wb, ws, "Laporan");
  XLSX.writeFile(wb, "Laporan_Saldo_Obat.xlsx");
});

    // === EXPORT PDF ===
    document.getElementById('btnPdfMonitoring').addEventListener('click', () => {
        const table = document.getElementById('monitoringTable');
        if (!table) return alert('Tampilkan data terlebih dahulu.');

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4'); // landscape A4

        // ambil header
        const headers = [];
        table.querySelectorAll('thead th').forEach(th => headers.push(th.innerText));

        // ambil isi tabel
        const rows = [];
        table.querySelectorAll('tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => row.push(td.innerText));
            rows.push(row);
        });

        // judul laporan
        doc.setFontSize(14);
        doc.text('Laporan Monitoring Obat', 40, 40);

        // tabel
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 60,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [200, 200, 200] },
            alternateRowStyles: { fillColor: [245, 245, 245] } // warna ganjil–genap halus
        });

        doc.save('Laporan Monitoring Obat.pdf');
    });

    // === PRINT ===
    document.getElementById('btnPrintMonitoring').addEventListener('click', () => {
        const table = document.getElementById('monitoringTable');
        if (!table) return alert('Tampilkan data terlebih dahulu.');
        const printWindow = window.open('', '', 'width=1000,height=800');
        printWindow.document.write(`<html><body>${table.outerHTML}</body></html>`);
        printWindow.document.close();
        printWindow.print();
    });
});
</script>

@endsection
