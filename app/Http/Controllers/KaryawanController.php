<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $gol = $request->input('gol', '');
        $search = $request->input('search', '');

        $idpay = session('idpay'); // contoh: 'CLKSK'
        $iddata = substr($idpay, 2); // ambil 3 huruf terakhir, contoh: 'KSK'

        $karyawan = DB::table('hrd_karyawan')
        ->select('nik', 'nama', 'lp', 'no_bpjs_kesehatan', 'kk', 'ktp', 'idpt', 'gol')
        ->whereRaw("SUBSTR(iddata, 1, 3) = ?", [$iddata])
        ->when($gol != '', fn($q) => $q->where('gol', $gol))
        ->when($search != '', fn($q) =>
            $q->where(function ($sub) use ($search) {
                $sub->whereRaw('LOWER(nik) LIKE ?', ['%'.strtolower($search).'%'])
                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%'.strtolower($search).'%']);
            })
        )
        ->orderBy('nama')
        ->get();

        // Gol options untuk select
        $golOptions = [
            '02' => 'II',
            '03' => 'III',
            '04' => 'IV',
            '05' => 'V',
            '06' => 'VI'
        ];

        return view('pages.karyawan', [
            'karyawan' => $karyawan,
            'gol' => $gol,
            'search' => $search,
            'golOptions' => $golOptions
        ]);
    }

    public function keluarga($nik)
    {
        $idpay = session('idpay');
        $iddata = substr($idpay, 2); // ambil 'KSK'

        $keluarga = DB::table('hrd_keluarga')
                    ->select('nama', 'hubungan', DB::raw("tmplahir || ', ' || tgllahir as ttl"), 'umur', 'bpjskes')
                    ->where('nik_ikatan', $nik)
                    ->get();

        return response()->json($keluarga);
    }
}
