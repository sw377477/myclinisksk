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
        session(['lokasi' => $request->lokasi]);
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
