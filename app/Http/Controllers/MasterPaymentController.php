<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class MasterPaymentController extends Controller
{
    // Tampilkan semua data
    public function index()
    {
        $data = Payment::orderBy('id_pay')->get();
        return view('master.payment', compact('data'));
    }

    // Form tambah
    public function create()
    {
        return view('master.payment.create');
    }

    // Simpan data baru
    public function store(Request $request)
    {
        $request->validate([
            'payment' => 'required|string|max:150',
        ]);

        Payment::create([
            'id_pay' => $request->id_pay,   // ✅ langsung ambil dari input user
            'payment' => $request->payment
        ]);

        return redirect()->route('master.payment.index')->with('success', 'Data berhasil ditambahkan.');
    }

    // Form edit
    public function edit($id)
    {
        $row = Payment::findOrFail($id);
        return view('master.payment_edit', compact('row'));
    }

    // Update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'payment' => 'required|string|max:150',
        ]);

        $row = Payment::findOrFail($id);
        $row->update([
            'payment' => $request->payment
        ]);

        return redirect()->route('master.payment.index')->with('success', 'Data berhasil diupdate.');
    }

    // Hapus data
    public function destroy($id)
    {
        $row = Payment::findOrFail($id);
        $row->delete();

        return response()->json(['success' => true]); // ✅ bukan redirect, biar AJAX tidak error
    }

    // ✅ Tambahan: Update 1 field via AJAX
    public function updateField(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'field' => 'required|in:payment',
            'value' => 'required|string|max:150',
        ]);

        $row = Payment::findOrFail($request->id);
        $row->{$request->field} = $request->value;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->field) . ' berhasil diupdate'
        ]);
    }
}
