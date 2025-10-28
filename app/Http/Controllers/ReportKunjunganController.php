<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportKunjunganController extends Controller
{
    public function index()
    {
        return view('pages.report-kunjungan');
    }

    public function getData(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $lokasi = session('idpay'); // ambil dari session

        if (!$bulan || !$tahun) {
            return response()->json([]);
        }

        // Range tanggal untuk bulan yang dipilih
        $tanggalAwal = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
        $tanggalAkhir = date("Y-m-t", strtotime($tanggalAwal));

        // Query utama
        $rows = DB::select("
            SELECT 
                a.tgl_kunjungan,
                a.jam_kunjungan,
                a.no_kunjungan,
                b.nm_member,
                b.no_rm,
                c.jenis_kunjungan,
                d.poli,
                d.nama_medis,
                e.payment,
                f.diagnosa
            FROM rme_entry_kunjungan a
            JOIN rme_entry_member b ON b.id_member = a.id_member AND b.idpay = a.idpay
            JOIN rme_master_jenis_kunjungan c ON c.id_kunjungan = a.jenis_kunjungan
            JOIN rme_master_poli d ON d.id_poli = a.id_poli AND d.idpay = a.idpay
            JOIN rme_master_payment e ON e.id_pay = a.id_pay
            JOIN rme_entry_diagnosa f ON TRIM(f.no_rm) = TRIM(b.no_rm) AND f.created_at = a.tgl_kunjungan
            WHERE a.idpay = :idpay
              AND a.tgl_kunjungan BETWEEN :tglAwal AND :tglAkhir
            ORDER BY a.tgl_kunjungan DESC
        ", [
            'idpay' => $lokasi,
            'tglAwal' => $tanggalAwal,
            'tglAkhir' => $tanggalAkhir
        ]);

        return response()->json($rows);
    }
}
