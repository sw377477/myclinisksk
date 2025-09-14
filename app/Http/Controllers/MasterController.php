<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterController extends Controller
{
    // Tampilkan halaman master
    public function index()
    {
        return view('master.master'); // file Blade master.blade.php
    }

    // Data AJAX untuk DataTable Poli
    public function getPoli()
{
    $polis = DB::table('rme_master_poli')->get();
    return response()->json(['data' => $polis]);
}

public function getPayment()
{
    $payments = DB::table('rme_master_payment')->get();
    return response()->json(['data' => $payments]);
}
}

