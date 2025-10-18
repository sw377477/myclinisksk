<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\SaldoObatExport;

class SaldoObatController extends Controller
{

    public function sync(Request $request)
{
    try {
        $lokasi = session('idpay'); // ambil lokasi dari session
        if (!$lokasi) {
            return response()->json(['success' => false, 'message' => 'Session lokasi tidak ditemukan']);
        }
        DB::statement('CALL sync_saldo_obat(?)', [$lokasi]); // Jalankan procedure
        return response()->json(['success' => true, 'message' => 'Sinkronisasi berhasil']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}


    public function index()
    {
        return view('saldo-obat.index');
    }

    public function getData(Request $request)
    {
        $tahun = (int) $request->input('tahun');
        $bulan = (int) $request->input('bulan');
        $lokasi = (string) session('idpay');
        //$lokasi = 'CLKSK';

      //  \Log::info("PARAMS:", [
      //  'tahun' => $tahun,
      //  'lokasi' => $lokasi,
      //  'bulan' => $bulan

    //]);


        $sql = "
            SELECT kode, nama_obat, satuan,
                   saldo_awal as qty_awal,
                   saldo_awal_rp as nilai_awal,
                   saldo_awal_hpp as hpp_awal,
                   qm as qty_masuk, tm as nilai_masuk,
                   qk as qty_keluar, tk as nilai_keluar,
                   qs as qty_akhir, ts as nilai_akhir,
                   hpp as hpp_akhir
            FROM get_saldo_obat_per_bulan(?::int, ?::text, ?::int)
        ";

            try {
            $data = DB::select($sql, [$tahun, $lokasi, $bulan]);
        //    \Log::info("HASIL DATA:", $data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        return response()->json($data);
    }

    public function export(Request $request)
{
    $tahun = (int) $request->input('tahun', date('Y'));
    $bulan = (int) $request->input('bulan', date('n'));
    $lokasi = (string) session('idpay');

    $sql = "
        SELECT kode, nama_obat, satuan,
               saldo_awal as qty_awal,
               saldo_awal_rp as nilai_awal,
               saldo_awal_hpp as hpp_awal,
               qm as qty_masuk, tm as nilai_masuk,
               qk as qty_keluar, tk as nilai_keluar,
               qs as qty_akhir, ts as nilai_akhir,
               hpp as hpp_akhir
        FROM get_saldo_obat_per_bulan(?::int, ?::text, ?::int)
        ORDER BY nama_obat
    ";

    $data = DB::select($sql, [$tahun, $lokasi, $bulan]);

    // buat HTML table untuk Excel
    $html = view('saldo-obat.export', compact('data'))->render();

    return response($html)
        ->header('Content-Type', 'application/vnd.ms-excel')
        ->header('Content-Disposition', 'attachment; filename="saldo_obat_'.$tahun.'_'.$bulan.'.xls"');
}

//Entry Obat Masuk

public function create()
{
    $obatList = DB::table('rme_master_obat')
        ->select('kode_obat', 'nama_obat', 'satuan')
        ->orderBy('nama_obat', 'asc')
        ->get();

        // cek apakah data benar-benar ada
    //dd($obatList);

    return view('pages.stock-obat', compact('obatList'));
}

}