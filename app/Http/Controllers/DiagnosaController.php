<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiagnosaController extends Controller
{
    // 🔹 Ambil pasien yang berkunjung hari ini
    public function getPasienHariIni()
    {
        $data = DB::table('rme_entry_kunjungan as a')
            ->join('rme_entry_member as b', 'b.id_member', '=', 'a.id_member')
            ->select('b.no_rm', 'b.nm_member')
            ->whereDate('a.tgl_kunjungan', DB::raw('CURRENT_DATE'))
            ->orderBy('b.nm_member')
            ->distinct()
            ->get();

        return response()->json($data);
    }

    // 🔹 Ambil daftar diagnosa ICD
    public function getDiagnosa()
    {
        $data = DB::table('rme_master_icd')
            ->select('kode_icd', 'diagnosis')
            ->orderBy('diagnosis')
            ->get();

        return response()->json($data);
    }


    public function getDetailPasien(Request $request)
    {
        $no_rm = trim($request->input('no_rm')); // ✨ Hapus spasi kiri-kanan

        $data = DB::table('rme_entry_kunjungan as a')
            ->join('rme_entry_member as b', 'b.id_member', '=', 'a.id_member')
            ->join('rme_master_poli as c', 'c.id_poli', '=', 'a.id_poli')
            ->join('rme_master_jenis_kunjungan as d', 'd.id_kunjungan', '=', 'a.jenis_kunjungan')
            ->select(
                'b.no_rm',
                'b.nm_member',
                DB::raw("EXTRACT(YEAR FROM AGE(CURRENT_DATE, b.tgl_lahir)) || ' Tahun' AS umur"),
                DB::raw("CASE b.gender WHEN 'L' THEN 'Laki-laki' WHEN 'P' THEN 'Perempuan' ELSE b.gender END AS gender"),
                'b.gol_darah',
                DB::raw("to_char(a.tgl_kunjungan, 'YYYY-MM-DD') AS tgl_kunjungan"),
                DB::raw("to_char(a.jam_kunjungan, 'HH24:MI:SS') AS jam_kunjungan"),
                'd.jenis_kunjungan',
                'c.poli'
            )
            ->whereRaw("TRIM(b.no_rm) = ?", [$no_rm]) // ✅ juga trim di sisi SQL
            ->orderByDesc('a.tgl_kunjungan')
            ->limit(1)
            ->first();

        return response()->json($data);
    }


    public function simpanAnamnesa(Request $request)
{
    try {
        DB::beginTransaction();

        // Simpan ke rme_entry_anamnesa
        DB::table('rme_entry_anamnesa')->insert([
            'no_rm' => trim($request->no_rm),
            'keluhan_utama' => $request->keluhan_utama,
            'riwayat_penyakit_sekarang' => $request->riwayat_penyakit_sekarang,
            'riwayat_penyakit_dahulu' => $request->riwayat_penyakit_dahulu,
            'riwayat_keluarga' => $request->riwayat_keluarga,
            'riwayat_sosial' => $request->riwayat_sosial,
            'alergi' => $request->alergi,
            'berat' => $request->berat,
            'tinggi' => $request->tinggi,
            'created_at' => now(),
        ]);

        // Simpan ke rme_entry_diagnosa
        DB::table('rme_entry_diagnosa')->insert([
            'no_rm' => trim($request->no_rm),
            'kode_icd' => $request->kode_icd,
            'diagnosa' => $request->diagnosa,
            'created_at' => now(),
        ]);

        DB::commit();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}



}
