<?php

namespace App\Http\Controllers;
use App\Models\ICD;
use Illuminate\Http\Request;

class MasterICDController extends Controller
{
    // Tampilkan semua data
    public function index()
    {
        $data = ICD::orderBy('kode_icd')->get();
        return view('master.icd', compact('data'));
    }

    // Form tambah
    public function create()
    {
        return view('master.icd.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'diagnosis' => 'required|string|max:115',
        ]);

        ICD::create([
            'kode_icd' => $request->kode_icd,   // ✅ langsung ambil dari input user
            'diagnosis' => $request->diagnosis
        ]);

        return redirect()->route('master.icd.index')->with('success', 'Data berhasil ditambahkan.');
    }

    // Form edit
    public function edit($id)
    {
        $row = ICD::findOrFail($id);
        return view('master.icd_edit', compact('row'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'diagnosis' => 'required|string|max:115',
        ]);

        $row = ICD::findOrFail($id);
        $row->update([
            'diagnosis' => $request->diagnosis
        ]);

        return redirect()->route('master.icd.index')->with('success', 'Data berhasil diupdate.');
    }

    // Hapus data
    public function destroy($id)
    {
        $row = ICD::findOrFail($id);
        $row->delete();

        return response()->json(['success' => true]); // ✅ bukan redirect, biar AJAX tidak error
    }

    // ✅ Tambahan: Update 1 field via AJAX
    public function updateField(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'field' => 'required|in:diagnosis',
            'value' => 'required|string|max:150',
        ]);

        $row = ICD::findOrFail($request->id);
        $row->{$request->field} = $request->value;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->field) . ' berhasil diupdate'
        ]);
    }
}
