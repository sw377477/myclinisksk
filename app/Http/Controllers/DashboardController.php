<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Tanggal sekarang
        $today = now()->toDateString();

        // Jumlah kunjungan hari ini
        $todayCount = DB::table('rme_entry_kunjungan')
            ->whereDate('tgl_kunjungan', $today)
            ->count();

        // Jumlah kunjungan sampai bulan ini
        $monthCount = DB::table('rme_entry_kunjungan')
            ->whereBetween('tgl_kunjungan', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->count();

        // Jumlah kunjungan sampai tahun ini
        $yearCount = DB::table('rme_entry_kunjungan')
            ->whereBetween('tgl_kunjungan', [
                now()->startOfYear(),
                now()->endOfYear()
            ])
            ->count();

        // Jumlah per jenis kunjungan (hari ini)
        $jenisToday = DB::table('rme_entry_kunjungan')
            ->select('jenis_kunjungan', DB::raw('COUNT(*) as total'))
            ->whereDate('tgl_kunjungan', $today)
            ->groupBy('jenis_kunjungan')
            ->pluck('total', 'jenis_kunjungan');

        // Grafik per bulan (setahun terakhir)
        $chartData = DB::table('rme_entry_kunjungan')
            ->selectRaw("TO_CHAR(tgl_kunjungan, 'YYYY-MM') as bulan, jenis_kunjungan, COUNT(*) as total")
            ->whereBetween('tgl_kunjungan', [
                now()->startOfYear(),
                now()->endOfYear()
            ])
            ->groupBy('bulan', 'jenis_kunjungan')
            ->orderBy('bulan')
            ->get();

        // Data antrian terakhir (anggap no_kunjungan = nomor antrian)
        $lastQueue = DB::table('rme_entry_kunjungan')
            ->whereDate('tgl_kunjungan', $today)
            ->orderBy('no_kunjungan', 'desc')
            ->value('no_kunjungan');

        return view('dashboard', [
            'todayCount' => $todayCount,
            'monthCount' => $monthCount,
            'yearCount' => $yearCount,
            'jenisToday' => $jenisToday,
            'chartData' => $chartData,
            'lastQueue' => $lastQueue,
        ]);
    }
}
