<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Illuminate\Http\Request;

class MasterKunjunganController extends Controller
{
    // Tampilkan semua data
    public function index()
    {
        $data = Kunjungan::orderBy('id_kunjungan')->get();
        return view('master.kunjungan', compact('data'));
    }

    // Form tambah
    public function create()
    {
        return view('master.kunjungan.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'jenis_kunjungan' => 'required|string|max:115',
        ]);

        Kunjungan::create([
            'id_kunjungan' => $request->id_kunjungan,   // ✅ langsung ambil dari input user
            'jenis_kunjungan' => $request->jenis_kunjungan
        ]);

        return redirect()->route('master.kunjungan.index')->with('success', 'Data berhasil ditambahkan.');
    }

    // Form edit
    public function edit($id)
    {
        $row = Kunjungan::findOrFail($id);
        return view('master.kunjungan_edit', compact('row'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_kunjungan' => 'required|string|max:115',
        ]);

        $row = Kunjungan::findOrFail($id);
        $row->update([
            'jenis_kunjungan' => $request->kunjungan
        ]);

        return redirect()->route('master.kunjungan.index')->with('success', 'Data berhasil diupdate.');
    }

    // Hapus data
    public function destroy($id)
    {
        $row = Kunjungan::findOrFail($id);
        $row->delete();

        return response()->json(['success' => true]); // ✅ bukan redirect, biar AJAX tidak error
    }

    // ✅ Tambahan: Update 1 field via AJAX
    public function updateField(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'field' => 'required|in:jenis_kunjungan',
            'value' => 'required|string|max:150',
        ]);

        $row = Kunjungan::findOrFail($request->id);
        $row->{$request->field} = $request->value;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->field) . ' berhasil diupdate'
        ]);
    }
}
