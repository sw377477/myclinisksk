<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use Illuminate\Http\Request;

class MasterPoliController extends Controller
{
    // 🔹 Tampilkan data sesuai lokasi (idpay)
    public function index()
    {
        $idpay = session('idpay');
        if (!$idpay) {
            return redirect()->back()->with('error', 'Session lokasi tidak ditemukan.');
        }

        $data = Poli::where('idpay', $idpay)
                    ->orderBy('id_poli')
                    ->get();

        return view('master.poli', compact('data'));
    }

    // 🔹 Simpan data baru
    public function store(Request $request)
    {
        $idpay = session('idpay');
        if (!$idpay) {
            return redirect()->back()->with('error', 'Session lokasi tidak ditemukan.');
        }

        $request->validate([
            'id_poli' => 'required|string|max:20',
            'poli' => 'required|string|max:50',
            'nama_medis' => 'nullable|string|max:150',
        ]);

        $exists = Poli::where('id_poli', $request->id_poli)
                      ->where('idpay', $idpay)
                      ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'ID Poli sudah terdaftar untuk lokasi ini.');
        }

        Poli::create([
            'id_poli' => $request->id_poli,
            'poli' => $request->poli,
            'nama_medis' => $request->nama_medis,
            'idpay' => $idpay,
        ]);

        return redirect()->route('master.poli.index')->with('success', 'Data berhasil ditambahkan.');
    }

    // 🔹 Form edit
    public function edit($id)
    {
        $idpay = session('idpay');
        $row = Poli::where('idpay', $idpay)
                   ->where('id_serial', $id)
                   ->firstOrFail();

        return view('master.poli_edit', compact('row'));
    }

    // 🔹 Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'poli' => 'required|string|max:50',
            'nama_medis' => 'nullable|string|max:150',
        ]);

        $idpay = session('idpay');

        $row = Poli::where('id_serial', $id)
                   ->where('idpay', $idpay)
                   ->first();

        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan untuk lokasi ini.'
            ], 404);
        }

        $row->update([
            'poli' => $request->poli,
            'nama_medis' => $request->nama_medis
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate.'
        ]);
    }

    // 🔹 Hapus data
    public function destroy($id)
    {
        $idpay = session('idpay');
        $row = Poli::where('idpay', $idpay)
                   ->where('id_serial', $id)
                   ->firstOrFail();

        $row->delete();

        return response()->json(['success' => true]);
    }

    // 🔹 Update satu kolom via AJAX (inline edit)
    public function updateField(Request $request)
    {
        $idpay = session('idpay');

        $request->validate([
            'id' => 'required|integer',
            'field' => 'required|in:poli,nama_medis',
            'value' => 'nullable|string|max:150',
        ]);

        $row = Poli::where('idpay', $idpay)
                   ->where('id_serial', $request->id)
                   ->first();

        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan untuk lokasi ini.'
            ], 404);
        }

        $row->{$request->field} = $request->value;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->field) . ' berhasil diupdate.'
        ]);
    }
}
