<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kunjungan;

class MasterKunjunganController extends Controller
{
    public function index()
    {
        $data = Kunjungan::orderBy('id_kunjungan')->get();
        return view('master.kunjungan', compact('data'));
    }

    public function create()
    {
        return view('master.kunjungan_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_kunjungan' => 'required|string|max:255',
        ]);

        Kunjungan::create($request->all());
        return redirect()->route('master.kunjungan.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = Kunjungan::findOrFail($id);
        return view('master.kunjungan_edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_kunjungan' => 'required|string|max:255',
        ]);

        $row = Kunjungan::findOrFail($id);
        $row->update($request->all());

        return redirect()->route('master.kunjungan.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $row = Kunjungan::findOrFail($id);
        $row->delete();
        return redirect()->route('master.kunjungan.index')->with('success', 'Data berhasil dihapus.');
    }
}
