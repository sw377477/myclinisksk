<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{
    public function getLokasi(Request $request)
    {
        // Ambil idpay dari session
        $idpay = session('idpay'); // misalnya "CLKSK"

        // Pastikan session tersedia
        if (!$idpay) {
            return response()->json(['error' => 'Session idpay not found'], 400);
        }

        // Ambil 3 huruf terakhir dari idpay, misalnya "KSK"
        $keyword = substr($idpay, -3);

        // Ambil lokasi yang iddata mengandung keyword itu
        $lokasi = DB::table('master_estate')
            ->select('iddata')
            ->where('iddata', 'ILIKE', '%' . $keyword . '%')
            ->distinct()
            ->orderBy('iddata')
            ->get();

        return response()->json($lokasi);
    }

    public function getDepartemen($iddata)
    {
        $departemen = DB::table('hrd_bagian')
            ->select('kode', 'ket')
            ->where('iddata', $iddata)
            ->orderBy('ket')
            ->get();

        return response()->json($departemen);
    }

    public function getKaryawanBulanan(Request $request)
    {
        $iddata = $request->input('iddata');
        $kode = $request->input('kode');
        
        $karyawan = DB::table('hrd_karyawan')
            ->select(
                'nik',
                'nama',
                'lp',
                DB::raw("lahir_tempat || ', ' || lahir_tgl AS ttl"),
                'ktp',
                DB::raw('no_bpjs_kesehatan AS bpjs'),
                'no_hp',
                DB::raw('ket_jab AS jabatan'),
                'idpt'
            )
            ->where('aktif', 'Y')
            ->where('status_karyawan', 1)
            ->when($iddata, fn($q) => $q->where('iddata', $iddata))
            ->when($kode, fn($q) => $q->where('bag', $kode))
            ->orderBy('nama')
            ->get();

        return response()->json($karyawan);
    }

    public function getEstate(Request $request)
    {
        // Ambil session idpay (contoh: 'CLKSK')
        $idpay = session('idpay');
        if (!$idpay) {
            return response()->json(['error' => 'Session idpay tidak ditemukan'], 400);
        }

        // Ambil 3 huruf terakhir saja (contoh: 'KSK')
        $iddata = substr($idpay, -3);

        // Ambil estate berdasarkan iddata
        $estates = DB::table('master_estate')
            ->select('estateid', 'nama')
            ->where('iddata', 'like', "%{$iddata}%")
            ->distinct()
            ->orderBy('nama')
            ->get();

        return response()->json($estates);
    }

    public function getDivisi($estateid)
    {
        $idpay = session('idpay');
        if (!$idpay) {
            return response()->json(['error' => 'Session idpay tidak ditemukan'], 400);
        }

        $iddata = substr($idpay, -3);

        $divisi = DB::table('master_divisi')
            ->select('divisiid', 'divisi')
            ->where('iddata', 'like', "%{$iddata}%")
            ->where('estateid', $estateid)
            ->where('aktif', 'Y')
            ->orderBy('divisi')
            ->get();

        return response()->json($divisi);
    }


    public function getKaryawanHarian(Request $request)
    {
        $divisiid = $request->input('divisiid');

        if (!$divisiid) {
            return response()->json(['error' => 'divisiid wajib diisi'], 400);
        }

        $karyawan = DB::table('hrd_karyawan AS a')
            ->join('hrd_pekerjaan AS b', function ($join) {
                $join->on('b.kode', '=', 'a.kerja')
                    ->on('b.iddata', '=', 'a.iddata');
                    })
            ->join('hrd_status_karyawan AS c', function ($join) {
                $join->on('c.kodestatus', '=', 'a.status_karyawan');
                    })
            ->select(
                'a.nik',
                'a.nama',
                'a.lp',
                DB::raw("a.lahir_tempat || ', ' || a.lahir_tgl AS ttl"),
                'b.ket',
                'c.status',
                'a.kk',
                'a.ktp',
                DB::raw('a.no_bpjs_kesehatan AS bpjs')
            )
            ->where('a.divisiid', $divisiid)
            ->where('a.aktif', 'Y')
            ->whereNotIn('a.status_karyawan', [1, 3])
            ->orderBy('a.nama')
            ->get();

        return response()->json($karyawan);
    }

    public function getKaryawanBorongan(Request $request)
    {
        $divisiid = $request->input('divisiid');

        if (!$divisiid) {
            return response()->json(['error' => 'divisiid wajib diisi'], 400);
        }

        $karyawan = DB::table('hrd_karyawan AS a')
            ->join('hrd_pekerjaan AS b', function ($join) {
                $join->on('b.kode', '=', 'a.kerja')
                    ->on('b.iddata', '=', 'a.iddata');
                    })
            ->join('hrd_status_karyawan AS c', function ($join) {
                $join->on('c.kodestatus', '=', 'a.status_karyawan');
                    })
            ->select(
                'a.nik',
                'a.nama',
                'a.lp',
                DB::raw("a.lahir_tempat || ', ' || a.lahir_tgl AS ttl"),
                'b.ket',
                'c.status',
                'a.kk',
                'a.ktp',
                DB::raw('a.no_bpjs_kesehatan AS bpjs')
            )
            ->where('a.divisiid', $divisiid)
            ->where('a.aktif', 'Y')
            ->where('a.status_karyawan', 3)
            ->orderBy('a.nama')
            ->get();

        return response()->json($karyawan);
    }

}
