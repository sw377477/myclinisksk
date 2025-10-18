<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
        public function generateNomorRM(Request $request)
{
    $idpay = session('idpay'); 
    $prefix = substr($idpay, -3); 
    $timestamp = Carbon::now('Asia/Jakarta')->format('YmdHis');
    $nomorRM = "{$prefix}.RM.{$timestamp}";

    return response()->json(['nomor_rm' => $nomorRM]);
}

    public function getData(Request $request)
{
    try {
        $type = $request->query('type'); // 'karyawan' atau 'nonkaryawan'
        $idpay = session('idpay'); // fallback
        $prefix = substr($idpay, -3); // contoh: 'KSK'

        if ($type === 'karyawan') {
            $data = DB::select("
                SELECT 
                    a.nama, a.ktp, a.lp, a.lahir_tempat as tmplahir, a.lahir_tgl as tgllahir,
                    a.no_bpjs_kesehatan as bpjskes, a.nik, pend.ket as pend, ag.ket as agama,
                    a.alamat, a.divisiid, div.divisi, b.ket as departemen, 
                    c.ket as jabatan, a.gol, a.kk 
                FROM HRD_KARYAWAN A
                LEFT JOIN MASTER_DIVISI DIV ON DIV.DIVISIID=A.DIVISIID AND DIV.IDDATA=A.IDDATA
                JOIN HRD_PENDIDIKAN PEND ON PEND.KODE=A.PEND
                JOIN HRD_AGAMA AG ON AG.KODE=A.AGAMA
                JOIN HRD_BAGIAN B ON B.KODE=A.BAG AND B.IDDATA=A.IDDATA
                JOIN HRD_JABATAN C ON C.KODE=A.JAB AND C.IDDATA=A.IDDATA
                WHERE A.AKTIF = 'Y'
                  AND A.IDDATA LIKE :prefix
                ORDER BY NAMA
            ", ['prefix' => "{$prefix}%"]);
        } elseif ($type === 'nonkaryawan') {
            $data = DB::select("
                SELECT nama, ktp, lp, tmplahir, tgllahir, bpjskes, kk
                FROM HRD_KELUARGA
                WHERE IDDATA LIKE :prefix
                ORDER BY NAMA
            ", ['prefix' => "{$prefix}%"]);
        } else {
            return response()->json(['error' => 'Tipe tidak valid'], 400);
        }

        return response()->json($data);
    } catch (\Throwable $e) {
        // kirimkan pesan error ke browser supaya mudah cek
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


public function getIdMember()
    {
        try {
            // Query untuk mencari ID tanpa celah seperti versi VB.NET
            $sql = "
                SELECT COALESCE(
                    (SELECT MIN(missing_id)
                     FROM generate_series(1, (SELECT COALESCE(MAX(id_member),0) + 1 FROM rme_entry_member)) missing_id
                     WHERE missing_id NOT IN (SELECT id_member FROM rme_entry_member)
                    ),
                    1
                ) AS next_id
            ";

            $result = DB::selectOne($sql);

            return response()->json(['next_id' => $result->next_id ?? 1]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function simpanMember(Request $request)
{
    try {
        $validated = $request->validate([
            'id_member' => 'required|integer',
            'nm_member' => 'required|string|max:150',
            'nik_ktp' => 'nullable|string|max:20',
            'nik_karyawan' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:50',
            'tgl_lahir' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'agama' => 'nullable|string|max:20',
            'pendidikan' => 'nullable|string|max:20',
            'pekerjaan' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:20',
            'gol_darah' => 'nullable|string|max:20',
            'no_rm' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:50',
            'departemen' => 'nullable|string|max:50',
            'divisi' => 'nullable|string|max:100',
            'tgl_member' => 'nullable|date',
            'jenis' => 'nullable|string|max:20',
            'idpay' => 'nullable|string|max:20',
        ]);

        // Cek apakah NIK/KTP sudah ada
        if (!empty($validated['nik_ktp'])) {
            $exists = DB::table('rme_entry_member')
                        ->where('nik_ktp', $validated['nik_ktp'])
                        ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'DUPLICATE_NIK',
                    'message' => '⚠️ NIK/KTP sudah terdaftar. Tidak bisa menyimpan data duplikat.'
                ]);
            }
        }

        // Insert data
        DB::table('rme_entry_member')->insert([
            'id_member' => $validated['id_member'],
            'nm_member' => $validated['nm_member'],
            'nik_ktp' => $validated['nik_ktp'],
            'nik_karyawan' => $validated['nik_karyawan'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tgl_lahir' => $validated['tgl_lahir'],
            'gender' => $validated['gender'],
            'agama' => $validated['agama'],
            'pendidikan' => $validated['pendidikan'],
            'pekerjaan' => $validated['pekerjaan'],
            'status' => $validated['status'],
            'gol_darah' => $validated['gol_darah'],
            'no_rm' => $validated['no_rm'],
            'jabatan' => $validated['jabatan'],
            'departemen' => $validated['departemen'],
            'divisi' => $validated['divisi'],
            'tgl_member' => now(),
            'nrow'=> 1,
            'tgl_daftar' => now()->toDateString(),
            'jam_daftar' => now()->toTimeString(),
            'jenis' => $validated['jenis'],
            'idpay' => $validated['idpay'] ?? session('idpay', 'CLXXX'),
        ]);

        return response()->json(['success' => true, 'message' => 'Data member berhasil disimpan.']);
    } catch (\Throwable $e) {
        // Tangkap error database (misal constraint unik)
        if (str_contains($e->getMessage(), 'UNIQUE') || str_contains($e->getMessage(), 'duplicate')) {
            return response()->json([
                'success' => false,
                'error_code' => 'DUPLICATE_NIK',
                'message' => '⚠️ NIK/KTP sudah terdaftar. Tidak bisa menyimpan data duplikat.'
            ]);
        }

        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

public function getPendaftaran(Request $request)
{
    $filter = $request->input('filter', 'today'); // default 'today'

    $query = DB::table('rme_entry_member');

    if ($filter === 'today') {
        $query->whereDate('tgl_daftar', now()->toDateString());
    } elseif ($filter === 'monthly') {
        $query->whereMonth('tgl_daftar', now()->month)
              ->whereYear('tgl_daftar', now()->year);
    }

    $data = $query->orderBy('tgl_daftar', 'desc')->get();

    return response()->json($data);
}


}

