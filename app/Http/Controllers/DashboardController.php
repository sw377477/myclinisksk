<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // === Tanggal & ID User ===
        $today = now()->toDateString();
        $idpay = session('idpay');

        if (!$idpay) {
            return redirect('/login')->with('error', 'Sesi telah habis, silakan login ulang.');
        }

        // === Tahun aktif (dropdown) ===
        $selectedYear = $request->input('year', date('Y'));

        // === Statistik dasar ===
        $todayCount = DB::table('rme_entry_kunjungan')
            ->where('idpay', $idpay)
            ->whereDate('tgl_kunjungan', $today)
            ->count();

        $monthCount = DB::table('rme_entry_kunjungan')
            ->where('idpay', $idpay)
            ->whereBetween('tgl_kunjungan', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $yearCount = DB::table('rme_entry_kunjungan')
            ->where('idpay', $idpay)
            ->whereBetween('tgl_kunjungan', [now()->startOfYear(), now()->endOfYear()])
            ->count();

        // === Per jenis kunjungan (hari ini) ===
        $jenisToday = DB::table('rme_entry_kunjungan')
            ->select('id_pay', DB::raw('COUNT(*) as total'))
            ->where('idpay', $idpay)
            ->whereDate('tgl_kunjungan', $today)
            ->groupBy('id_pay')
            ->pluck('total', 'id_pay');

        // === Grafik per bulan (berdasarkan tahun terpilih) ===
        $chartDataRaw = DB::table('rme_entry_kunjungan')
            ->selectRaw("EXTRACT(MONTH FROM tgl_kunjungan) as bulan, COUNT(*) as total")
            ->whereYear('tgl_kunjungan', $selectedYear)
            ->where('idpay', $idpay)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Pastikan 12 bulan penuh (Jan–Des)
        $chartData = array_fill(1, 12, 0);
        foreach ($chartDataRaw as $item) {
            $chartData[(int)$item->bulan] = (int)$item->total;
        }

        // === Dropdown tahun (tahun ini dan +1) ===
        $currentYear = date('Y');
        $yearOptions = [$currentYear, $currentYear + 1];

        // === Nomor antrian terakhir (hari ini) ===
        $lastQueue = DB::table('rme_entry_kunjungan')
            ->where('idpay', $idpay)
            ->whereDate('tgl_kunjungan', $today)
            ->orderBy('no_kunjungan', 'desc')
            ->value('no_kunjungan');

        // === Kirim semua data ke view ===
        return view('dashboard', [
            'selectedYear' => $selectedYear,
            'yearOptions'  => $yearOptions,
            'chartData'    => array_values($chartData),
            'todayCount'   => $todayCount,
            'monthCount'   => $monthCount,
            'yearCount'    => $yearCount,
            'jenisToday'   => $jenisToday,
            'lastQueue'    => $lastQueue,
        ]);
    }
}
