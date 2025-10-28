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

public function destroyKeluar($nomor)
{
    try {
        // Hapus semua data dengan nomor yang sama
        DB::table('rme_obat_keluar')
            ->where('nomor', $nomor)
            ->delete();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('Gagal hapus data keluar: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

public function destroyMasuk($nomor)
{
    try {
        DB::table('rme_obat_masuk')
            ->where('nomor', $nomor)
            ->delete();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('Gagal hapus data masuk: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

public function update(Request $request)
{
    $nomor = $request->nomor;
    $rows = $request->rows;
    $lokasi = session('idpay');
    $nama_pasien = $request->nama_pasien;
    $no_rm = $request->no_rm;
    $tanggal = $request->tanggal;

    DB::beginTransaction();
    try {
        // 🔹 Ambil semua ID lama di DB untuk nomor ini
        $existingIds = DB::table('rme_obat_keluar')
            ->where('nomor', $nomor)
            ->pluck('id')
            ->toArray();

        // 🔹 Kumpulkan semua ID yang dikirim user (yang tidak null)
        $sentIds = collect($rows)->pluck('id')->filter()->toArray();

        // 🔹 Cari baris yang dihapus oleh user (ada di DB tapi tidak dikirim)
        $deletedIds = array_diff($existingIds, $sentIds);

        // 🔹 Hapus baris yang dihapus user
        if (!empty($deletedIds)) {
            DB::table('rme_obat_keluar')->whereIn('id', $deletedIds)->delete();
        }

        // 🔹 Loop baris kiriman
        foreach ($rows as $row) {
            if (!empty($row['id'])) {
                // ✅ Update baris lama
                DB::table('rme_obat_keluar')
                    ->where('id', $row['id'])
                    ->update([
                        'kode' => $row['kode'],
                        'qty' => $row['qty'],
                        'satuan' => $row['satuan'],
                        'harga' => $row['harga'],
                        'jumlah' => $row['jumlah'],
                        'nama_pasien' => $nama_pasien,
                        'no_rm' => $no_rm,
                        'tanggal' => $tanggal,
                        'tgl_update' => now(),
                        'jam_update' => now(),
                        'user_update' => auth()->user()->name ?? 'system',
                    ]);
            } else {
                // 🆕 Insert baris baru
                DB::table('rme_obat_keluar')->insert([
                    'nomor' => $nomor,
                    'kode' => $row['kode'],
                    'qty' => $row['qty'],
                    'satuan' => $row['satuan'],
                    'harga' => $row['harga'],
                    'jumlah' => $row['jumlah'],
                    'periode' => now()->format('Ym'),
                    'lokasi' => $lokasi,
                    'tanggal' => $tanggal,
                    'nama_pasien' => $nama_pasien,
                    'no_rm' => $no_rm,
                    'user_input' => auth()->user()->name ?? 'system',
                    'tgl_input' => now(),
                    'jam_input' => now(),
                ]);
            }
        }

        DB::commit();
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
}

public function updateMasuk(Request $request)
{
    $nomor = $request->input('nomor');
    $rows = $request->input('rows', []);

    DB::beginTransaction();

    try {
        // 1️⃣ Ambil semua ID lama berdasarkan nomor
        $oldIds = DB::table('rme_obat_masuk')
            ->where('nomor', $nomor)
            ->pluck('id')
            ->toArray();

        $newIds = [];

        // 2️⃣ Loop semua baris baru (insert/update)
        foreach ($rows as $row) {
            if (!empty($row['id'])) {
                // Jika baris sudah ada → update
                DB::table('rme_obat_masuk')
                    ->where('id', $row['id'])
                    ->update([
                        'kode'       => $row['kode'],
                        'qty'        => $row['qty'],
                        'satuan'     => $row['satuan'],
                        'harga'      => $row['harga'],
                        'jumlah'     => $row['jumlah'],
                        'expired'    => $row['expired'] ?? null,
                        'no_batch'   => $row['no_batch'] ?? null,
                        'ket'        => $row['ket'] ?? null,
                        'user_update'=> auth()->user()->name ?? 'system',
                        'tgl_update' => now()->toDateString(),
                        'jam_update' => now()->toTimeString(),
                    ]);

                $newIds[] = $row['id'];
            } else {
                // Jika baris baru → insert
                $newId = DB::table('rme_obat_masuk')->insertGetId([
                    'periode'     => $request->input('periode'),
                    'lokasi'      => $request->input('lokasi'),
                    'nomor'       => $nomor,
                    'tanggal'     => $request->input('tanggal'),
                    'kode'        => $row['kode'],
                    'qty'         => $row['qty'],
                    'satuan'      => $row['satuan'],
                    'harga'       => $row['harga'],
                    'jumlah'      => $row['jumlah'],
                    'expired'     => $row['expired'] ?? null,
                    'no_batch'    => $row['no_batch'] ?? null,
                    'ket'         => $row['ket'] ?? null,
                    'user_input'  => auth()->user()->name ?? 'system',
                    'tgl_input'   => now()->toDateString(),
                    'jam_input'   => now()->toTimeString(),
                    'unixrow'     => time(),
                ]);

                $newIds[] = $newId;
            }
        }

        // 3️⃣ Hapus baris lama yang tidak ada di data baru
        $deleteIds = array_diff($oldIds, $newIds);
        if (!empty($deleteIds)) {
            DB::table('rme_obat_masuk')
                ->whereIn('id', $deleteIds)
                ->delete();
        }

        DB::commit();

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}


}