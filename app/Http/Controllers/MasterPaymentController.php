<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;

class MasterPaymentController extends Controller
{
    public function index()
    {
        $data = Payment::orderBy('id_pay')->get();
        return view('master.payment', compact('data'));
    }

    public function create()
    {
        return view('master.payment_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment' => 'required|string|max:255',
        ]);

        Payment::create($request->all());
        return redirect()->route('master.payment.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = Payment::findOrFail($id);
        return view('master.payment_edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payment' => 'required|string|max:255',
        ]);

        $row = Payment::findOrFail($id);
        $row->update($request->all());

        return redirect()->route('master.payment.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $row = Payment::findOrFail($id);
        $row->delete();
        return redirect()->route('master.payment.index')->with('success', 'Data berhasil dihapus.');
    }
}
