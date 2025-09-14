<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ICD;

class MasterICDController extends Controller
{
    public function index()
    {
        $data = ICD::orderBy('kode_icd')->get();
        return view('master.icd', compact('data'));
    }

    public function create()
    {
        return view('master.icd_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_icd' => 'required|string|max:50|unique:icds,kode_icd',
            'diagnosis' => 'required|string|max:255',
        ]);

        ICD::create($request->all());
        return redirect()->route('master.icd.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = ICD::findOrFail($id);
        return view('master.icd_edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_icd' => 'required|string|max:50|unique:icds,kode_icd,'.$id.',kode_icd',
            'diagnosis' => 'required|string|max:255',
        ]);

        $row = ICD::findOrFail($id);
        $row->update($request->all());

        return redirect()->route('master.icd.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $row = ICD::findOrFail($id);
        $row->delete();
        return redirect()->route('master.icd.index')->with('success', 'Data berhasil dihapus.');
    }
}
