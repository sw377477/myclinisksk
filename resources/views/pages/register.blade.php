@extends('layouts.app')

@section('content')
<!-- Wrapper utama -->
<div class="h-[680px] flex flex-col p-5 bg-white rounded-2xl shadow-xl">
    <!--<h1 class="text-3xl font-bold text-center mb-6">🩺 MyClinis</h1>-->

    @if(session('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs Wrapper -->
    <div x-data="{ tab: 'kunjungan' }" class="flex-1 flex flex-col min-h-[630px]"> 
        <ul class="flex justify-center space-x-4 text-lg font-semibold mb-6">
            <li>
                <button :class="tab === 'kunjungan' ? 'bg-gradient-to-r from-blue-400 to-blue-600 text-white shadow-lg' : 'bg-blue-100 text-gray-700'"
                        class="px-5 py-2 rounded-lg transition-all duration-300" @click.prevent="tab = 'kunjungan'">
                    📝 Data Kunjungan
                </button>
            </li>
            <li>
                <button :class="tab === 'pendaftaran' ? 'bg-gradient-to-r from-blue-400 to-blue-600 text-white shadow-lg' : 'bg-blue-100 text-gray-700'"
                        class="px-5 py-2 rounded-lg transition-all duration-300" @click.prevent="tab = 'pendaftaran'">
                    📋 Data Pendaftaran
                </button>
            </li>
            <li>
                <button :class="tab === 'explore' ? 'bg-gradient-to-r from-blue-400 to-blue-600 text-white shadow-lg' : 'bg-blue-100 text-gray-700'"
                        class="px-5 py-2 rounded-lg transition-all duration-300" @click.prevent="tab = 'explore'">
                    🔎 Explore Data
                </button>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="relative flex-1 flex flex-col overflow-hidden">

            <!-- Data Kunjungan -->
            <div x-show="tab === 'kunjungan'"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                 class="flex flex-col flex-1 p-4 bg-blue-50 rounded-lg shadow-inner overflow-hidden">

                <!-- Tambah pembatas width di sini -->
                 <div class="w-full">
                 <h2 class="text-m font-bold mb-4">📝 Form Data Kunjungan</h2>

                <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- MEMBER (Select2) -->
                        <div class="flex items-center space-x-2">
                            <!--<label class="block font-semibold mb-1">Member</label>-->
                            <!-- NOTE: option text = nama saja; no_rm disimpan di data-no_rm -->
                            <select name="id_member" id="id_member" class="w-600 p-1 border rounded-md" required>
                                <option value="">-- Pilih Member --</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id_member }}" data-no_rm="{{ $member->no_rm }}">
                                        {{ $member->nm_member }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- No RM -->
                        <div class="flex items-center space-x-2">
                            <label for="no_rm" class="w-32 font-semibold">No RM</label>
                            <input type="text" id="no_rm" name="no_rm"
                                class="w-80 p-1 border rounded-md bg-gray-100" readonly>
                        </div>

                        <!-- No Kunjungan -->
                        <div class="flex items-center space-x-2">
                            <label for="no_kunjungan" class="w-32 font-semibold">ID Kunjungan</label>
                            <input type="text" id="no_kunjungan" name="no_kunjungan"
                                class="w-80 p-1 border rounded-md bg-gray-100" readonly>
                        </div>

                        <!-- Jenis Kunjungan -->
                        <div class="flex items-center space-x-2">
                            <label for="jenis_kunjungan" class="w-32 font-semibold">Jenis Kunjungan</label>
                            <select name="jenis_kunjungan" id="id_kunjungan" class="w-80 p-1 border rounded-md" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisKunjungan as $jenis)
                                    <option value="{{ $jenis->id_kunjungan }}">{{ $jenis->jenis_kunjungan }}</option>
                                @endforeach
                            </select>
                            </div>

                        <!-- Poli -->
                        <div class="flex items-center space-x-2">
                            <label for="id_poli" class="w-32 font-semibold">Poli</label>
                            <select name="id_poli" id="id_poli" class="w-80 p-1 border rounded-md" required>
                                <option value="">-- Pilih Poli --</option>
                                @foreach($polis as $poli)
                                    <option value="{{ $poli->id_poli }}">{{ $poli->poli }}</option>
                                @endforeach
                            </select>                            
                        </div>

                        <!-- Payment -->
                        <div class="flex items-center space-x-2">
                            <label for="id_pay" class="w-32 font-semibold">Payment</label>
                            <select name="id_pay" id="id_pay" class="w-80 p-1 border rounded-md" required>
                                <option value="">-- Pilih Payment --</option>
                                @foreach($payments as $pay)
                                    <option value="{{ $pay->id_pay }}">{{ $pay->payment }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="lex justify-end mt-4 px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700">
                            💾 Simpan
                        </button>
                        </div>

                         <!-- Kolom 3: Tombol Simpan di kanan -->
                    <div class="flex justify-end">
                        
                    </div>

                    </div>
                        
                </form>

                <!-- Tambahkan di bawah form Data Kunjungan -->
                    <div class="mt-8 max-w-full mx-auto border rounded-lg">
                        <h2 class="text-m font-bold mb-4">📋 Data Kunjungan Hari Ini</h2>
                        <div class="overflow-x-auto" style="max-height:400px;">
                            <table  class="min-w-full w-full table-auto border border-gray-300 rounded-lg">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-4 py-1 border sticky top-0 bg-gray-200 z-10 text-sm text-center">No</th> <!-- Kolom nomor urut -->
                                        <th class="px-4 py-1 border sticky top-0 bg-gray-200 z-10">ID Kunjungan</th>
                                        <th class="px-4 py-1 border sticky top-0 bg-gray-200 z-10">Tanggal</th>
                                        <th class="px-4 py-1 border sticky top-0 bg-gray-200 z-10">Jam</th>
                                        <th class="px-4 py-1 border sticky top-0 bg-gray-200 z-10">Member</th>
                                        <th class="px-4 py-1 border sticky top-0 bg-gray-200 z-10">Jenis Kunjungan</th>
                                        <th class="px-4 py-1 border sticky top-0 bg-gray-200 z-10">Poli</th>
                                        <th class="px-4 py-1 border sticky top-0 bg-gray-200 z-10">Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entries as $entry)
                                        <tr class="{{ $loop->index % 2 == 0 ? 'bg-white' : 'bg-gray-100' }} hover:bg-blue-100">
                                            <td class="px-4 py-1 border text-sm text-center">{{ $loop->iteration }}</td> <!-- Nomor urut -->
                                            <td class="px-4 py-1 border">{{ $entry->no_kunjungan }}</td>
                                            <td class="px-4 py-1 border">{{ \Carbon\Carbon::parse($entry->tgl_kunjungan)->format('d-m-Y') }}</td>
                                            <td class="px-4 py-1 border">{{ $entry->jam_kunjungan }}</td>
                                            <td class="px-4 py-1 border">{{ $entry->nm_member }}</td>
                                            <td class="px-4 py-1 border">{{ $entry->jenis_kunjungan }}</td>
                                            <td class="px-4 py-1 border">{{ $entry->poli }}</td>
                                            <td class="px-4 py-1 border">{{ $entry->payment }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-1 border text-center text-gray-500">Belum ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

            </div>
        </div>

            <!-- Data Pendaftaran -->
            <div x-show="tab === 'pendaftaran'"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                 class="p-4 bg-green-50 rounded-lg shadow-inner absolute inset-0">
                <!--<h2 class="text-xl font-bold mb-2">📋 Data Pendaftaran</h2>-->
                <div class="flex space-x-4">
    <!-- Form Entry -->
    <div class="w-1/2 bg-gray-50 p-4 rounded-lg shadow space-y-2">
        <h2 class="font-bold text-m mb-2">Entry Pendaftaran</h2>
        
        <div class="flex items-center space-x-4">
            <select name="type_member" class="w-60 border rounded p-1">
                <option value="INTERNAL">INTERNAL</option>
                <option value="EXTERNAL">EXTERNAL</option>
            </select>

            <div class="flex items-center space-x-2">
            <!--<span>Nomor RM:</span>-->
            <label class="w-30">Nomor RM :</label>
            <input type="text" value="KSK.RM.20250919152528" class="border p-1 w-80 text-center" readonly>
            </div>

            
        </div>
        <div class="flex items-center space-x-2">
                <label><input type="radio" name="karyawan" value="1" checked> Karyawan</label>
                <label><input type="radio" name="karyawan" value="0"> Non Karyawan</label>
            </div>

        <h2><div class="border-t mt-4 pt-2 text-blue-600 text-sm italic">
            
            </div>
        </h2>

        <!-- Nama lengkap + search -->
    
        <div class="flex items-center space-x-1">
            <label class="w-32">Nama Lengkap :</label>
            <input type="text" class="border p-1 flex-1 h-7 rounded" placeholder="ctrl + F">
            <button class="bg-gray-200 px-2 rounded">🔍</button>
        </div>

        <!-- Contoh beberapa field lain -->
    <div class="space-y-0.5">
        <div class="grid grid-cols-2 gap-1">
            <div class="flex items-center space-x-2">
                <label class="w-32">No SatuSehat :</label>
                <input type="text" class="border flex-1 h-7 px-1 rounded">
            </div> 
            <div class="flex items-center space-x-2">
                <label class="w-32">ID Member :</label>
                <input type="text" class="border flex-1 h-7 px-1 rounded" readonly value="21">
            </div>
            <div class="flex items-center space-x-2">
                <label class="w-32">Nomor BPJS :</label>
                <input type="text" class="border flex-1 h-7 px-1 rounded">
            </div>
            <div class="flex items-center space-x-2">
                <label class="w-32">Nik Karyawan :</label>
                <input type="text" class="border flex-1 h-7 px-1 rounded" readonly value="21">
            </div>
            <div class="flex items-center space-x-2">
                <label class="w-32">No KTP/SIM :</label>
                <input type="text" class="border flex-1 h-7 px-1 rounded">
            </div>
            <div class="flex items-center space-x-2">
                <label class="w-32">Status :</label>
                <select name="status" class="border h-7 p-1 w-30 rounded">
                <option value="Kawin">Kawin</option>
                <option value="Belum Kawin">Belum Kawin</option>
                <option value="Cerai">Cerai</option>
            </select>
            </div>
            
            
            <div class="flex items-center space-x-2">
                <label class="w-32">Tgl Lahir :</label>
                <input type="date" class="border h-7 p-1 w-30 rounded" value="{{ now()->format('Y-m-d') }}">
                <input type="text" class="border flex-1 h-7 p-1 w-10 rounded" value="98" readonly>
            </div>
            <div class="flex items-center space-x-2">
                <label class="w-32">Gol Darah :</label>
                <select name="goldarah" class="border h-7 p-1 w-20 rounded">
                <option value="O">O</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
            </select>
            </div>
            <div class="flex items-center space-x-2">
                <label class="w-32">Tempat Lahir :</label>
                <input type="text" class="border flex-1 h-7 px-1 rounded">
            </div>            
            <div class="flex items-center space-x-2">
                <label class="w-32">No. Telp :</label>
                <input type="text" class="border flex-1 h-7 px-1 rounded">
            </div>           
            
            <div class="flex items-center space-x-2">
                <label class="w-32">Gender :</label>
                <select name="gender" class="border h-7 p-1 w-30 rounded">                    
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <label class="w-32">No. KK :</label>
                <input type="text" class="border flex-1 h-7 px-1 rounded">
            </div>

            <!-- Tambahkan field lain sesuai kebutuhan -->
        </div>
</div>

        <!-- Optional Contact -->
        <div class="border-t mt-4 pt-2 text-blue-600 text-sm italic">
            Optional
        </div>
        <div class="space-y-2">
            <input type="text" placeholder="Nama bisa dihubungi" class="border p-1 w-full">
            <div class="flex space-x-2">
                <input type="text" placeholder="Hubungan Keluarga" class="border p-1 flex-1">
                <input type="text" placeholder="No. Telp / HP" class="border p-1 flex-1">
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2 mt-4">
            <button class="bg-orange-500 text-white px-4 py-1 rounded shadow">EDIT</button>
            <button class="bg-orange-500 text-white px-4 py-1 rounded shadow">SIMPAN</button>
            <button class="bg-orange-500 text-white px-4 py-1 rounded shadow">CLEAR</button>
        </div>
    </div>

    <!-- Data Pendaftaran -->
    <div class="w-1/2 bg-gray-50 p-4 rounded-lg shadow flex flex-col">
        <div class="flex justify-between items-center mb-2">
            <span class="font-bold">Data Pendaftaran</span>
            <select class="border rounded p-1">
                <option value="today">Hari Ini</option>
                <option value="all">Semua Data</option>
            </select>
        </div>

        <div class="overflow-x-auto flex-1">
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-200 sticky top-0">
                    <tr>
                        <th class="px-2 py-1 border">NO</th>
                        <th class="px-2 py-1 border">NAMA</th>
                        <th class="px-2 py-1 border">KTP</th>
                        <th class="px-2 py-1 border">UMUR</th>
                        <th class="px-2 py-1 border">LP</th>
                        <th class="px-2 py-1 border">NOMOR</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan di-loop di sini -->
                    <tr class="bg-white hover:bg-gray-100">
                        <td class="px-2 py-1 border text-center">1</td>
                        <td class="px-2 py-1 border">Nama Contoh</td>
                        <td class="px-2 py-1 border">1234567890</td>
                        <td class="px-2 py-1 border">25</td>
                        <td class="px-2 py-1 border">L</td>
                        <td class="px-2 py-1 border">KSK.RM.20250919152528</td>
                    </tr>
                    <!-- Tambahkan loop entries dari controller -->
                </tbody>
            </table>
        </div>
    </div>
</div>

            </div>

            <!-- Explore Data -->
<div x-show="tab === 'explore'"
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0 transform -translate-y-4"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform -translate-y-4"
     class="p-4 bg-yellow-50 rounded-lg shadow-inner absolute inset-0"
     x-data="{ exploreTab: 'kunjungan' }">

    <!-- Sub Tab Navigation -->
    <div class="flex space-x-4 border-b border-gray-300 mb-4">
        <button 
            class="px-4 py-2 text-m font-semibold"
            :class="exploreTab === 'kunjungan' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600'"
            @click="exploreTab = 'kunjungan'">
            📊 Explore Data Kunjungan
        </button>
        <button 
            class="px-4 py-2 text-m font-semibold"
            :class="exploreTab === 'pendaftaran' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600'"
            @click="exploreTab = 'pendaftaran'">
            📝 Explore Data Pendaftaran
        </button>
    </div>

    <!-- Konten Sub Tab -->
    <!-- Tab Kunjungan -->
<div x-show="exploreTab === 'kunjungan'" x-transition
     x-data="exploreKunjungan()" 
     x-init="fetchData()" 
     class="space-y-4">

    <!-- Filter Bulan & Tahun -->
    <div class="flex space-x-4">
        <div>
            <label class="w-32">Bulan :</label>
            <select x-model="bulan" @change="fetchData()" class="border rounded-lg px-3 py-2">
                <template x-for="(nama, index) in bulanOptions" :key="index">
                    <option :value="index+1" x-text="(index+1) + ' - ' + nama"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="w-32">Tahun :</label>
            <select x-model="tahun" @change="fetchData()" class="border rounded-lg px-3 py-2">
                <template x-for="t in tahunOptions" :key="t">
                    <option :value="t" x-text="t"></option>
                </template>
            </select>
            
        </div>
        <div class="flex space-x-4">
        <input 
      type="text" 
      placeholder="🔍 Cari pasien..." 
      class="w-80 py-2 border rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-400"
      x-model="search"
        >
        <span x-text="data.length + ' data ditemukan'" class="text-sm text-gray-600 font-medium"></span>
        </div>
        </div>

        <!-- Loader -->
        <div x-show="loading" class="text-blue-500">🔄 Memuat data...</div>
        <div class="flex items-center justify-between mb-3">
        </div>

        <!-- Tabel Data -->
        <div class="overflow-y-auto max-h-[552px]" x-show="!loading">
            <table class="min-w-full border border-gray-300-collapse rounded-lg">
                <thead class="bg-gray-200 sticky top-0 z-10">
                    <tr>
                        <th class="px-2 py-1 border">Pasien</th>
                        <th class="px-2 py-1 border">No RM</th>
                        <th class="px-2 py-1 border">Tanggal</th>
                        <th class="px-2 py-1 border">Jam</th>
                        <th class="px-2 py-1 border">Nomor</th>
                        <th class="px-2 py-1 border">Kunjungan</th>
                        <th class="px-2 py-1 border">Poli</th>
                        <th class="px-2 py-1 border">Payment</th>
                        <th class="px-2 py-1 border">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in filteredData" :key="index">
                        <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                            <td class="px-2 py-1 border" x-text="row.pasien"></td>
                            <td class="px-2 py-1 border" x-text="row.no_rm"></td>
                            <td class="px-2 py-1 border" x-text="row.tanggal"></td>
                            <td class="px-2 py-1 border" x-text="row.jam"></td>
                            <td class="px-2 py-1 border" x-text="row.nomor"></td>
                            <td class="px-2 py-1 border" x-text="row.kunjungan"></td>
                            <td class="px-2 py-1 border" x-text="row.poli"></td>
                            <td class="px-2 py-1 border" x-text="row.payment"></td>
                            <td class="px-2 py-1 border text-center">
                            <input type="checkbox" class="w-6 h-5 cursor-pointer"
                            :checked="row.status_lk === 'Done'"
                            @change="toggleStatus(row)">
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>


    <!-- ===================== TAB PENDAFTARAN ===================== -->
<div x-show="exploreTab === 'pendaftaran'" x-transition>
    <p>📝 Data Pendaftaran akan ditampilkan di sini.</p>

    <div x-show="exploreTab === 'pendaftaran'"
         x-data="exploreData('pendaftaran')"
         x-init="fetchData()"
         class="space-y-4">

        <!-- Combo / Lookup Jenis -->
        <div class="flex items-center space-x-2">
            <label class="font-medium">Jenis:</label>
            <select x-model="jenis" @change="fetchHeader()"
                    class="border rounded px-2 py-1">
                <option value="INTERNAL">INTERNAL</option>
                <option value="EXTERNAL">EXTERNAL</option>
            </select>
        </div>

        <!-- Master-Detail Layout -->
        <div class="flex space-x-4">
            <!-- Master: Tabel Kiri -->
            <div class="w-3/5 overflow-y-auto max-h-[530px] border rounded-lg shadow-sm">
                <table class="min-w-full border border-gray-300 table-auto">
                    <thead class="bg-gray-200 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-1 border text-left">Nama</th>
                            <th class="px-4 py-1 border text-left">No RM</th>
                            <th class="px-4 py-1 border text-left">KTP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in headerData" :key="index">
                            <tr :class="selectedNoRM === row.nomor ? 'bg-blue-100' : (index % 2 === 0 ? 'bg-white' : 'bg-gray-50')"
                                @click="selectRow(row.nomor)" class="cursor-pointer hover:bg-blue-50">
                                <td class="px-2 py-1 border whitespace-nowrap" x-text="row.nama"></td>
                                <td class="px-2 py-1 border whitespace-nowrap" x-text="row.nomor"></td>
                                <td class="px-2 py-1 border whitespace-nowrap" x-text="row.ktp"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Detail: Tabel Kanan -->
            <div class="w-2/5 overflow-y-auto max-h-[530px] border rounded-lg shadow-sm">
                
                <template x-if="loadingDetail">
                    <div class="p-4 text-blue-500">🔄 Memuat detail...</div>
                </template>

                <template x-if="!loadingDetail && Object.keys(detailData).length > 0">
                    <table class="min-w-full border border-gray-300 table-auto text-sm">
                        <tbody>
                            <template x-for="(value, key) in detailData" :key="key">
                                <tr class="border-b">
                                    <td class="px-1 py-0.5 font-medium bg-gray-100 w-1/3" x-text="formatKey(key)"></td>
                                    <td class="px-1 py-0.5" x-text="value"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </template>

                <template x-if="!loadingDetail && Object.keys(detailData).length === 0" >
                    <div class="p-4 text-gray-500">Pilih member untuk melihat detail</div>
                </template>
            </div>
        </div>
    </div>
</div>


</div>

@endsection

@section('scripts')

<script>
function exploreKunjungan() {
    return {
        bulan: new Date().getMonth() + 1,
        tahun: new Date().getFullYear(),
        data: [],
        search: '',
        loading: false,
        bulanOptions: [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ],
        tahunOptions: [
            new Date().getFullYear(), 
            new Date().getFullYear() + 1
        ],

        get filteredData() {
            if (!this.search) return this.data;
            const keyword = this.search.toLowerCase();
            return this.data.filter(d =>
                d.pasien?.toLowerCase().includes(keyword) ||
                d.no_rm?.toLowerCase().includes(keyword) ||
                d.kunjungan?.toLowerCase().includes(keyword) ||
                d.poli?.toLowerCase().includes(keyword) ||
                d.payment?.toLowerCase().includes(keyword)
            );
        },

        fetchData() {
            this.loading = true;
            fetch(`/explore-data?type=kunjungan&bulan=${this.bulan}&tahun=${this.tahun}`)
                .then(res => res.json())
                .then(json => {
                    if (Array.isArray(json)) {
                        this.data = json;
                    } else if (json && Array.isArray(json.data)) {
                        this.data = json.data;
                    } else {
                        this.data = [];
                    }
                    this.loading = false;
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    this.data = [];
                    this.loading = false;
                });
        },

        toggleStatus(row) {
            // ubah langsung di tabel (optimistic update)
            row.status_lk = row.status_lk === "Done" ? " " : "Done";
            //row.status_lk = event.target.checked ? "Done" : " ";
            
            fetch(`/update-status/${row.nomor}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ status: row.status_lk })
            })
            .then(res => res.json())
            .then(resp => {
                console.log("Status updated:", resp);
            })
            .catch(err => {
                console.error("Update error:", err);
                // kalau gagal, balikin lagi
                row.status = row.status === "Done" ? " " : "Done";
            });
        }
    }
}
</script>

<script>
function exploreData(tab) {
    return {
        headerData: [],
        detailData: {},
        jenis: 'INTERNAL',
        selectedNoRM: null,
        loadingDetail: false,

        fetchHeader() {
            fetch(`/api/pendaftaran-header?jenis=${this.jenis}`)
                .then(res => res.json())
                .then(data => {
                    // trim semua no_rm dari header agar klik bisa match
                    this.headerData = data.map(row => ({
                        ...row,
                        nomor: row.nomor.trim()
                    }));
                    this.detailData = {};
                    this.selectedNoRM = null;
                });
        },

        selectRow(no_rm) {
            this.selectedNoRM = no_rm.trim();
            this.loadingDetail = true;

            fetch(`/api/pendaftaran-detail?no_rm=${this.selectedNoRM}`)
                .then(res => res.json())
                .then(data => {
                    this.detailData = data || {};
                    this.loadingDetail = false;
                });
        },

        fetchData() {
            this.fetchHeader();
        },

        formatKey(key) {
            return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }
    }
}
</script>

<!-- jQuery (Select2 requires jQuery) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Alpine (untuk tab UI) -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Select2 on #id_member
    $('#id_member').select2({
        placeholder: "🔍 Cari member...",
        allowClear: true,
        width: '90%',
        // Customize how each option appears in the dropdown (open state)
        templateResult: function (data) {
            if (!data.id) return data.text; // placeholder
            // data.element is the <option> DOM element
            var noRm = $(data.element).data('no_rm') || '';
            // Return HTML for dropdown list: name left, no_rm right
            var $row = $(
                '<div style="display:flex;justify-content:space-between;">' +
                    '<span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + $('<div>').text(data.text).html() + '</span>' +
                    '<span style="color:gray; margin-left:8px;">' + $('<div>').text(noRm).html() + '</span>' +
                '</div>'
            );
            return $row;
        },
        // Customize what's shown in the closed select (after chosen)
        templateSelection: function (data) {
            if (!data.id) return data.text;
            // only show the name (data.text is the option inner text, i.e. the name)
            return data.text;
        },
        escapeMarkup: function(markup) { return markup; } // allow HTML from templateResult
    });

    // When a member is selected, fill no_rm and no_kunjungan
    $('#id_member').on('select2:select', function (e) {
        var data = e.params.data; // select2 data
        var noRm = $(data.element).data('no_rm') || '';
        var idMember = data.id;

        // set textbox no_rm
        $('#no_rm').val(noRm);

        // generate no_kunjungan = id_member.yymmdd
        var today = new Date();
        var yy = today.getFullYear().toString().slice(-2);
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var dd = String(today.getDate()).padStart(2, '0');
        var yyMMdd = yy + mm + dd;
        $('#no_kunjungan').val(idMember + '.' + yyMMdd);
    });

    // Clear fields when selection cleared
    $('#id_member').on('select2:clear', function () {
        $('#no_rm').val('');
        $('#no_kunjungan').val('');
    });
});
</script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#kunjunganTable').DataTable({
        responsive: true,
        autoWidth: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        columnDefs: [
            { width: '15%', targets: 0 },
            { width: '15%', targets: 1 },
            { width: '20%', targets: 3 },
            { width: '15%', targets: 4 },
            { width: '15%', targets: 5 },
            { width: '20%', targets: 6 },
        ],
        language: {
            search: ""  // kosongkan label default
        }
    });

    // Tambahkan placeholder
    $('#kunjunganTable_filter input').attr('placeholder', '🔍 Cari Data Kunjungan...');

    // Panjangkan kotak search
    $('#kunjunganTable_filter input').css({
        'width': '470px',       // ganti sesuai kebutuhan
        'display': 'inline-block',
        'margin-left': '0'      // agar tidak terlalu ke kanan
    });
});

</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

@endsection
