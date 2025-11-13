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

                <li>
                    <a href="{{ url('/dashboard') }}"
                    class="block px-4 py-2 rounded hover:bg-gray-700
                    {{ request()->is('dashboard') ? 'bg-gray-700 text-yellow-400 font-bold' : '' }}">
                        🏠 Dashboard
                    </a>
                </li>

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
                        🎗️ Diagnosa
                    </a>
                </li>
                <li>
                    <a href="{{ url('/pages/stock-obat') }}"
                    class="block px-4 py-2 rounded hover:bg-gray-700
                    {{ request()->is('stock-obat') ? 'bg-gray-700 text-yellow-400 font-bold' : '' }}">
                        💊 Stock Obat
                    </a>
                </li>
                <li>
                    <a href="{{ url('/trxbiaya') }}"
                    class="block px-4 py-2 rounded hover:bg-gray-700
                    {{ request()->is('trxbiaya') ? 'bg-gray-700 text-yellow-400 font-bold' : '' }}">
                        💲 Transaksi Biaya
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
            <button id="btnBukaPanduan" 
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

                <!-- Modal Panduan dengan efek animasi -->
        <!-- POPUP UTAMA -->
        <div id="panduanModal"
            class="fixed inset-0 hidden bg-black/50 flex items-center justify-center z-50 opacity-0 transition-opacity duration-300">

            <div class="bg-white rounded-lg shadow-lg w-[600px] max-h-[90vh] overflow-y-auto p-6 transform scale-95 transition-transform duration-300">
                <h2 class="text-xl font-bold mb-4 text-gray-700">📘 Panduan Aplikasi</h2>
                <p class="text-gray-600 mb-3">Pilih menu di bawah untuk melihat panduan langkah demi langkah:</p>

                <div class="space-y-4 text-sm">
                    <!-- Menu Master -->
                    <div>
                        <h3 class="font-semibold text-gray-800">Menu Master</h3>
                        <ul class="ml-4 list-disc text-gray-600 space-y-1">
                            <li><button class="text-blue-600 hover:underline panduan-item" data-target="popupMasterObat">Master Obat</button></li>
                            <li><button class="text-blue-600 hover:underline panduan-item" data-target="popupMasterLogo">Master Logo</button></li>
                        </ul>
                    </div>

                    <!-- Menu Register -->
                    <div>
                        <h3 class="font-semibold text-gray-800">Menu Register</h3>
                        <ul class="ml-4 list-disc text-gray-600 space-y-1">
                            <li><button class="text-blue-600 hover:underline panduan-item" data-target="popupTabKunjungan">Data Kunjungan</button></li>
                            <li><button class="text-blue-600 hover:underline panduan-item" data-target="popupTabPendaftaran">Data Pendaftaran</button></li>
                    </div>

                    <!-- Menu Diagnosa -->
                    <div>
                        <h3 class="font-semibold text-gray-800">Menu Diagnosa</h3>
                        <ul class="ml-4 list-disc text-gray-600 space-y-1">
                            <li><button class="text-blue-600 hover:underline panduan-item" data-target="popupTabDiagnosa">Anamnesa</button></li>
                            <li><button class="text-blue-600 hover:underline panduan-item">Pemberian Resep</button></li>
                        </ul>
                    </div>

                    <!-- Menu Stock Obat -->
                    <div>
                        <h3 class="font-semibold text-gray-800">Menu Stock Obat</h3>
                        <ul class="ml-4 list-disc text-gray-600 space-y-1">
                            <li><button class="text-blue-600 hover:underline panduan-item" data-target="popupTabSaldoObat">Form Saldo Obat</button></li>
                            <li><button class="text-blue-600 hover:underline panduan-item" data-target="popupTabTransaksiObat">Transaksi Obat</button></li>
                            <li><button class="text-blue-600 hover:underline panduan-item" data-target="popupTabPPObat">PP Obat</button></li>
                        </ul>
                    </div>

                    <!-- Menu Data Karyawan -->
                    <div>
                        <h3 class="font-semibold text-gray-800">Menu Data Karyawan</h3>
                        <ul class="ml-4 list-disc text-gray-600 space-y-1">
                            <li><button class="text-blue-600 hover:underline panduan-item">Karyawan & Keluarga</button></li>
                            <li><button class="text-blue-600 hover:underline panduan-item">Detail Karyawan</button></li>
                            <li><button class="text-blue-600 hover:underline panduan-item">Integrasi HRIS</button></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 text-right">
                    <button id="btnPdfPanduan"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">
                         <strong>PDF</strong>
                    </button>

                    <button id="btnTutupPanduan"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1 rounded">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- POPUP KHUSUS MASTER OBAT -->
        <div id="popupMasterObat"
            class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-[60] opacity-0 transition-opacity duration-300">
            <div class="bg-white w-[500px] rounded-lg shadow-lg p-6 transform scale-95 transition-transform duration-300">
                <h3 class="text-xl font-bold mb-3 text-gray-800">🧠 Panduan Master Obat</h3>
                <ol class="list-decimal ml-5 text-gray-700 space-y-1">
                    <li>Buka menu <strong>Master → Master Obat</strong>.</li>
                    <li>Tekan tombol <strong>Tambah Obat</strong>.</li>
                    <li>Isi kolom seperti <strong>Kode</strong>, <strong>Nama Obat</strong>, dan <strong>Stok Minimal</strong>.</li>
                    <li><strong>Stok Minimal</strong> harus ditentukan di awal karena digunakan untuk notifikasi <strong>PP Obat</strong>.</li>
                    <li>Klik tombol <strong>Simpan</strong> untuk menyimpan data.</li>
                    <li>Gunakan tombol ✏️ untuk <strong>edit data</strong>.</li>
                    <li>Gunakan fitur 🔽dropdown untuk memilih <strong>Kategori</strong>, <strong>Satuan</strong>, dan <strong>Golongan</strong>.</li>
                    <li>Gunakan toggle untuk <strong>mengaktifkan</strong> atau <strong>menonaktifkan</strong> obat.</li>
                    <li>Gunakan fitur <strong>Hapus</strong> jika diperlukan.</li>
                </ol>
                <div class="mt-5 text-right">
                    <button class="close-detail bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Tutup</button>
                </div>
            </div>
        </div>

        <!-- POPUP KHUSUS MASTER LOGO -->
        <div id="popupMasterLogo"
            class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-[60] opacity-0 transition-opacity duration-300">
            <div class="bg-white w-[500px] rounded-lg shadow-lg p-6 transform scale-95 transition-transform duration-300">
                <h3 class="text-xl font-bold mb-3 text-gray-800">🖼️ Panduan Master Logo</h3>
                <ol class="list-decimal ml-5 text-gray-700 space-y-1">
                    <li>Pilih menu <strong>Master → Master Logo</strong>.</li>
                    <li>Klik papan atau seret gambar dan lepaskan pada papan <strong>Upload Logo</strong> yang disediakan.</li>
                    <li>Pilih file gambar berformat <strong>PNG</strong> atau <strong>JPG</strong>.</li>
                    <li>Klik <strong>Simpan/Ganti</strong> untuk memperbarui logo aplikasi.</li>
                </ol>
                <div class="mt-5 text-right">
                    <button class="close-detail bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Tutup</button>
                </div>
            </div>
        </div>

        <!-- POPUP KHUSUS TAB KUNJUNGAN -->
        <div id="popupTabKunjungan"
            class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-[60] opacity-0 transition-opacity duration-300">
            <div class="bg-white w-[500px] rounded-lg shadow-lg p-6 transform scale-95 transition-transform duration-300">
                <h3 class="text-xl font-bold mb-3 text-gray-800">📅 Panduan Entry Kunjungan Pasien</h3>
                <ol class="list-decimal ml-5 text-gray-700 space-y-1">
                    <li>Buka menu Register kemudian<strong> Tab Data Kunjungan</strong> akan disajikan menu form Entry.</li>
                    <li>Pilih atau Cari nama pasien yang sudah terdaftar administrasi klinik pada 🔽dropdown <strong>🔎Cari Member.</strong></li>
                    <li><strong>Id kunjungan</strong> akan otomatis terisi dari system dan <strong>nomor RM</strong> tampil sesuai pada saat pendaftaran.</li>
                    <li>Klik <strong>Simpan</strong> untuk menyimpan data kunjungan.</li>
                </ol>
                <div class="mt-5 text-right">
                    <button class="close-detail bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Tutup</button>
                </div>
            </div>
        </div>

        <!-- POPUP KHUSUS TAB PENDAFTARAN -->
        <div id="popupTabPendaftaran"
            class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-[60] opacity-0 transition-opacity duration-300">
            <div class="bg-white w-[500px] rounded-lg shadow-lg p-6 transform scale-95 transition-transform duration-300">
                <h3 class="text-xl font-bold mb-3 text-gray-800">📝 Panduan Entry Pendaftaran Pasien</h3>
                <ol class="list-decimal ml-5 text-gray-700 space-y-1">
                    <li>Buka menu Register kemudian<strong> Tab Data Pendaftaran</strong> akan disajikan menu form Entry.</li>
                    <li>Pilih Jenis <strong>[Internal atau External].</strong></li>
                    <li>Jika pilih <strong>Internal</strong> maka 🔘radiobutton aktif untuk opsi Karyawan atau Non Karyawan (keluarga) dan pemilihan daftar karyawan atau keluarga bisa dilakukan dengan 🔍Pencarian nama karyawan atau keluarga karyawan yang sudah terhubung ke <strong>HR System.</strong></li>
                    <li>Jika pilih <strong>External</strong> maka 🔘radiobutton nonaktif untuk opsi Karyawan atau Non Karyawan dan bisa langsung lakukan entry pendaftaran pasien secara manual.</li>
                    <li>Untuk Nomor RM sudah otomatis dari system, gunakan tombol ⚙ untuk <strong>Generate nomor RM</strong> jika ingin mengacak lagi nomor RM untuk pasien yang didaftarkan.</li>
                    <li>Isi data dengan benar sesuai dengan kebutuhan administrasi.</li>
                    <li>Klik <strong>Simpan</strong> untuk menyimpan data.</li>
                    <li>Data yang sudah di-entry akan muncul pada tabel sebelah kanan untuk bisa di explore kembali.</li>
                </ol>
                <div class="mt-5 text-right">
                    <button class="close-detail bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Tutup</button>
                </div>
            </div>
        </div>

        <!-- POPUP KHUSUS TAB PENDAFTARAN -->
        <div id="popupTabExplore"
            class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-[60] opacity-0 transition-opacity duration-300">
            <div class="bg-white w-[500px] rounded-lg shadow-lg p-6 transform scale-95 transition-transform duration-300">
                <h3 class="text-xl font-bold mb-3 text-gray-800">📝 Panduan Entry Pendaftaran Pasien</h3>
                <ol class="list-decimal ml-5 text-gray-700 space-y-1">
                    <li>Buka menu Register kemudian<strong> Tab Data Pendaftaran</strong> akan disajikan menu form Entry.</li>
                    <li>Pilih Jenis <strong>[Internal atau External].</strong></li>
                    <li>Jika pilih <strong>Internal</strong> maka 🔘radiobutton aktif untuk opsi Karyawan atau Non Karyawan (keluarga) dan pemilihan daftar karyawan atau keluarga bisa dilakukan dengan 🔍Pencarian nama karyawan atau keluarga karyawan yang sudah terhubung ke <strong>HR System.</strong></li>
                    <li>Jika pilih <strong>External</strong> maka 🔘radiobutton nonaktif untuk opsi Karyawan atau Non Karyawan dan bisa langsung lakukan entry pendaftaran pasien secara manual.</li>
                    <li>Untuk Nomor RM sudah otomatis dari system, gunakan tombol ⚙ untuk <strong>Generate nomor RM</strong> jika ingin mengacak lagi nomor RM untuk pasien yang didaftarkan.</li>
                    <li>Isi data dengan benar sesuai dengan kebutuhan administrasi.</li>
                    <li>Klik <strong>Simpan</strong> untuk menyimpan data.</li>
                    <li>Data yang sudah di-entry akan muncul pada tabel sebelah kanan untuk bisa di explore kembali.</li>
                </ol>
                <div class="mt-5 text-right">
                    <button class="close-detail bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Tutup</button>
                </div>
            </div>
        </div>

        <!-- POPUP KHUSUS TAB Diagnosa -->
        <div id="popupTabDiagnosa"
            class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-[60] opacity-0 transition-opacity duration-300">
            <div class="bg-white w-[500px] rounded-lg shadow-lg p-6 transform scale-95 transition-transform duration-300">
                <h3 class="text-xl font-bold mb-3 text-gray-800">📝 Panduan Entry Diagnosa</h3>
                <ol class="list-decimal ml-5 text-gray-700 space-y-1">
                    <li>Buka menu Diagnosa kemudian<strong> Tab Anamnesa</strong> akan disajikan menu form Entry.</li>
                    <li>Pilih nama pasien yang berkunjung hari ini, maka akan ditampilkan informasi umum dari pasien.</li>
                    <li>Isi Keluhan Utama (Wajib) - Riwayat (Optional).</li>
                    <li>Kemudian pilih / tentukan jenis Diangnosa (kode ICD) yang tersedia pada dropdown.</li>
                    <li>Kemudian klik Simpan Diagnosa.</li>
                </ol>
                <div class="mt-5 text-right">
                    <button class="close-detail bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Tutup</button>
                </div>
            </div>
        </div>

        <!-- POPUP KHUSUS TAB Form Saldo Obat -->
        <div id="popupTabSaldoObat"
            class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-[60] opacity-0 transition-opacity duration-300">
            <div class="bg-white w-[500px] rounded-lg shadow-lg p-6 transform scale-95 transition-transform duration-300">
                <h3 class="text-xl font-bold mb-3 text-gray-800"> Panduan Form Saldo Obat</h3>
                <ol class="list-decimal ml-5 text-gray-700 space-y-1">
                    <li>Buka menu Stock Obat kemudian<strong> Tab Form Saldo Obat</strong> akan disajikan menu Saldo Obat.</li>
                    <li>Pilih bulan dan tahun pada dropdown yang tersedia.</li>
                    <li>Gunakan button syncronisasi saldo jika diperlukan, ini berfungsi untuk menyeimbangkan saldo jika stock awal, obat masuk, obat keluar serta saldo akhir belum balance.</li>
                    <li>Gunakan fitur Export Excel jika diperlukan.</li>
                    <li>Cari🔍 data obat yang diperlukan pada placeholder searching yang tersedia berdasarkan Kode atau Nama Obat.</li>
                </ol>
                <div class="mt-5 text-right">
                    <button class="close-detail bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Tutup</button>
                </div>
            </div>
        </div>

        <!-- POPUP KHUSUS TAB Form Transaksi Obat -->
        <div id="popupTabTransaksiObat"
            class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-[60] opacity-0 transition-opacity duration-300">
            <div class="bg-white w-[500px] rounded-lg shadow-lg p-6 transform scale-95 transition-transform duration-300">
                <h3 class="text-xl font-bold mb-3 text-gray-800"> Panduan Transaksi Obat</h3>
                <ol class="list-decimal ml-5 text-gray-700 space-y-1">
                    <li>LT : <strong> Tab Entry Obat Masuk</strong> akan disajikan menu Entry Transaksi.</li>
                    <li>Tentukan Tanggal Transaksi Obat masuk maka akan terbentuk nomor urut LT transaksi otomatis pada bulan yang dipilih.</li>
                    <li>Kemudian Cari nama obat sesuai SPB/Surat Jalan dan masukkan quantity,harga,tanggal expired, serta No. batch</li>
                    <li>Tambah ➕ Baris untuk input obat masuk berikutnya, Hapus ❌ baris yang tidak diinginkan.</li>
                    <li>Kemudian klik Simpan jika semua sudah terpenuhi, tekan Clear jika ingin membatalkan transaksi.</li>
                    <li>LK : <strong> Tab Entry Obat Keluar</strong> akan disajikan menu Entry Transaksi.</li>
                    <li>Tentukan Tanggal Transaksi Obat Keluar maka akan terbentuk nomor urut LK transaksi otomatis pada bulan yang dipilih.</li>
                    <li>Pilih nama Pasien yang sudah ada dalam daftar kunjungan.</li>
                    <li>Kemudian Cari nama obat sesuai Resep yang diberikan, kemudian masukkan quantity</li>
                    <li>Tambah ➕ Baris untuk input obat masuk berikutnya, Hapus ❌ baris yang tidak diinginkan.</li>
                    <li>Kemudian klik Simpan jika semua sudah terpenuhi, tekan Clear jika ingin membatalkan transaksi.</li>
                    <li>Lihat data transaksi LT dan LK pada <strong>Tab Explore Data</strong> pilih bulan dan tahun kemudian tampilkan LT atau LK, gunakan fitur edit jika diperlukan</li>
                </ol>
                <div class="mt-5 text-right">
                    <button class="close-detail bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Tutup</button>
                </div>
            </div>
        </div>

        <!-- POPUP KHUSUS TAB Form Saldo Obat -->
        <div id="popupTabPPObat"
            class="fixed inset-0 hidden bg-black/40 flex items-center justify-center z-[60] opacity-0 transition-opacity duration-300">
            <div class="bg-white w-[500px] rounded-lg shadow-lg p-6 transform scale-95 transition-transform duration-300">
                <h3 class="text-xl font-bold mb-3 text-gray-800"> Panduan Form PP Obat</h3>
                <ol class="list-decimal ml-5 text-gray-700 space-y-1">
                    <li>Pilih <strong>Tab Informasi Stock Minimal </strong> terlebih dahulu untuk mengetahui stock akhir atau status PP, jika stock akhir sama dengan stock minimal atau stock akhir kurang dari stock minimal maka akan otomatis masuk dalam daftar PP.</li>
                    <li>Kemudian pilih Golongan jenis obat yang akan di PP-kan.</li>
                    <li>Lalu klik Transfer ke Format PP.</li>
                    <li>Pada <strong>Tab Format PP</strong>, Isikan jumlah obat yang akan di PP-kan jika sudah terpenuhi maka klik Simpan.</li>
                    <li>Masuk ke <strong>Tab Daftar PP</strong> untuk melihat detail PP Obat dan mencetak atau mengexport detail PP untuk dipergunakan sebagaimana-mestinya.</li>
                </ol>
                <div class="mt-5 text-right">
                    <button class="close-detail bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded">Tutup</button>
                </div>
            </div>
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

        {{-- Script halaman khusus (letakkan di bawah supaya DOM sudah ada) --}}
