<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use Illuminate\Http\Request;

class MasterPoliController extends Controller
{
    // Tampilkan semua data
    public function index()
    {
        $data = Poli::orderBy('id_poli')->get();
        return view('master.poli', compact('data'));
    }

    // Form tambah
    public function create()
    {
        return view('master.poli_create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'poli' => 'required|string|max:50',
            'nama_medis' => 'nullable|string|max:150',
        ]);

        // Generate id_poli otomatis
        $id = 'POLI' . str_pad(Poli::count() + 1, 3, '0', STR_PAD_LEFT);

        Poli::create([
            'id_poli' => $id,
            'poli' => $request->poli,
            'nama_medis' => $request->nama_medis
        ]);

        return redirect()->route('master.poli.index')->with('success', 'Data berhasil ditambahkan.');
    }

    // Form edit
    public function edit($id)
    {
        $row = Poli::findOrFail($id);
        return view('master.poli_edit', compact('row'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'poli' => 'required|string|max:50',
            'nama_medis' => 'nullable|string|max:150',
        ]);

        $row = Poli::findOrFail($id);
        $row->update([
            'poli' => $request->poli,
            'nama_medis' => $request->nama_medis
        ]);

        return redirect()->route('master.poli.index')->with('success', 'Data berhasil diupdate.');
    }

    // Hapus data
    public function destroy($id)
    {
        $row = Poli::findOrFail($id);
        $row->delete();

        return redirect()->route('master.poli.index')->with('success', 'Data berhasil dihapus.');
    }
}
