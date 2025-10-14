<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use Illuminate\Http\Request;

class MasterKategoriController extends Controller
{
    // Tampilkan semua data
    public function index()
    {
        $data = Kategori::orderBy(DB::raw('CAST(id_kategori AS INTEGER)'))->get();
        return view('master.kategori', compact('data'));
    }

    // Form tambah
    public function create()
    {
        return view('master.kategori.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:150',
        ]);

        Kategori::create([
            'id_kategori' => $request->id_kategori,   // ✅ langsung ambil dari input user
            'kategori' => $request->kategori
        ]);

        return redirect()->route('master.kategori.index')->with('success', 'Data berhasil ditambahkan.');
    }

    // Form edit
    public function edit($id)
    {
        $row = Kategori::findOrFail($id);
        return view('master.kategori', compact('row'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:150',
        ]);

        $row = Kategori::findOrFail($id);
        $row->update([
            'kategori' => $request->kategori
        ]);

        return redirect()->route('master.kategori.index')->with('success', 'Data berhasil diupdate.');
    }

    // Hapus data
    public function destroy($id)
    {
        $row = Kategori::findOrFail($id);
        $row->delete();

        return response()->json(['success' => true]); // ✅ bukan redirect, biar AJAX tidak error
    }

    // ✅ Tambahan: Update 1 field via AJAX
    public function updateField(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'field' => 'required|in:kategori',
            'value' => 'required|string|max:150',
        ]);

        $row = Kategori::findOrFail($request->id);
        $row->{$request->field} = $request->value;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->field) . ' berhasil diupdate'
        ]);
    }
}