<script>
    const panduanModal = document.getElementById('panduanModal');
    const btnPanduan = document.getElementById('btnBukaPanduan');
    const btnTutup = document.getElementById('btnTutupPanduan');
    const btnPdf = document.getElementById('btnPdfPanduan');
   
    // Buka popup utama
    btnPanduan.addEventListener('click', () => {
        panduanModal.classList.remove('hidden');
        setTimeout(() => {
            panduanModal.classList.remove('opacity-0', 'scale-95');
        }, 10);
    });

    btnPdf.addEventListener('click', function () {
        window.open('/panduan/panduan_myclinis.pdf', '_blank');
    });

    // Tutup popup utama
    btnTutup.addEventListener('click', () => {
        panduanModal.classList.add('opacity-0');
        setTimeout(() => panduanModal.classList.add('hidden'), 300);
    });

    // Buka popup detail (misalnya Master Obat)
    document.querySelectorAll('.panduan-item').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.target;
            if (target) {
                const modal = document.getElementById(target);
                modal.classList.remove('hidden');
                setTimeout(() => modal.classList.remove('opacity-0', 'scale-95'), 10);
            }
        });
    });

    // Tutup popup detail tanpa menutup popup utama
    document.querySelectorAll('.close-detail').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.fixed');
            modal.classList.add('opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 300);
        });
    });
</script>

    {{-- Kalau halaman butuh skrip tambahan, child view bisa menggunakan @push / @stack --}}
    @stack('scripts')
</body>
</html>

