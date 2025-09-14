<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'MyClinis' }}</title>

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Tambahan CSS/JS khusus halaman --}}
    @yield('styles')
</head>
<body class="flex bg-gray-100 min-h-screen">

    <!-- Sidebar -->
<aside class="flex flex-col w-64 bg-gray-800 text-white h-screen sticky top-0 overflow-y-auto">
    <div class="p-4 text-center border-b border-gray-700">
        <h1 class="text-3xl font-bold">📋 MyClinis</h1>
        <p class="text-lg mt-1">Clinic Information System</p>
        <p class="text-m mt-1">{{ session('lokasi') ?? 'Belum dipilih' }}</p>
    </div>

    <nav class="flex-1 p-3">
        <ul class="space-y-2 text-lg"> <!-- font menu utama diperbesar -->
            
            <!-- Master -->
<details class="group {{ request()->is('master/*') ? 'open' : '' }}">
    <summary class="flex justify-between items-center px-4 py-2 rounded cursor-pointer
        {{ request()->is('master/*') ? 'bg-gray-700 text-yellow-400 font-bold' : 'hover:bg-gray-700' }}">
        📂 Master
        <span class="transition-transform duration-200 group-open:rotate-90">▶</span>
    </summary>
    <ul class="mt-1 space-y-1 text-gray-300 text-lg">
        <li class="border-b border-gray-700">
            <a href="{{ route('master.poli.index') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('master/poli') ? 'text-yellow-400 font-bold' : '' }}">
                📌 Poli
            </a>
        </li>
        <li class="border-b border-gray-700">
            <a href="{{ route('master.payment.index') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('master/payment') ? 'text-yellow-400 font-bold' : '' }}">
                📌 Payment
            </a>
        </li>
        <li class="border-b border-gray-700">
            <a href="{{ route('master.obat.index') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('master/obat') ? 'text-yellow-400 font-bold' : '' }}">
                📌 Obat
            </a>
        </li>
        <li class="border-b border-gray-700">
            <a href="{{ route('master.kunjungan.index') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('master/kunjungan') ? 'text-yellow-400 font-bold' : '' }}">
                📌 Kunjungan
            </a>
        </li>
        <li class="border-b border-gray-700">
            <a href="{{ route('master.logo.index') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('master/logo') ? 'text-yellow-400 font-bold' : '' }}">
                📌 Logo
            </a>
        </li>
        <li class="border-b border-gray-700">
            <a href="{{ route('master.satuan.index') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('master/satuan') ? 'text-yellow-400 font-bold' : '' }}">
                📌 Satuan
            </a>
        </li>
        <li class="border-b border-gray-700">
            <a href="{{ route('master.kategori.index') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('master/kategori') ? 'text-yellow-400 font-bold' : '' }}">
                📌 Kategori
            </a>
        </li>
        <li class="border-b border-gray-700">
            <a href="{{ route('master.icd.index') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('master/icd') ? 'text-yellow-400 font-bold' : '' }}">
                📌 ICD
            </a>
        </li>
    </ul>
</details>

            <!-- menu lain -->
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
    <a href="{{ url('/stock-obat') }}"
       class="block px-4 py-2 rounded hover:bg-gray-700 
       {{ request()->is('stock-obat') ? 'bg-gray-700 text-yellow-400 font-bold' : '' }}">
        💊 Stock Obat
    </a>
</li>

<!-- Report -->
<details class="group {{ request()->is('report/*') ? 'open' : '' }}">
    <summary class="flex justify-between items-center px-4 py-2 rounded cursor-pointer
        {{ request()->is('report/*') ? 'bg-gray-700 text-yellow-400 font-bold' : 'hover:bg-gray-700' }}">
        📑 Report
        <span class="transition-transform duration-200 group-open:rotate-90">▶</span>
    </summary>
    <ul class="mt-1 space-y-1 text-gray-300 text-lg">
        <li class="border-b border-gray-700">
            <a href="{{ url('/report/kunjungan') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('report/kunjungan') ? 'text-yellow-400 font-bold' : '' }}">
                <i class="fas fa-clipboard-list mr-2"></i> 🚀 Kunjungan
            </a>
        </li>
        <li class="border-b border-gray-700">
            <a href="{{ url('/report/obat') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('report/obat') ? 'text-yellow-400 font-bold' : '' }}">
                <i class="fas fa-prescription-bottle-alt mr-2"></i> 🚀 Obat
            </a>
        </li>
        <li class="border-b border-gray-700">
            <a href="{{ url('/report/biaya') }}"
               class="block pl-8 py-1 hover:text-yellow-400 
               {{ request()->is('report/biaya') ? 'text-yellow-400 font-bold' : '' }}">
                <i class="fas fa-coins mr-2"></i> 🚀 Biaya
            </a>
        </li>
    </ul>
</details>



                </ul>
            </details>
        </ul>
    </nav>

    <!-- Logout tetap di bawah sidebar -->
    <div class="p-3 border-t border-gray-700 mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-white text-base">
                Logout
            </button>
        </form>
    </div>
</aside>


    <!-- Main content -->
    <main class="flex-1 p-4 overflow-auto">
        @yield('content')
    </main>

    {{-- Scripts halaman khusus --}}
    @yield('scripts')
</body>
</html>
