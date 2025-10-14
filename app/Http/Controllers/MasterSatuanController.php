<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Satuan;
use Illuminate\Http\Request;

class MasterSatuanController extends Controller
{
    // Tampilkan semua data
    public function index()
    {
        $data = Satuan::orderBy(DB::raw('CAST(id_satuan AS INTEGER)'))->get();
        return view('master.satuan', compact('data'));
    }

    // Form tambah
    public function create()
    {
        return view('master.satuan.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'satuan' => 'required|string|max:150',
        ]);

        Satuan::create([
            'id_satuan' => $request->id_satuan,   // ✅ langsung ambil dari input user
            'satuan' => $request->satuan
        ]);

        return redirect()->route('master.satuan.index')->with('success', 'Data berhasil ditambahkan.');
    }

    // Form edit
    public function edit($id)
    {
        $row = Satuan::findOrFail($id);
        return view('master.satuan', compact('row'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'satuan' => 'required|string|max:150',
        ]);

        $row = Satuan::findOrFail($id);
        $row->update([
            'satuan' => $request->satuan
        ]);

        return redirect()->route('master.satuan.index')->with('success', 'Data berhasil diupdate.');
    }

    // Hapus data
    public function destroy($id)
    {
        $row = Satuan::findOrFail($id);
        $row->delete();

        return response()->json(['success' => true]); // ✅ bukan redirect, biar AJAX tidak error
    }

    // ✅ Tambahan: Update 1 field via AJAX
    public function updateField(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'field' => 'required|in:satuan',
            'value' => 'required|string|max:150',
        ]);

        $row = Satuan::findOrFail($request->id);
        $row->{$request->field} = $request->value;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->field) . ' berhasil diupdate'
        ]);
    }
}
