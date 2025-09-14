@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Welcome Card -->
    <div class="bg-blue-500/70 text-white shadow-2xl rounded-2xl backdrop-blur-sm p-6 mb-6">
        <h2 class="text-2xl font-bold mb-2">🎉 Selamat Datang, {{ $user->username }}</h2>
        <p class="text-lg">Lokasi aktif : <strong>{{ $lokasi }}</strong></p>
        <p class="opacity-90">Silakan pilih menu di sebelah kiri untuk melanjutkan.</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-md p-5 flex items-center">
            <div class="bg-blue-100 text-blue-600 p-3 rounded-lg mr-4">
                <i class="fas fa-users fa-lg"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Pasien</p>
                <h3 class="text-xl font-bold">124</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5 flex items-center">
            <div class="bg-green-100 text-green-600 p-3 rounded-lg mr-4">
                <i class="fas fa-pills fa-lg"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Stock Obat</p>
                <h3 class="text-xl font-bold">58</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-5 flex items-center">
            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-lg mr-4">
                <i class="fas fa-file-medical-alt fa-lg"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Laporan Bulan Ini</p>
                <h3 class="text-xl font-bold">17</h3>
            </div>
        </div>
    </div>

    <!-- Shortcut Menu -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold mb-4">🚀 Akses Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="{{ url('/register') }}" 
               class="bg-white rounded-xl shadow-md p-6 text-center hover:bg-blue-50 transition">
                <i class="fas fa-user-plus text-2xl text-blue-600 mb-2"></i>
                <p>Register</p>
            </a>
            <a href="{{ url('/diagnosa') }}" 
               class="bg-white rounded-xl shadow-md p-6 text-center hover:bg-green-50 transition">
                <i class="fas fa-syringe text-2xl text-green-600 mb-2"></i>
                <p>Diagnosa</p>
            </a>
            <a href="{{ url('/master/obat') }}" 
               class="bg-white rounded-xl shadow-md p-6 text-center hover:bg-yellow-50 transition">
                <i class="fas fa-capsules text-2xl text-yellow-600 mb-2"></i>
                <p>Master Obat</p>
            </a>
            <a href="{{ url('/report/kunjungan') }}" 
               class="bg-white rounded-xl shadow-md p-6 text-center hover:bg-purple-50 transition">
                <i class="fas fa-chart-line text-2xl text-purple-600 mb-2"></i>
                <p>Laporan</p>
            </a>
        </div>
    </div>
</div>
@endsection
