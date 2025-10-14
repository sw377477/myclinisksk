<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logo;
use Illuminate\Support\Facades\Storage;

class MasterLogoController extends Controller
{
    public function uploadLogo(Request $request)
{
    $request->validate([
        'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
    ]);

    $iddata = session('iddata');

    if (!$iddata) {
        return back()->with('error', 'ID Data tidak ditemukan, silakan pilih lokasi dulu.');
    }

    $path = $request->file('logo')->store('logo', 'public');

    // pakai Eloquent, biar rapi
    Logo::updateOrCreate(
        ['iddata' => $iddata],
        ['logo'   => $path]
    );

    return back()->with('success', 'Logo berhasil diupload.');
}


    
    public function index()
    {
        //$iddata = session('iddata');
        $lokasi = session('lokasi');

        $iddata = session('iddata'); // ambil dari session

        $logo = Logo::where('iddata', $iddata)->first();

        return view('master.logo', compact('iddata', 'lokasi', 'logo'));
    }

    public function store(Request $request)
{
    $request->validate([
        'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
    ]);

    // cek dulu
    //dd(session('iddata'));

    //$iddata = session('iddata');

    // TESTING: pakai iddata fixed
    $iddata = session('iddata'); // ambil dari session

    if (!$iddata) {
        return back()->with('error', 'ID Data tidak ditemukan, silakan pilih lokasi dulu.');
    }

    

    $file = $request->file('logo');
    $path = $file->store('logo', 'public');

    // Insert / Update
    Logo::updateOrCreate(
        ['iddata' => $iddata],
        ['logo' => $path]
    );
    return redirect()->route('master.logo.index')->with('success', 'Logo berhasil disimpan.');


    //return back()->with('success', 'Logo berhasil disimpan.');
}



    public function update(Request $request)
{
    $request->validate([
        'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
    ]);

    //$iddata = session('iddata');
    $iddata = session('iddata'); // ambil dari session

    if (!$iddata) {
        return redirect()->route('master.logo.index')->with('success', 'Logo berhasil disimpan.');

    }

    $file = $request->file('logo');

    // hapus logo lama
    $logo = Logo::where('iddata', $iddata)->first();
    if ($logo && $logo->logo && \Storage::disk('public')->exists($logo->logo)) {
        \Storage::disk('public')->delete($logo->logo);
    }

    $path = $file->store('logo', 'public');

    Logo::updateOrCreate(
        ['iddata' => $iddata],
        ['logo' => $path]
    );

    return redirect()->route('master.logo.index')->with('success', 'Logo berhasil diperbarui.');
}


    public function destroy()
    {
        //$iddata = session('iddata');
        $iddata = session('iddata'); // ambil dari session
        $logo = Logo::where('iddata', $iddata)->first();

        if ($logo) {
            if ($logo->logo && Storage::disk('public')->exists($logo->logo)) {
                Storage::disk('public')->delete($logo->logo);
            }
            $logo->delete();
        }

        return redirect()->route('master.logo.index')->with('success', 'Logo berhasil dihapus.');
    }
}
