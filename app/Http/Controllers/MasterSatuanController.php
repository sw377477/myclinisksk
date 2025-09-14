<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Satuan;

class MasterSatuanController extends Controller
{
    public function index()
    {
        $data = Satuan::orderBy('id_satuan')->get();
        return view('master.satuan', compact('data'));
    }

    public function create()
    {
        return view('master.satuan_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'satuan' => 'required|string|max:255',
        ]);

        Satuan::create($request->all());
        return redirect()->route('master.satuan.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = Satuan::findOrFail($id);
        return view('master.satuan_edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'satuan' => 'required|string|max:255',
        ]);

        $row = Satuan::findOrFail($id);
        $row->update($request->all());

        return redirect()->route('master.satuan.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $row = Satuan::findOrFail($id);
        $row->delete();
        return redirect()->route('master.satuan.index')->with('success', 'Data berhasil dihapus.');
    }
}
