<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EntryKunjungan;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /**
     * Menampilkan halaman register + mengirim data dropdown
     */
    public function index()
    {
        $idpay = session('idpay');
        if (!$idpay) {
            return redirect()->back()->with('error', 'Session lokasi tidak ditemukan.');
        }

        // Ambil hanya member berdasarkan lokasi aktif
        $members = DB::table('rme_entry_member')
            ->select('id_member', 'nm_member', 'no_rm')
            ->where('idpay', $idpay)
            ->orderBy('nm_member')
            ->get();

        // Jenis kunjungan (biasanya global)
        $jenisKunjungan = DB::table('rme_master_jenis_kunjungan')->orderBy('id_kunjungan')->get();

        // Polis khusus lokasi aktif (jika master_poli punya kolom idpay)
        $polis = DB::table('rme_master_poli')
            ->where('idpay', $idpay)
            ->orderBy('id_poli')
            ->get();

        // Payments (jika payment bersifat global, bisa tetap semua)
        $payments = DB::table('rme_master_payment')->orderBy('id_pay')->get();

        $today = Carbon::today()->toDateString();

        // Ambil entries untuk lokasi aktif (join dengan member/poli agar tampil nama)
        $entries = DB::table('rme_entry_kunjungan as e')
            // join member kecocokan id_member + idpay
            ->leftJoin('rme_entry_member as m', function($join) {
                $join->on('e.id_member', '=', 'm.id_member')
                     ->on('e.idpay', '=', 'm.idpay');
            })
            // join poli kecocokan id_poli + idpay
            ->leftJoin('rme_master_poli as p', function($join) {
                $join->on('e.id_poli', '=', 'p.id_poli')
                     ->on('e.idpay', '=', 'p.idpay');
            })
            // join payment (tidak memaksa idpay karena payment mungkin global)
            ->leftJoin('rme_master_payment as pay', 'e.id_pay', '=', 'pay.id_pay')
            ->leftJoin('rme_master_jenis_kunjungan as jk', 'e.jenis_kunjungan', '=', 'jk.id_kunjungan')
            ->select(
                'e.no_kunjungan',
                'e.tgl_kunjungan',
                'e.jam_kunjungan',
                'm.nm_member',
                'jk.jenis_kunjungan',
                'p.poli',
                'pay.payment',
                'e.status_lk'
            )
            ->whereDate('e.tgl_kunjungan', $today)
            ->where('e.idpay', $idpay) // hanya lokasi aktif
            ->orderBy('e.tgl_kunjungan', 'desc')
            ->orderBy('e.jam_kunjungan', 'desc')
            ->get();

        return view('pages.register', compact('members', 'jenisKunjungan', 'polis', 'payments', 'entries'));
    }

    /**
     * Simpan kunjungan baru
     */
    public function store(Request $request)
    {
        $idpay = session('idpay');
        if (!$idpay) {
            return redirect()->back()->with('error', 'Session lokasi tidak ditemukan.');
        }

        // Validasi sederhana
        $request->validate([
            'no_kunjungan'     => 'required|string|max:50',
            'jenis_kunjungan'  => 'required',
            'id_member'        => 'required',
            'id_poli'          => 'required',
            'id_pay'           => 'required' // jenis pembayaran tetap dari input
        ]);

        try {
            $tgl = date('Y-m-d');
            $jam = date('H:i:s');

            EntryKunjungan::create([
                'tgl_kunjungan'   => $tgl,
                'jam_kunjungan'   => $jam,
                'no_kunjungan'    => $request->no_kunjungan,
                'status_lk'       => " ",
                'jenis_kunjungan' => $request->jenis_kunjungan,
                'id_member'       => $request->id_member,
                'id_poli'         => $request->id_poli,
                'id_pay'          => $request->id_pay,
                'idpay'           => $idpay, // lokasi otomatis dari session
            ]);

            return redirect()->back()->with('success', 'Data kunjungan berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('Gagal simpan kunjungan: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Gagal menyimpan data kunjungan.');
        }
    }

    /**
     * Explore / export data berdasarkan bulan/tahun (AJAX)
     */
    public function exploreData(Request $request)
    {
        $idpay = session('idpay');
        if (!$idpay) {
            // jika dipanggil lewat AJAX, kembalikan array kosong
            return response()->json([], 403);
        }

        $type  = $request->type;   // kunjungan / pendaftaran
        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;
        $data  = [];

        if ($type === 'kunjungan') {
            $data = DB::table('rme_entry_kunjungan as a')
                ->join('rme_entry_member as b', function($join) {
                    $join->on('b.id_member', '=', 'a.id_member')
                         ->on('b.idpay', '=', 'a.idpay');
                })
                ->join('rme_master_jenis_kunjungan as c', function($join) {
                    $join->on('c.id_kunjungan', '=', 'a.jenis_kunjungan');
                })
                ->join('rme_master_poli as d', function($join) {
                    $join->on('d.id_poli', '=', 'a.id_poli')
                         ->on('d.idpay', '=', 'a.idpay');
                })
                ->join('rme_master_payment as e', 'e.id_pay', '=', 'a.id_pay')
                ->select(
                    'b.nm_member as pasien',
                    'b.no_rm',
                    'a.tgl_kunjungan as tanggal',
                    'a.jam_kunjungan as jam',
                    'a.no_kunjungan as nomor',
                    'c.jenis_kunjungan as kunjungan',
                    'd.poli',
                    'e.payment',
                    'a.status_lk'
                )
                ->where('a.idpay', $idpay) // filter lokasi
                ->whereRaw("DATE_PART('month', a.tgl_kunjungan) = ?", [$bulan])
                ->whereRaw("DATE_PART('year', a.tgl_kunjungan) = ?", [$tahun])
                ->orderBy('a.tgl_kunjungan')
                ->orderBy('a.jam_kunjungan')
                ->get();
        }

        // Logging (sebelum return)
        Log::info('Request Explore Data', [
            'type' => $type,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'idpay' => $idpay
        ]);
        Log::info('Query Result Count', ['count' => is_iterable($data) ? count($data) : 0]);

        return response()->json($data);
    }

    /**
     * Update status kunjungan (hanya untuk lokasi aktif)
     */
    public function updateStatus(Request $request, $nomor)
    {
        $idpay = session('idpay');
        if (!$idpay) {
            return response()->json(['success' => false, 'message' => 'Session lokasi tidak ditemukan.'], 403);
        }

        $status = $request->input('status');

        // Normalisasi: kalau kosong "" ganti jadi " "
        if ($status === "" || $status === null) {
            $status = " ";
        }

        $updated = DB::table('rme_entry_kunjungan')
            ->where('no_kunjungan', $nomor)
            ->where('idpay', $idpay) // pastikan hanya lokasi aktif
            ->update([
                'status_lk' => $status
            ]);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau bukan milik lokasi ini.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'nomor' => $nomor,
            'status' => $status
        ]);
    }

    /**
     * Tampilan master data (jika ada)
     */
    public function masterData()
    {
        return view('register'); // pastikan ini sesuai nama blade
    }

    /**
     * API untuk header (autocomplete) - hanya member lokasi aktif
     */
    public function apiHeader(Request $request)
    {
        $idpay = session('idpay');
        $jenis = $request->query('jenis', 'INTERNAL');

        if (!$idpay) {
            return response()->json([], 403);
        }

        $data = DB::table('rme_entry_member')
            ->select(
                DB::raw('TRIM(nm_member) as nama'),
                DB::raw('TRIM(no_rm) as nomor'),
                DB::raw('TRIM(nik_ktp) as ktp')
            )
            ->where('jenis', $jenis)
            ->where('idpay', $idpay)
            ->orderBy('nm_member')
            ->get();

        return response()->json($data);
    }

    /**
     * API detail member berdasarkan no_rm (terbatas lokasi aktif)
     */
    public function apiDetail(Request $request)
    {
        $idpay = session('idpay');
        if (!$idpay) {
            return response()->json([], 403);
        }

        $no_rm = trim($request->query('no_rm'));

        $data = DB::table('rme_entry_member')
            ->select(
                'id_member',
                'nm_member',
                'no_rm',
                'nik_ktp',
                'no_kk',
                'tgl_daftar',
                DB::raw("TO_CHAR(jam_daftar, 'HH24:MI:SS') as jam_daftar"),
                'nik_karyawan',
                DB::raw("tempat_lahir || ', ' || TO_CHAR(tgl_lahir, 'DD-MM-YYYY') AS ttl"),
                DB::raw("EXTRACT(YEAR FROM AGE(CURRENT_DATE, tgl_lahir))||' Th' AS umur"),
                DB::raw("CASE gender WHEN 'L' THEN 'Laki-laki' WHEN 'P' THEN 'Perempuan' ELSE gender END AS gender"),
                'gol_darah',
                'no_telp',
                'pekerjaan',
                'status',
                'agama',
                'pendidikan',
                'departemen',
                'jabatan',
                'divisi',
                'nama_sos',
                'no_sos',
                'status_sos'
            )
            ->where(DB::raw('TRIM(no_rm)'), $no_rm)
            ->where('idpay', $idpay)
            ->first(); // pakai first() agar JSON object, bukan array

        return response()->json($data ?? []);
    }
}
