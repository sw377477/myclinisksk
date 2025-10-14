<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MyClinis' }}</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Tambahan CSS/JS khusus halaman --}}
    @yield('styles')

    <style>
        html {
            font-size: 14px; /* Normalisasi ukuran font */
            zoom: 0.9; /* Tambahan agar tampilan lebih fit di hosting */
        }

        body {
            font-family: system-ui, sans-serif;
            line-height: 1.2; /* Biar tabel rapat */
            max-width: 100%;
            overflow-x: hidden; /* Cegah scroll horizontal */
        }

        table {
            font-size: 0.9rem; /* Pastikan tabel tidak membesar */
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body class="flex bg-gray-100 min-h-screen">

    {{-- Sidebar --}}
        <aside class="flex flex-col w-64 bg-gray-800 text-white h-[710px]">

        {{-- Header --}}
        <div class="p-4 text-center border-b border-gray-700">
            <h1 class="text-3xl font-bold">🩺 myClinis</h1>
            <p class="text-lg mt-1">Clinic Information System</p>
            <p class="text-m mt-1">{{ session('lokasi') ?? 'Belum dipilih' }}</p>
            <p class="text-sm mt-1">ID Lokasi : {{ session('idpay') ?? 'Belum dipilih' }}</p>
        </div>

        {{-- Scrollable Menu --}}
        <div class="flex-1 overflow-y-auto p-3">
            <ul class="space-y-2 text-lg">

                {{-- Master Menu --}}
                <details class="group {{ request()->is('master/*') ? 'open' : '' }}">
                    <summary class="flex justify-between items-center px-4 py-2 rounded cursor-pointer
                        {{ request()->is('master/*') ? 'bg-gray-700 text-yellow-400 font-bold' : 'hover:bg-gray-700' }}">
                        📂 Master
                        <span class="transition-transform duration-200 group-open:rotate-90">▶</span>
                    </summary>
                    <ul class="mt-1 space-y-1 text-gray-300 text-lg">
                        @php
                            $masterLinks = [
                                ['route' => 'master.poli.index', 'path' => 'master/poli', 'label' => '📌 Poli'],
                                ['route' => 'master.payment.index', 'path' => 'master/payment', 'label' => '📌 Payment'],
                                ['route' => 'master.obat.index', 'path' => 'master/obat', 'label' => '📌 Obat'],
                                ['route' => 'master.kunjungan.index', 'path' => 'master/kunjungan', 'label' => '📌 Kunjungan'],
                                ['route' => 'master.logo.index', 'path' => 'master/logo', 'label' => '📌 Logo'],
                                ['route' => 'master.satuan.index', 'path' => 'master/satuan', 'label' => '📌 Satuan'],
                                ['route' => 'master.kategori.index', 'path' => 'master/kategori', 'label' => '📌 Kategori'],
                                ['route' => 'master.icd.index', 'path' => 'master/icd', 'label' => '📌 ICD-10'],
                            ];
                        @endphp

                        @foreach ($masterLinks as $item)
                            <li class="border-b border-gray-700">
                                <a href="{{ route($item['route']) }}"
                                class="block pl-8 py-1 hover:text-yellow-400
                                {{ request()->is($item['path']) ? 'text-yellow-400 font-bold' : '' }}">
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </details>

                {{-- Menu Lain --}}
                <li>
                    <a href="{{ url('/register') }}"
                    class="block px-4 py-2 rounded hover:bg-gray-700
                    {{ request()->is('register') ? 'bg-gray-700 text-yellow-400 font-bold' : '' }}">
                        📝 Register
                    </a>
                </li>
                <li>
                    <a href="{{ url('/diagnosa') }}"
                    class="block px-4 py-2 rounded hover:bg-gray-700
                    {{ request()->is('diagnosa') ? 'bg-gray-700 text-yellow-400 font-bold' : '' }}">
                        💉 Diagnosa
                    </a>
                </li>
                <li>
                    <a href="{{ url('/pages/stock-obat') }}"
                    class="block px-4 py-2 rounded hover:bg-gray-700
                    {{ request()->is('stock-obat') ? 'bg-gray-700 text-yellow-400 font-bold' : '' }}">
                        💊 Stock Obat
                    </a>
                </li>

                {{-- Report Menu --}}
                <details class="group {{ request()->is('report/*') ? 'open' : '' }}">
                    <summary class="flex justify-between items-center px-4 py-2 rounded cursor-pointer
                        {{ request()->is('report/*') ? 'bg-gray-700 text-yellow-400 font-bold' : 'hover:bg-gray-700' }}">
                        📉 Report
                        <span class="transition-transform duration-200 group-open:rotate-90">▶</span>
                    </summary>
                    <ul class="mt-1 space-y-1 text-gray-300 text-lg">
                        <li class="border-b border-gray-700">
                            <a href="{{ url('/report/kunjungan') }}"
                            class="block pl-8 py-1 hover:text-yellow-400
                            {{ request()->is('report/kunjungan') ? 'text-yellow-400 font-bold' : '' }}">
                                🎯 Kunjungan
                            </a>
                        </li>
                        <li class="border-b border-gray-700">
                            <a href="{{ url('/report/obat') }}"
                            class="block pl-8 py-1 hover:text-yellow-400
                            {{ request()->is('report/obat') ? 'text-yellow-400 font-bold' : '' }}">
                                🎯 Obat
                            </a>
                        </li>
                        <li class="border-b border-gray-700">
                            <a href="{{ url('/report/biaya') }}"
                            class="block pl-8 py-1 hover:text-yellow-400
                            {{ request()->is('report/biaya') ? 'text-yellow-400 font-bold' : '' }}">
                                🎯 Biaya
                            </a>
                        </li>
                    </ul>
                </details>

                <li>
                    <a href="{{ url('/karyawan') }}"
                    class="block px-4 py-2 rounded hover:bg-gray-700
                    {{ request()->is('karyawan') ? 'bg-gray-700 text-yellow-400 font-bold' : '' }}">
                        🧑‍🧑‍🧒‍🧒 Data Karyawan
                    </a>
                </li>
                <li>
                    <a href="{{ url('/satusehat') }}"
                    class="block px-4 py-2 rounded transition-all duration-200
                    {{ request()->is('satusehat') 
                        ? 'bg-gray-700 text-yellow-400 font-bold' 
                        : 'text-blue-300 hover:bg-gray-700 hover:text-yellow-400' }}">
                        Program Satu Sehat
                    </a>
                </li>

            </ul>
        </div>

        {{-- Tombol Panduan & Logout --}}
        <div class="p-3 border-t border-gray-700 flex flex-col gap-2">
            <button type="submit"
                    class="w-full bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded text-white text-base">
                Panduan
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white text-base">
                    Logout
                </button>
            </form>
        </div>

    </aside>


    {{-- Main Content --}}
    <main class="flex-1 p-4 overflow-auto">
        @yield('content')
    </main>

    {{-- Script halaman khusus --}}
    @yield('scripts')
    @section('scripts')
    @endsection
</body>
</html>
