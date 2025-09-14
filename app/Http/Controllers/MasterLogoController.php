<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Logo;

class MasterLogoController extends Controller
{
    public function index()
    {
        $data = Logo::orderBy('iddata')->get();
        return view('master.logo', compact('data'));
    }

    public function create()
    {
        return view('master.logo_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $logo = new Logo();
        if($request->hasFile('logo')){
            $logo->logo = file_get_contents($request->file('logo')->getRealPath());
        }
        $logo->save();

        return redirect()->route('master.logo.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $row = Logo::findOrFail($id);
        return view('master.logo_edit', compact('row'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $row = Logo::findOrFail($id);
        if($request->hasFile('logo')){
            $row->logo = file_get_contents($request->file('logo')->getRealPath());
        }
        $row->save();

        return redirect()->route('master.logo.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $row = Logo::findOrFail($id);
        $row->delete();
        return redirect()->route('master.logo.index')->with('success', 'Data berhasil dihapus.');
    }
}
