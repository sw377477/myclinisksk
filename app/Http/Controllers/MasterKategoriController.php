<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;

class MasterKategoriController extends Controller
{
    public function index()
    {
        $data = Kategori::orderBy('id_kategori')->get();
        return view('master.kategori', compact('data'));
    }

    public function create()
    {
        return view('master.kategori_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        Kategori::create($request->all());
        return redirect()->route('master.kategori.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = Kategori::findOrFail($id);
        return view('master.kategori_edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
        ]);

        $row = Kategori::findOrFail($id);
        $row->update($request->all());

        return redirect()->route('master.kategori.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $row = Kategori::findOrFail($id);
        $row->delete();
        return redirect()->route('master.kategori.index')->with('success', 'Data berhasil dihapus.');
    }
}
