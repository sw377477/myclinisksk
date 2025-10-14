<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $user = DB::table('rme_login_user')
            ->where('username', $request->username)
            ->where('password', $request->password)
            ->first();

        if ($user) {
            session(['user' => $user]);
            return redirect('/lokasi');
        }

        return back()->withErrors(['msg' => 'Username / password salah']);
    }

    public function showLokasi()
    {
        $user = session('user');
        if (!$user) return redirect('/login');

        $lokasi = DB::table('rme_login_lokasi')
            ->where('userid', $user->userid)
            ->get();

        return view('auth.lokasi', compact('lokasi'));
    }

    public function pilihLokasi(Request $request)
    {
    $iddata = $request->iddata;

    // Ambil nama lokasi berdasarkan iddata
    $lokasiData = DB::table('rme_login_lokasi')
                ->where('iddata', $iddata)
                ->first(); // ambil seluruh row;
    if (!$lokasiData) {
        return back()->with('error', 'Lokasi tidak ditemukan');
    }

    // Set session
    session([
        'iddata' => $iddata,
        'lokasi' => $lokasiData->lokasi,  // nama lokasi
        'idpay'  => $lokasiData->idpay,   // idpay
    ]);
    return redirect('/home');
}

    public function home()
    {
        $user = session('user');
        $lokasi = session('lokasi');

        if (!$user || !$lokasi) return redirect('/login');

       

        return view('home', compact('user', 'lokasi'));
    }
}
