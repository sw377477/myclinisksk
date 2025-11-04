<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportObatController extends Controller
{
    public function laporan(Request $request)
    {
        // Ambil bulan & tahun dari request, jika kosong gunakan bulan sekarang
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $periode = $tahun . $bulan; // contoh: 202510

        // Query sesuai yang kamu berikan
        $dataObatMasuk = DB::select("
            SELECT a.tanggal,a.kode,a.satuan,b.nama_obat,a.jumlah,a.expired,a.no_batch 
            FROM rme_obat_masuk a
            JOIN rme_master_obat b ON b.kode_obat=a.kode
            WHERE a.periode = ? AND a.lokasi = ?
            ORDER BY a.id ASC
        ", [$periode, session('idpay')]);

        // Kirim ke Blade
        return view('pages.report-obat', compact('dataObatMasuk'));
    }


    public function harian(Request $r)
{
    $bulan  = $r->bulan;
    $tahun  = $r->tahun;
    $lokasi = session('idpay'); // ambil lokasi dari session

    $data = DB::select("
        SELECT * 
        FROM fn_laporan_obat_harian(?, ?, ?)
        ORDER BY nama_obat
    ", [$lokasi, $tahun, $bulan]);

    return response()->json($data);
}

// ✅ Export Excel (pakai HTML table)
    public function exportExcel(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        if (!$tahun || !$bulan) {
            return back()->with('error', 'Pilih bulan dan tahun terlebih dahulu.');
        }

        $data = $this->getDataObatHarian($tahun, $bulan);

        $html = view('laporan.obat_harian_excel', compact('data', 'bulan', 'tahun'))->render();
        $filename = "Laporan_Obat_Harian_{$bulan}_{$tahun}.xls";

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    // ✅ Export PDF
    public function exportPDF(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        if (!$tahun || !$bulan) {
            return back()->with('error', 'Pilih bulan dan tahun terlebih dahulu.');
        }

        $data = $this->getDataObatHarian($tahun, $bulan);

        $pdf = Pdf::loadView('laporan.obat_harian_pdf', compact('data', 'bulan', 'tahun'))
                  ->setPaper('A4', 'landscape');

        return $pdf->download("Laporan_Obat_Harian_{$bulan}_{$tahun}.pdf");
    }

    // ✅ Print Preview (langsung tampil di browser)
    public function printPreview(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        if (!$tahun || !$bulan) {
            return back()->with('error', 'Pilih bulan dan tahun terlebih dahulu.');
        }

        $data = $this->getDataObatHarian($tahun, $bulan);

        return view('laporan.obat_harian_print', compact('data', 'bulan', 'tahun'));
    }

    // ✅ Fungsi ambil data utama
    private function getDataObatHarian($tahun, $bulan)
{
    $lokasi = session('idpay'); // atau ambil dari session('idpay') jika perlu
    $periode = $tahun . str_pad($bulan, 2, '0', STR_PAD_LEFT);

    return DB::select("
        WITH data_keluar AS (
            SELECT 
                k.kode,
                EXTRACT(DAY FROM k.tanggal)::int AS tgl,
                SUM(k.qty) AS qty_harian
            FROM rme_obat_keluar k
            WHERE TO_CHAR(k.tanggal, 'YYYYMM') = ?
              AND k.lokasi = ?
            GROUP BY k.kode, EXTRACT(DAY FROM k.tanggal)
        ),
        pivot_harian AS (
            SELECT 
                kode,
                " . implode(", ", array_map(fn($i) => "COALESCE(SUM(CASE WHEN tgl = {$i} THEN qty_harian END),0) AS tgl_{$i}", range(1, 31))) . "
                FROM data_keluar
                GROUP BY kode
        )
        SELECT 
            o.kode_obat AS kode,
            o.nama_obat,
            o.stok,
            o.satuan,
            " . implode(", ", array_map(fn($i) => "p.tgl_{$i}", range(1, 31))) . ",
            (o.stok - COALESCE((" . implode("+", array_map(fn($i) => "p.tgl_{$i}", range(1, 31))) . "),0)) AS sisa
        FROM obats o
        LEFT JOIN pivot_harian p ON o.kode_obat = p.kode
        WHERE o.idpay = ?
        ORDER BY o.nama_obat
    ", [$periode, $lokasi, $lokasi]);
}

public function rekap(Request $r)
{
    $bulan = $r->bulan;
    $tahun = $r->tahun;
    $lokasi = session('idpay');
    $periode = $tahun . str_pad($bulan, 2, '0', STR_PAD_LEFT);

    // Validasi sederhana
    if (!$lokasi || !$bulan || !$tahun) {
        return response()->json(['error' => 'Parameter tidak lengkap'], 400);
    }

    $data = DB::select("
        SELECT 
            a.tanggal,
            a.nama_pasien,
            EXTRACT(YEAR FROM AGE(a.tanggal, c.tgl_lahir)) AS umur,
            c.gender,
            c.departemen,
            d.diagnosa,
            a.kode,
            b.nama_obat,
            a.satuan,
            a.qty,
            a.harga,
            a.jumlah
        FROM rme_obat_keluar a
        JOIN rme_master_obat b ON b.kode_obat = a.kode
        JOIN rme_entry_member c ON TRIM(c.no_rm) = TRIM(a.no_rm)
        JOIN rme_entry_diagnosa d ON TRIM(d.no_rm) = TRIM(a.no_rm) AND d.created_at = a.tanggal
        WHERE a.lokasi = ? 
          AND a.periode = ?
        ORDER BY a.tanggal, a.nama_pasien
    ", [$lokasi, $periode]);

    return response()->json($data);
}

public function monitoring(Request $r)
{
    $bulan = $r->bulan;
    $tahun = $r->tahun;
    $lokasi = session('idpay');

    if (!$lokasi || !$bulan || !$tahun) {
        return response()->json(['error' => 'Parameter tidak lengkap'], 400);
    }

    $data = DB::select("
        SELECT 
            g.kode,
            g.nama_obat,
            g.satuan,
            g.saldo_awal AS qty_awal,
            g.qm AS qty_masuk,
            g.qk AS qty_keluar,
            g.qs AS qty_akhir,
            g.hpp AS hpp_akhir,
            g.ts AS nilai_akhir,
            m.expired,
            m.no_batch
        FROM get_saldo_obat_per_bulan(?, ?, ?) g
        LEFT JOIN rme_obat_masuk m 
            ON m.kode = g.kode 
           AND m.lokasi = g.lokasi
        ORDER BY g.nama_obat
    ", [$tahun, $lokasi, $bulan]);

    return response()->json($data);
}


}
