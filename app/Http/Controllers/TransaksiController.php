<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // Halaman form (preview nomor tanpa menulis ke DB)
    public function create(Request $request)
    {
        $lokasi = session('idpay') ?? null;
        $tipe   = $request->input('tipe', 'LT'); // default LT

        // tanggal default (dipakai hanya untuk preview bulan/tahun)
        $tanggal = $request->input('tgl_masuk', Carbon::now()->toDateString());
        $dt = Carbon::parse($tanggal);
        $tahun = $dt->year;
        $bulan = $dt->month;

        // ambil 3 karakter terakhir lokasi dengan fallback
        $lokasi3 = $lokasi ? (strlen($lokasi) >= 3 ? substr($lokasi, -3) : $lokasi) : '000';

        // Hanya baca last_number untuk preview (TIDAK update DB)
        $row = DB::table('rme_transaksi_nomor')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('tipe', $tipe)
            ->where('lokasi', $lokasi)
            ->first();

        $previewNumber = $row ? $row->last_number + 1 : 1;

        $romawi = [
            1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'
        ][$bulan] ?? $bulan;

        $noTransaksi = "{$previewNumber}/{$tipe}-Klinik/{$lokasi3}/{$romawi}/{$tahun}";

        return view('pages.stock-obat', [
            'noTransaksi' => $noTransaksi,
            'tanggal' => $tanggal,
            'tipe' => $tipe,
        ]);
    }

    // Endpoint AJAX (optional) untuk generate preview tanpa menulis DB
    public function generate(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'nullable|string',
        ]);

        $lokasi = session('idpay') ?? null;
        $tipe = $request->input('tipe', 'LT');
        $tanggal = $request->input('tanggal');
        $dt = Carbon::parse($tanggal);
        $tahun = $dt->year;
        $bulan = $dt->month;

        $lokasi3 = $lokasi ? (strlen($lokasi) >= 3 ? substr($lokasi, -3) : $lokasi) : 'XXX';

        $row = DB::table('rme_transaksi_nomor')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('tipe', $tipe)
            ->where('lokasi', $lokasi)
            ->first();

        $previewNumber = $row ? $row->last_number + 1 : 1;

        $romawi = [
            1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'
        ][$bulan] ?? $bulan;

        $noTransaksi = "{$previewNumber}/{$tipe}-Klinik/{$lokasi3}/{$romawi}/{$tahun}";

        return response()->json(['noTransaksi' => $noTransaksi, 'tanggal' => $tanggal]);
    }

    // Simpan transaksi: DI SINI last_number di-INCREMENT secara atomic
    public function store(Request $request)
    {
        $request->validate([
            'tgl_masuk'  => 'required|date',
            'lokasi'     => 'required|string',
            'kode_obat'  => 'required|array',
            'qty'        => 'required|array',
            'satuan'     => 'required|array',
            'harga'      => 'required|array',
            'jumlah'     => 'required|array',
            'expired'    => 'required|array',
            'batch'      => 'required|array',
            // tambahkan validasi field lain sesuai form (obat rows, qty, dll)
        ]);

        $lokasi = session('idpay') ?? null;
        if (!$lokasi) {
            return back()->with('error', 'Session lokasi belum ter-set.');
        }

        $tipe = $request->input('tipe', 'LT');
        $tanggal = $request->input('tgl_masuk');
        $dt = Carbon::parse($tanggal);
        $tahun = $dt->year;
        $bulan = $dt->month;

        try {
            $noTransaksiFinal = DB::transaction(function () use ($tahun, $bulan, $tipe, $lokasi, $tanggal, $request) {
                // ambil row dengan lock agar safe dari race condition
                $row = DB::table('rme_transaksi_nomor')
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->where('tipe', $tipe)
                    ->where('lokasi', $lokasi)
                    ->lockForUpdate()
                    ->first();

                if ($row) {
                    $next = $row->last_number + 1;
                    DB::table('rme_transaksi_nomor')->where('id', $row->id)->update(['last_number' => $next]);
                } else {
                    $next = 1;
                    DB::table('rme_transaksi_nomor')->insert([
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'tipe' => $tipe,
                        'lokasi' => $lokasi,
                        'last_number' => $next,
                    ]);
                }

                $lokasi3 = strlen($lokasi) >= 3 ? substr($lokasi, -3) : $lokasi;
                $romawi = [
                    1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'
                ][$bulan] ?? $bulan;
                $noTransaksi = "{$next}/{$tipe}-Klinik/{$lokasi3}/{$romawi}/{$tahun}";

                // Simpan ke rme_obat_masuk
                foreach ($request->kode_obat as $i => $kode) {
                    if (!$kode) continue;

                    DB::table('rme_obat_masuk')->insert([
                        'periode'    => $tahun . str_pad($bulan, 2, '0', STR_PAD_LEFT),
                        'lokasi'     => $lokasi,
                        'nomor'      => $noTransaksi,
                        'tanggal'    => $tanggal,
                        'unixrow'    => now()->timestamp,
                        'kode'       => $kode,
                        'qty'        => $request->qty[$i] ?? 0,
                        'satuan'     => $request->satuan[$i] ?? '',
                        'harga'      => $request->harga[$i] ?? 0,
                        'jumlah'     => $request->jumlah[$i] ?? 0,
                        'ackredit'   => null,
                        'ket'        => null,
                        'user_input' => gethostname(),
                        'expired'    => $request->expired[$i] ?? now()->addYear(),
                        'no_batch'   => $request->batch[$i] ?? '',
                    ]);
}

                return $noTransaksi;
            });

            return redirect()->route('stock-obat.create')->with('success', "Transaksi {$noTransaksiFinal} berhasil disimpan!");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }


// =============================== OBAT KELUAR ===================================

// 1️⃣ Preview nomor transaksi LK
public function createLK(Request $request)
{
    $lokasi = session('idpay') ?? null;
    $tipe   = $request->input('tipe', 'LK');

    // tanggal default (untuk preview bulan/tahun)
    $tanggalLk = $request->input('tgl_keluar', Carbon::now()->toDateString());
    $dt = Carbon::parse($tanggalLk);
    $tahun = $dt->year;
    $bulan = $dt->month;

    // ambil 3 karakter terakhir lokasi dengan fallback
    $lokasi3 = $lokasi ? (strlen($lokasi) >= 3 ? substr($lokasi, -3) : $lokasi) : '000';

    // Hanya baca last_number untuk preview (tidak update DB)
    $row = DB::table('rme_transaksi_nomor')
        ->where('tahun', $tahun)
        ->where('bulan', $bulan)
        ->where('tipe', $tipe)
        ->where('lokasi', $lokasi)
        ->first();

    $previewNumber = $row ? $row->last_number + 1 : 1;

    $romawi = [
        1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'
    ][$bulan] ?? $bulan;

    $noTransaksiLk = "{$previewNumber}/{$tipe}-Klinik/{$lokasi3}/{$romawi}/{$tahun}";

    return view('pages.stock-obat', [
        'no_transaksi_keluar' => $noTransaksiLk,
        'tanggal' => $tanggalLk,
        'tipe' => $tipe,
    ]);
}

// 2️⃣ Endpoint AJAX untuk generate preview nomor tanpa simpan DB
public function generateLK(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date',
        'tipe' => 'nullable|string',
    ]);

    $lokasi = session('idpay') ?? null;
    $tipe = $request->input('tipe', 'LK');
    $tanggalLk = $request->input('tanggal');
    $dt = Carbon::parse($tanggalLk);
    $tahun = $dt->year;
    $bulan = $dt->month;

    $lokasi3 = $lokasi ? (strlen($lokasi) >= 3 ? substr($lokasi, -3) : $lokasi) : 'XXX';

    $row = DB::table('rme_transaksi_nomor')
        ->where('tahun', $tahun)
        ->where('bulan', $bulan)
        ->where('tipe', $tipe)
        ->where('lokasi', $lokasi)
        ->first();

    $previewNumber = $row ? $row->last_number + 1 : 1;

    $romawi = [
        1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'
    ][$bulan] ?? $bulan;

    $noTransaksiLk = "{$previewNumber}/{$tipe}-Klinik/{$lokasi3}/{$romawi}/{$tahun}";

    return response()->json([
        'no_transaksi_keluar' => $noTransaksiLk,
        'tanggal' => $tanggalLk
    ]);
}

