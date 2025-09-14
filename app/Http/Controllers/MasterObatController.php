<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Obat;
use Carbon\Carbon;

class MasterObatController extends Controller
{
    public function index()
    {
        // Ambil data obat
        $obat = Obat::orderBy('nama_obat')->get();

        // Lookup dropdown
        $kategori = DB::table('rme_master_kategori')->get();
        $satuan   = DB::table('rme_master_satuan')->get();
        $golongan = DB::table('rme_master_golongan')->get();

        return view('master.obat', compact('obat', 'kategori', 'satuan', 'golongan'));
    }

    public function data()
    {
        // Kalau nanti dipakai DataTables/AJAX
        $obat = Obat::orderBy('nama_obat','asc')->get();
        return response()->json($obat);
    }

    public function store(Request $request)
    {
    $id = Carbon::now()->format('YmdHis'); // contoh: 20250913154530

    // Ambil kode terakhir
    $lastKode = Obat::select('kode_obat')
                    ->where('kode_obat', 'like', 'KDO%')
                    ->orderByRaw('CAST(SUBSTRING(kode_obat, 4) AS INTEGER) DESC')
                    ->first();

    // Hitung kode baru
    if ($lastKode) {
        $number = (int)substr($lastKode->kode_obat, 3) + 1;
    } else {
        $number = 1;
    }
    $newKode = 'KDO' . $number;

try {
    $obat = Obat::create([
        'id_obat' => $id,
        'kode_obat' => $request->kode_obat,
        'nama_obat' => $request->nama_obat,
        'kategori' => $request->kategori,
        'satuan' => $request->satuan,
        'stok_minimal' => $request->stok_minimal,
        'golongan' => $request->golongan,
        'is_aktif' => 1,
        'tgl_input' => now(),
        'user_input' => auth()->user()->name ?? 'system',
    ]);

    return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan.',
            'id_obat' => $obat->id_obat,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
    

    public function update(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $request->validate([
            'nama_obat' => 'required|string',
        ]);

        $obat->nama_obat   = $request->nama_obat;
        $obat->kategori    = $request->kategori ?? $obat->kategori;
        $obat->satuan      = $request->satuan ?? $obat->satuan;
        $obat->golongan    = $request->golongan ?? $obat->golongan;
        $obat->stok_minimal = $request->stok_minimal ?? $obat->stok_minimal;
        $obat->is_aktif    = $request->is_aktif ?? $obat->is_aktif;
        $obat->tgl_update  = now();
        $obat->user_update = auth()->user()->name ?? 'system';
        $obat->save();

        return redirect()->back()->with('success', 'Obat berhasil diupdate.');
    }

    public function destroy($id)
    {
    try {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    // Universal update via AJAX
    public function updateField(Request $request)
{
    $obat = Obat::findOrFail($request->id);

    $field = $request->field;
    $value = $request->value;

    // validasi field yang boleh diubah
    $allowed = ['is_aktif', 'kategori', 'satuan', 'golongan', 'stok_minimal','nama_obat'];
    if (!in_array($field, $allowed)) {
        return response()->json(['error' => 'Field tidak valid'], 422);
    }

    $obat->$field = $value;
    $obat->save();

    return response()->json(['success' => true, 'field' => $field, 'value' => $value]);
}
}
