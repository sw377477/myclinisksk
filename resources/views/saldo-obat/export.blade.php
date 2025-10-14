@php
    function formatNumber($val, $isQty = false) {
        if ($val === null || $val == 0) return '';
        return $isQty 
            ? number_format($val, 0, ',', '.') // N0
            : number_format($val, 2, ',', '.'); // N2
    }

    $bulanList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $bulanNama = $bulanList[(int) request('bulan')] ?? '';
    $tahun = request('tahun');
    $lokasi = session('lokasi');
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid #bbb;   /* tipis abu */
        padding: 5px;
        white-space: nowrap;      /* no wrap */
    }
    th {
        background: #dce6f1;      /* smooth biru abu header */
        text-align: center;
        font-weight: bold;
    }
    /* Baris ganjil genap */
    tbody tr:nth-child(odd) {
        background: #f9f9f9;
    }
    tbody tr:nth-child(even) {
        background: #ffffff;
    }
    /* Sticky header */
    thead th {
        position: sticky;
        top: 0;
        background: #dce6f1;  /* ulangi warna supaya tidak transparan */
        z-index: 2;
        }
    
    /* untuk printing orientation landscape */
    @page {
        size: A4 landscape;
        margin: 0.5cm;
        
    }
    
</style>
    
</head>
<body>

<!-- Judul Laporan -->
    <table style="border: none; margin-bottom: 10px; width:100%;">
        <tr>
            <td colspan="14" style="text-align:center; font-size:16px; font-weight:bold; border:none;">
                Laporan Monitoring Obat
            </td>
        </tr>
        <tr>
            <td colspan="14" style="text-align:center; font-size:14px; border:none;">
                Periode : {{ $bulanNama }} {{ $tahun }}
            </td>
        </tr>
        <tr>
            <td colspan="14" style="text-align:center; font-size:14px; border:none;">
                Klinik : {{ $lokasi }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Obat</th>
                <th>Satuan</th>
                <th>Qty Awal</th>
                <th>Nilai Awal</th>
                <th>HPP Awal</th>
                <th>Qty Masuk</th>
                <th>Nilai Masuk</th>
                <th>Qty Keluar</th>
                <th>Nilai Keluar</th>
                <th>Qty Akhir</th>
                <th>Nilai Akhir</th>
                <th>HPP Akhir</th>
                
            </tr>
            
        </thead>
        
        <tbody>
            @foreach($data as $i => $row)
                <tr style="background: {{ $i % 2 == 0 ? '#ffffff' : '#f9f9f9' }};">
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $row->kode }}</td>
                    <td>{{ $row->nama_obat }}</td>
                    <td>{{ $row->satuan }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->qty_awal, true) }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->nilai_awal) }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->hpp_awal) }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->qty_masuk, true) }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->nilai_masuk) }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->qty_keluar, true) }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->nilai_keluar) }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->qty_akhir, true) }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->nilai_akhir) }}</td>
                    <td style="text-align: right;">{{ formatNumber($row->hpp_akhir) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