public function storeLK(Request $request)
{
    try {
        $request->validate([
            'tanggal'      => 'required|date',
            'lokasi'       => 'required|string',
            'nama_pasien'  => 'required|string',
            'no_rm'        => 'required|string',
            'kode_obat'    => 'required|array',
            'qty'          => 'required|array',
            'satuan'       => 'required|array',
            'harga'        => 'required|array',
            'jumlah'       => 'required|array',
        ]);

        $lokasi = session('idpay') ?? null;
        if (!$lokasi) {
            return response()->json(['success' => false, 'error' => 'Session lokasi belum ter-set.']);
        }

        $tipe = $request->input('tipe', 'LK');
        $tanggalLk = $request->input('tanggal');
        $dt = \Carbon\Carbon::parse($tanggalLk);
        $tahun = $dt->year;
        $bulan = $dt->month;

        $noTransaksiFinalLk = DB::transaction(function () use ($tahun, $bulan, $tipe, $lokasi, $tanggalLk, $request) {
            $row = DB::table('rme_transaksi_nomor')
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->where('tipe', $tipe)
                ->where('lokasi', $lokasi)
                ->lockForUpdate()
                ->first();

            $next = $row ? $row->last_number + 1 : 1;

            if ($row) {
                DB::table('rme_transaksi_nomor')->where('id', $row->id)->update(['last_number' => $next]);
            } else {
                DB::table('rme_transaksi_nomor')->insert([
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'tipe'  => $tipe,
                    'lokasi'=> $lokasi,
                    'last_number' => $next,
                ]);
            }

            $lokasi3 = strlen($lokasi) >= 3 ? substr($lokasi, -3) : $lokasi;
            $romawi = [1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$bulan] ?? $bulan;
            $noTransaksiLk = "{$next}/{$tipe}-Klinik/{$lokasi3}/{$romawi}/{$tahun}";

            foreach ($request->kode_obat as $i => $kode) {
                if (!$kode) continue;

                DB::table('rme_obat_keluar')->insert([
                    'periode'     => $tahun . str_pad($bulan, 2, '0', STR_PAD_LEFT),
                    'lokasi'      => $lokasi,
                    'nomor'       => $noTransaksiLk,
                    'tanggal'     => $tanggalLk,
                    'nama_pasien' => $request->nama_pasien,
                    'no_rm'       => $request->no_rm,
                    'kode'        => $kode,
                    'qty'         => $request->qty[$i] ?? 0,
                    'satuan'      => $request->satuan[$i] ?? '',
                    'harga'       => $request->harga[$i] ?? 0,
                    'jumlah'      => $request->jumlah[$i] ?? 0,
                    'acdebit'     => null,
                    'user_input'  => gethostname(),
                ]);
            }

            if ($request->filled('no_kunjungan')) {
                DB::table('rme_entry_kunjungan')
                    ->where('no_kunjungan', $request->no_kunjungan)
                    ->update(['status_lk' => 'Done']);
            }

            return $noTransaksiLk;
        });

        return response()->json([
            'success' => true,
            'no_transaksi_keluar' => $noTransaksiFinalLk,
            'message' => "Transaksi {$noTransaksiFinalLk} berhasil disimpan!"
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['success' => false, 'errors' => $e->errors()], 422);
    } catch (\Throwable $e) {
        \Log::error("Gagal simpan LK: " . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
}

//combo/LookUp nama Pasien
public function getPasienHariIni()
{
    $pasien = DB::select("
        SELECT a.id_member AS id, 
               b.nm_member AS nama, 
               a.no_kunjungan, 
               b.no_rm, 
               a.tgl_kunjungan, 
               a.jam_kunjungan
        FROM rme_entry_kunjungan a 
        JOIN rme_entry_member b ON b.id_member = a.id_member 
        WHERE a.tgl_kunjungan::date = CURRENT_DATE 
          AND a.status_lk <> 'Done' 
        ORDER BY a.jam_kunjungan
    ");

    return response()->json($pasien);
}

//lookUp Obat keluar
public function getObat($tahun, $bulan, $lokasi)
{
    $data = DB::select("
        SELECT kode, nama_obat, satuan, qs AS saldo, hpp AS harga
        FROM get_saldo_obat_per_bulan(?, ?, ?)
    ", [$tahun, $lokasi, $bulan]);

    return response()->json($data);
}

//Explore Transaksi obat masuk dan keluar
public function headerMasuk($tahun, $bulan)
    {
        $periode = $tahun . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lokasi = session('idpay');

        $rows = DB::select("
            SELECT DISTINCT tanggal, nomor
            FROM rme_obat_masuk
            WHERE lokasi = ? AND periode = ?
            ORDER BY tanggal, nomor
        ", [$lokasi, $periode]);

        return response()->json($rows);
    }

    public function detailMasuk($nomor)
    {
        $rows = DB::select("
            SELECT a.id,ROW_NUMBER() OVER(ORDER BY b.nama_obat) AS no, 
                   a.kode, b.nama_obat, a.qty, a.satuan, a.harga, a.jumlah, 
                   TO_CHAR(a.expired, 'YYYY-MM-DD') AS expired, a.no_batch, a.ket
            FROM rme_obat_masuk a
            JOIN rme_master_obat b ON b.kode_obat = a.kode
            WHERE a.nomor = ?
            ORDER BY b.nama_obat
        ", [$nomor]);

        return response()->json($rows);
    }

    public function headerKeluar($tahun, $bulan)
    {
        $periode = $tahun . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $lokasi = session('idpay');

        $rows = DB::select("
            SELECT DISTINCT tanggal, nomor, nama_pasien, no_rm
            FROM rme_obat_keluar
            WHERE lokasi = ? AND periode = ?
            ORDER BY nomor
        ", [$lokasi, $periode]);

        return response()->json($rows);
    }

    public function detailKeluar($nomor)
    {
        $rows = DB::select("
            SELECT ROW_NUMBER() OVER(ORDER BY b.nama_obat) AS no,
                   a.kode, b.nama_obat, a.qty, a.satuan, a.harga, a.jumlah
            FROM rme_obat_keluar a
            JOIN rme_master_obat b ON b.kode_obat = a.kode
            WHERE a.nomor = ?
            ORDER BY b.nama_obat
        ", [$nomor]);

        return response()->json($rows);
    }


//Informasi Stock Minimal -Tab
public function infoMinimal(Request $request)
{
    $bulan = $request->input('bulan', date('m'));
    $tahun = $request->input('tahun', date('Y'));
    $lokasi = session('idpay');

    $stokMinimal = DB::select("
        SELECT 
            a.kode_obat,
            a.nama_obat,
            a.satuan,
            a.stok_minimal,
            a.golongan,
            COALESCE(b.qs, 0) AS stock_akhir,
            CASE 
                WHEN a.stok_minimal > 0 AND COALESCE(b.qs, 0) <= a.stok_minimal 
                THEN 'YES' 
                ELSE NULL 
            END AS status_pp,
            CASE 
                WHEN a.stok_minimal > 0 AND COALESCE(b.qs, 0) <= a.stok_minimal 
                THEN true 
                ELSE false 
            END AS checklist
        FROM rme_master_obat a
        LEFT JOIN get_saldo_obat_per_bulan(?, ?, ?) b 
            ON b.kode = a.kode_obat
        ORDER BY a.nama_obat
    ", [$tahun, $lokasi, $bulan]);

    return response()->json($stokMinimal);
}

public function simpanPP(Request $request)
{
    $data = $request->input('data', []);
    $nomor = $request->input('nomor');
    $tanggal = $request->input('tanggal');
    $userpc = $request->ip();

    foreach ($data as $row) {
        DB::table('rme_entry_pp')->insert([
            'id_pp'       => (string) Str::uuid(),
            'tanggal'     => $tanggal,
            'kode_obat'   => $row['kode'],
            'nama_obat'   => $row['nama'],
            'satuan'      => $row['satuan'],
            'stock_akhir' => $row['stock'],
            'jumlah_pp'   => $row['jumlah_pp'],
            'userpc'      => gethostname(),
            'nomor'       => $nomor,
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Data PP berhasil disimpan']);
}

public function listGolongan()
{
    $golongan = DB::table('rme_master_golongan')
        ->select('id_gol', 'golongan')
        ->orderBy('golongan')
        ->get();

    return response()->json($golongan);
}

//daftar List PP
public function daftarPP(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        if (!$bulan || !$tahun) {
            return response()->json(['header' => [], 'detail' => []]);
        }

        // Header
        $header = DB::table('rme_entry_pp as a')
            ->select(DB::raw('ROW_NUMBER() OVER (ORDER BY a.nomor) as no_urut'),
                     'a.nomor',
                     'a.tanggal')
            ->whereMonth('a.tanggal', $bulan)
            ->whereYear('a.tanggal', $tahun)
            ->groupBy('a.nomor', 'a.tanggal')
            ->orderBy('a.nomor')
            ->get();

        return response()->json([
            'header' => $header,
        ]);
    }

    public function detailPP(Request $request)
{
    $nomor = $request->nomor;

    $detail = DB::table('rme_entry_pp as a')
        ->join('rme_master_obat as b','b.kode_obat','=','a.kode_obat')
        ->select(
            'a.nomor',
            'a.nama_obat',
            'a.satuan',
            'b.stok_minimal',
            DB::raw('CAST(a.stock_akhir AS INTEGER) as stock_akhir'),
            'a.jumlah_pp',
            'a.keterangan'
        )
        ->where('a.nomor',$nomor)
        ->orderBy('a.nama_obat','asc')
        ->get();

    // Tambahkan no_urut manual
    $detail = $detail->map(function ($item, $index) {
        $item->no_urut = $index + 1;
        return $item;
    });

    return response()->json([
        'detail' => $detail
    ]);
}


    // export excel/pdf (dummy dulu)
    public function exportPP(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $type  = $request->input('type');

        return "Export {$type} untuk bulan {$bulan}-{$tahun}";
    }

}
