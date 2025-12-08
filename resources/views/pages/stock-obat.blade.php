@extends('layouts.app')
@section('style')
<style>
.select2-container {
  width: 100% !important;
}
.select2-dropdown {
  z-index: 99999 !important;
}
</style>
@endsection

@section('content')
<div class="h-[680px] flex flex-col p-5 bg-white rounded-2xl shadow-xl">
   <h1 class="text-2xl font-bold mb-0.5">📦 Stock Obat</h1>
   <div x-data="{ tab: 'saldo', transaksiTab: 'masuk', exploreTab: 'datamasuk', ppTab: 'minimal' }" class="flex-1 flex flex-col">
      <!-- Tabs Utama -->
      <ul class="flex justify-center space-x-4 text-lg font-semibold mb-6">
         <li>
            <button @click="tab = 'saldo'"
               :class="tab === 'saldo' ? 'bg-gradient-to-r from-blue-400 to-blue-600 text-white shadow-lg' : 'bg-blue-100 text-gray-700'"
               class="px-5 py-2 rounded-lg transition-all duration-300">
            💊 Form Saldo Obat
            </button>
         </li>
         <li>
            <button @click="tab = 'transaksi'"
               :class="tab === 'transaksi' ? 'bg-gradient-to-r from-green-400 to-green-600 text-white shadow-lg' : 'bg-green-100 text-gray-700'"
               class="px-5 py-2 rounded-lg transition-all duration-300">
            📑 Transaksi Obat
            </button>
         </li>
         <li>
            <button @click="tab = 'ppobat'"
               :class="tab === 'ppobat' ? 'bg-gradient-to-r from-purple-400 to-purple-600 text-white shadow-lg' : 'bg-purple-100 text-gray-700'"
               class="px-5 py-2 rounded-lg transition-all duration-300">
            📋 PP Obat
            </button>
         </li>
      </ul>
      <!-- Konten Tab -->
      <div class="relative flex-1 flex flex-col overflow-hidden">
         <!-- 1. SALDO OBAT -->
         <div x-show="tab === 'saldo'" x-transition  
            class="absolute inset-0 p-4 bg-blue-50 rounded-lg shadow-inner flex flex-col">
            <!-- Filter & Button -->
            <div class="flex flex-wrap items-center space-x-4 mb-6">
               <!-- Bulan -->
               <div>
                  <select id="bulan" name="bulan" class="w-full border rounded-lg px-3 py-2">
                     <option value="">-- Pilih Bulan --</option>
                     @php
                     $bulanSekarang = date('n');
                     $bulanList = [
                     1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                     5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                     9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                     ];
                     @endphp
                     @foreach($bulanList as $num => $nama)
                     <option value="{{ $num }}" {{ $bulanSekarang == $num ? 'selected' : '' }}>
                     {{ $nama }}
                     </option>
                     @endforeach
                  </select>
               </div>
               <!-- Tahun -->
               <div>
                  <select id="tahun" name="tahun" class="w-full border rounded-lg px-3 py-2">
                     <option value="">-- Pilih Tahun --</option>
                     @php
                     $tahunSekarang = date('Y');
                     @endphp
                     @for($i = $tahunSekarang; $i <= $tahunSekarang + 1; $i++)
                     <option value="{{ $i }}" {{ $tahunSekarang == $i ? 'selected' : '' }}>
                     {{ $i }}
                     </option>
                     @endfor
                  </select>
               </div>
               <!-- Tombol -->
               <div class="flex items-end space-x-3 mt-6 sm:mt-0">
                  <button id="btnSync" type="button"
                     class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition-all">
                  🔄 Sync Saldo
                  </button>
                  <button id="btnExport" type="button"
                     class="px-4 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition-all">
                  📊 Export Excel
                  </button>
               </div>
               <!-- Search -->
               <input type="text" id="searchInput" placeholder="🔍 Cari obat / kode..."
                  class="border rounded-lg px-3 py-2 w-1/3 focus:ring focus:ring-blue-500">
            </div>
            <!-- Tabel -->
            <div class="flex-1 overflow-auto rounded-lg shadow-md bg-white">
               <table id="saldoTable" class="min-w-full text-sm text-left border-collapse">
                  <thead class="bg-blue-100 sticky top-0 z-10">
                     <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Kode Obat</th>
                        <th class="px-4 py-2 border">Nama Obat</th>
                        <th class="px-4 py-2 border">Satuan</th>
                        <th class="px-4 py-2 border">Qty Awal</th>
                        <th class="px-4 py-2 border">Nilai Awal</th>
                        <th class="px-4 py-2 border">Hpp Awal</th>
                        <th class="px-4 py-2 border">Qty Masuk</th>
                        <th class="px-4 py-2 border">Nilai Masuk</th>
                        <th class="px-4 py-2 border">Qty Keluar</th>
                        <th class="px-4 py-2 border">Nilai Keluar</th>
                        <th class="px-4 py-2 border">Qty Akhir</th>
                        <th class="px-4 py-2 border">Nilai Akhir</th>
                        <th class="px-4 py-2 border">Hpp Akhir</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td colspan="14" class="text-center py-4 text-gray-500">Loading data...</td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
         <!-- 2. TRANSAKSI OBAT -->
         <div x-show="tab === 'transaksi'" x-transition
            class="absolute inset-0 p-4 bg-green-50 rounded-lg shadow-inner flex flex-col">
            <!-- Sub Tab -->
            <div class="flex space-x-4 mb-4">
               <button @click="transaksiTab = 'masuk'"
                  :class="transaksiTab === 'masuk' ? 'bg-green-600 text-white shadow-md' : 'bg-green-100 text-gray-700'"
                  class="px-4 py-2 rounded-lg transition-all duration-300">
               ➕ Entry Obat Masuk
               </button>
               <button @click="transaksiTab = 'keluar'"
                  :class="transaksiTab === 'keluar' ? 'bg-green-600 text-white shadow-md' : 'bg-green-100 text-gray-700'"
                  class="px-4 py-2 rounded-lg transition-all duration-300">
               ➖ Entry Obat Keluar
               </button>
               <button @click="transaksiTab = 'explore'"
                  :class="transaksiTab === 'explore' ? 'bg-green-600 text-white shadow-md' : 'bg-green-100 text-gray-700'"
                  class="px-4 py-2 rounded-lg transition-all duration-300">
               🔎 Explore Data
               </button>
            </div>
            <!-- Konten Sub Tab -->
            <div class="relative flex-1 overflow-hidden">
               <!-- Entry Obat Masuk -->
               <div x-show="transaksiTab === 'masuk'" x-transition
                  class="absolute inset-0 p-4 bg-white rounded-lg shadow-inner overflow-auto">
                  <h3 class="text-lg font-bold mb-4">Entry Obat Masuk</h3>
                  <!-- FORM dimulai di sini -->
                  <form method="POST" action="{{ route('stock-obat.store') }}">
                     @csrf
                     <input type="hidden" name="lokasi" value="{{ session('idpay') }}">
                     <!-- Form atas -->
                     <div class="flex flex-wrap items-center gap-4 mb-4">
                        <div>
                           <label for="tgl_masuk">Tanggal Masuk :</label>
                           <input type="date" id="tgl_masuk" name="tgl_masuk"
                              class="border rounded-lg px-3 py-2 focus:ring focus:ring-blue-400"
                              value="{{ $tanggal ?? '' }}">
                        </div>
                        <div>
                           <label for="no_transaksi">Nomor Transaksi :</label>
                           <input type="text" id="no_transaksi" name="no_transaksi"
                              class="border rounded-lg px-3 py-2 w-80 focus:ring focus:ring-blue-400"
                              value="{{ $noTransaksi ?? '' }}" readonly>
                        </div>
                        <div class="flex items-end gap-2">
                           <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">
                           💾 Simpan
                           </button>
                           <button type="button" id="clearRows"
                              class="px-4 py-2 bg-green-400 text-white rounded-lg shadow hover:bg-yellow-600">
                           🧹 Clear
                           </button>
                        </div>
                     </div>
                     <!-- Tabel input -->
                     <div class="overflow-x-auto">
                        <table id="obatTable" class="min-w-full border border-gray-300 text-sm">
                           <thead class="bg-gray-100">
                              <tr>
                                 <th class="border px-3 py-2 text-center w-72">Obat</th>
                                 <th class="border px-3 py-2 text-center w-24">Satuan</th>
                                 <th class="border px-3 py-2 text-center w-20">Qty</th>
                                 <th class="border px-3 py-2 text-center w-32">Harga</th>
                                 <th class="border px-3 py-2 text-center w-32">Jumlah</th>
                                 <th class="border px-3 py-2 text-center w-36">Expired</th>
                                 <th class="border px-3 py-2 text-center w-40">No Batch</th>
                                 <th class="border px-3 py-2 text-center w-10">Aksi</th>
                              </tr>
                           </thead>
                           <tbody id="obatBody">
                              <tr>
                                 <td class="border px-2 py-1">
                                    <select class="obat-lookup border rounded px-2 py-1 w-full">
                                       <option value="">-- Pilih Obat --</option>
                                       @foreach($obatList ?? [] as $obat)
                                       <option value="{{ $obat->kode_obat }}" data-satuan="{{ $obat->satuan }}">
                                          {{ $obat->nama_obat }}
                                       </option>
                                       @endforeach
                                    </select>
                                    <input type="hidden" name="kode_obat[]" class="kode-obat-hidden">
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="text" name="satuan[]" class="satuan-input border rounded px-2 py-1 w-full text-center" readonly>
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="number" name="qty[]" class="qty-input border rounded px-2 py-1 w-full text-right" min="0" step="any">
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="number" name="harga[]" class="harga-input border rounded px-2 py-1 w-full text-right" min="0" step="any">
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="number" name="jumlah[]" class="jumlah-input border rounded px-2 py-1 w-full text-right" min="0" step="any">
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="date" name="expired[]" class="w-full border rounded px-2 py-1">
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="text" name="batch[]" placeholder="Batch No" class="w-full border rounded px-2 py-1">
                                 </td>
                                 <td class="border px-2 py-1 text-center">
                                    <button type="button" class="remove-row bg-red-500 text-white px-2 py-1 rounded">✕</button>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="mt-3">
                        <button type="button" id="addRow" class="bg-blue-500 text-white px-4 py-2 rounded">
                        + Tambah Baris
                        </button>
                     </div>
                  </form>
                  <!-- FORM ditutup di sini -->
               </div>
               <!-- Entry Obat Keluar -->
               <div x-show="transaksiTab === 'keluar'" x-transition
                  class="absolute inset-0 p-4 bg-white rounded-lg shadow-inner">
                  <h3 class="text-lg font-bold mb-2">Entry Obat Keluar</h3>
                  <!-- isi form keluar -->
                  <form method="POST" action="{{ route('stock-obat.storeLK') }}">
                     @csrf
                     <input type="hidden" name="lokasi" value="{{ session('idpay') }}">
                     <!-- Form atas -->
                     <div class="flex flex-wrap items-center gap-4 mb-4">
                        <div>
                           <label for="tgl_keluar">Tanggal Keluar :</label>
                           <input type="date" id="tgl_keluar" name="tanggal"
                              class="border rounded-lg px-3 py-2 focus:ring focus:ring-blue-400"
                              value="{{ $tanggal ?? '' }}">
                           <div>
                              <label for="no_transaksi_keluar">No _ Transaksi :</label>
                              <input type="text" id="no_transaksi_keluar" name="nomor"
                                 class="mt-2 border rounded-lg px-3 py-2 w-60 h-7 focus:ring focus:ring-blue-400"
                                 value="{{ $noTransaksiLk ?? '' }}" readonly>
                           </div>
                        </div>
                        <div>
                           <!--<label class="block mb-1 font-medium">Nama Pasien :</label>-->
                           <select name="nama_pasien" id="pasien"
                              class="border rounded-lg px-3 py-2 w-[500px] focus:outline-none focus:ring focus:ring-blue-400">
                              <option value="nama_pasien">-- Pilih Pasien --</option>
                           </select>
                           <input type="hidden" name="no_kunjungan" value="">
                           <div class="mt-2">
                              <input type="text" name="no_rm" value="Nomor RM" 
                                 class="border rounded-lg text-gray-400 px-2 py-2 w-80 h-7 focus:ring focus:ring-blue-400" readonly>
                           </div>
                        </div>
                        <div class="flex items-end gap-2">
                           <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">
                           💾 Simpan
                           </button>
                           <button type="button" id="clearRowsLK"
                              class="px-4 py-2 bg-green-400 text-white rounded-lg shadow hover:bg-yellow-600">
                           🧹 Clear
                           </button>
                        </div>
                     </div>
                     <div id="errorLK" class="text-red-600 mt-2"></div>
                     <!-- Tabel input -->
                     <div class="overflow-x-auto">
                        <table id="tableKeluar" class="min-w-full border border-gray-300 text-sm">
                           <thead class="bg-gray-100">
                              <tr>
                                 <th class="border px-3 py-2 text-center w-72">Obat</th>
                                 <th class="border px-3 py-2 text-center w-24">Satuan</th>
                                 <th class="border px-3 py-2 text-center w-20">Qty</th>
                                 <th class="border px-3 py-2 text-center w-32">Harga</th>
                                 <th class="border px-3 py-2 text-center w-32">Jumlah</th>
                                 <th class="border px-3 py-2 text-center w-10">Aksi</th>
                              </tr>
                           </thead>
                           <tbody id="obatKeluarBody">
                              <tr>
                                 <td class="border px-2 py-1">
                                    <select class="obat-lookupLK border rounded px-2 py-1 w-full">
                                       <option value="">-- Pilih Obat --</option>
                                       <!--@foreach($obatList ?? [] as $obat)
                                          <option value="{{ $obat->kode_obat }}" data-satuan="{{ $obat->satuan }}">
                                              {{ $obat->nama_obat }}
                                          </option>
                                          @endforeach-->
                                    </select>
                                    <input type="hidden" name="kode_obat[]" class="kode-obat-hidden">
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="text" name="satuan[]" class="satuan-input border rounded px-2 py-1 w-full text-center" readonly>
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="number" name="qty[]" class="qty-input border rounded px-2 py-1 w-full text-right" min="0" step="any">
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="number" name="harga[]" class="harga-input border rounded px-2 py-1 w-full text-right" min="0" step="any">
                                 </td>
                                 <td class="border px-2 py-1">
                                    <input type="number" name="jumlah[]" class="jumlah-input border rounded px-2 py-1 w-full text-right" min="0" step="any">
                                 </td>
                                 <td class="border px-2 py-1 text-center">
                                    <button type="button" class="remove-row bg-red-500 text-white px-2 py-1 rounded">✕</button>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="mt-3">
                        <button type="button" id="addRowKeluar" class="bg-blue-500 text-white px-4 py-2 rounded">
                        + Tambah Baris
                        </button>
                     </div>
                  </form>
               </div>
               <!-- Explore Data -->
               <div x-show="transaksiTab === 'explore'" x-transition
                  class="absolute inset-0 flex flex-col">
                  <!-- Tab Switch -->
                  <div class="flex space-x-4 mb-4">
                     <button @click="exploreTab = 'datamasuk'"
                        :class="exploreTab === 'datamasuk' ? 'bg-yellow-500 text-white shadow-md' : 'bg-yellow-100 text-gray-700'"
                        class="px-4 py-2 rounded-lg transition-all duration-300">
                     📥 Data Masuk
                     </button>
                     <button @click="exploreTab = 'datakeluar'"
                        :class="exploreTab === 'datakeluar' ? 'bg-yellow-500 text-white shadow-md' : 'bg-yellow-100 text-gray-700'"
                        class="px-4 py-2 rounded-lg transition-all duration-300">
                     📤 Data Keluar
                     </button>
                  </div>
                  <!-- Content -->
                  <div class="relative flex-1 overflow-hidden">
                     <!-- 📥 Data Masuk -->
                     <div x-show="exploreTab === 'datamasuk'" x-transition
                        class="absolute inset-0 p-4 bg-yellow-50 rounded-lg shadow-inner flex flex-col space-y-4"
                        x-data="{headerMasuk: [],
                                 detailMasuk: [],
                                 tahunMasuk: '',
                                 bulanMasuk: '',
                                 selectedNomor: '',
                                 selectedTanggal: '',
                                 showModalEditLT: false,
                                 editRowsLT: [],
                                 hapusIds: [],

                                 async hapusDataMasuk(nomor) {
                                          if (confirm('Yakin ingin menghapus data MASUK dengan nomor ' + nomor + '?')) {
                                             const res = await fetch(`/explore/masuk/delete/${encodeURIComponent(nomor)}`, {
                                                method: 'DELETE',
                                                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                                             });
                                             const data = await res.json();
                                             if (data.success) {
                                                alert('✅ Data MASUK berhasil dihapus');
                                                this.headerMasuk = this.headerMasuk.filter(h => h.nomor !== nomor);
                                             } else {
                                                alert('⚠️ ' + (data.error || 'Gagal menghapus data MASUK'));
                                             }
                                          }
                                       },

                                 async bukaEditLT(nomor) {
                                       if (!nomor) {
                                          alert('⚠️ Pilih nomor dulu dari daftar sebelum mengedit!');
                                          return;
                                       }
                                       this.selectedNomor = nomor;
                                       this.showModalEditLT = true;

                                       const res = await fetch(`/explore/masuk/detail/${encodeURIComponent(nomor)}`);
                                       const data = await res.json();

                                       this.editRowsLT = data.map(r => ({
                                          id: r.id,  // ⚠️ penting!
                                          kode: r.kode,
                                          satuan: r.satuan,
                                          qty: r.qty,
                                          harga: r.harga,
                                          jumlah: r.jumlah,
                                          expired: r.expired || '',
                                          no_batch: r.no_batch || '',
                                          ket: r.ket || ''
                                       }));
                                 },

                                 tambahBarisEditLT() {
                                       this.editRowsLT.push({ id: null, kode: '', satuan: '', qty: 0, harga: 0, jumlah: 0, expired: '', no_batch: '' });
                                 },

                                 hapusBarisEditLT(index) {
                                 const row = this.editRowsLT[index];
                                 if (row.id) {
                                    this.hapusIds.push(row.id); // tandai untuk dihapus di backend
                                 }
                                 this.editRowsLT.splice(index, 1);
                                 },

                                 async simpanEditLT() {
                                 const payload = { 
                                    nomor: this.selectedNomor, 
                                    tanggal: this.selectedTanggal, 
                                    rows: this.editRowsLT, 
                                    hapusIds: this.hapusIds
                                 };

                                 console.log('🔹 Payload terkirim:', payload);

                                 const res = await fetch('/explore/masuk/update', {
                                    method: 'POST',
                                    headers: {
                                          'Content-Type': 'application/json',
                                          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                    },
                                    body: JSON.stringify(payload)
                                 });

                                 // Log hasil response
                                 const data = await res.json().catch(() => ({ error: 'Respon bukan JSON' }));
                                 console.log('📦 Respons server:', data);

                                 if (data.success) {
                                    alert('✅ Data berhasil diperbarui');
                                    this.showModalEditLT = false;
                                    this.hapusIds = [];
                                 } else {
                                    alert('❌ Gagal memperbarui data: ' + (data.error || 'Unknown error'));
                                 }
                              }
                              }">
                        <!-- Filter Periode -->
                        <div class="flex space-x-4">
                           <select x-model="bulanMasuk" class="border rounded-lg px-3 py-2">
                              <option value="">-- Bulan --</option>
                              <template x-for="(nama, index) in ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']" :key="index">
                                 <option :value="index+1" x-text="nama"></option>
                              </template>
                           </select>
                           <select x-model="tahunMasuk" class="border rounded-lg px-3 py-2">
                              <option value="">-- Tahun --</option>
                              <template 
                                 x-for="th in Array.from({ length: (new Date().getFullYear() + 1) - 2025 + 1 }, (_, i) => 2025 + i)" 
                                 :key="th"
                              >
                                 <option :value="th" x-text="th"></option>
                              </template>
                           </select>
                           <button @click="if(bulanMasuk && tahunMasuk){
                              fetch(`/explore/masuk/${tahunMasuk}/${bulanMasuk}`)
                              .then(r=>r.json()).then(d=>{headerMasuk=d; detailMasuk=[]});
                              }"
                              class="px-3 py-2 bg-blue-500 text-white rounded-lg">🔍 Tampilkan LT</button>
                           <button class="px-3 py-2 bg-yellow-500 text-white rounded-lg"
                                    @click="bukaEditLT(selectedNomor)">
                                 🖋 Edit Data LT
                           </button> 
                           <!-- Modal popup edit -->
                           <div x-show="showModalEditLT" x-transition
                                 class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                                 <div id="modalEditContainerLT" class="bg-white rounded-xl shadow-xl p-4 w-[1000px]"
                                    @click.outside="showModalEditLT=false; editRowsLT=[]">

                                    <h2 class="text-lg font-semibold mb-3">Edit Data LT: <span x-text="selectedNomor"></span></h2>

                                    <!-- tabel input -->
                                    <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                                       <table class="min-w-full border border-gray-300 text-sm">
                                             <thead class="bg-gray-100">
                                                <tr>
                                                   <th class="border px-3 py-2 text-center w-68">Obat</th>
                                                   <th class="border px-3 py-2 text-center w-24">Satuan</th>
                                                   <th class="border px-3 py-2 text-center w-20">Qty</th>
                                                   <th class="border px-3 py-2 text-center w-32">Harga</th>
                                                   <th class="border px-3 py-2 text-center w-32">Jumlah</th>
                                                   <th class="border px-2 py-1 text-center w-32">Expired</th>
                                                   <th class="border px-2 py-1 text-center w-32">No Batch</th>
                                                   <th class="border px-2 py-1 w-10 text-center">🗑️</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <template x-for="(r, i) in editRowsLT" :key="r.id ?? i">
                                                   <tr x-effect="r.jumlah = (parseFloat(r.qty||0) * parseFloat(r.harga||0)).toFixed(2)">
                                                         <td class="border px-2 py-1">
                                                            <select x-model="r.kode"
                                                               x-init="$nextTick(() => { 
                                                                     const el = $el;
                                                                     $(el).select2({
                                                                        placeholder: '🔍 Cari obat...',
                                                                        allowClear: true,
                                                                        width: '100%',
                                                                        dropdownParent: $('#modalEditContainerLT')
                                                                     });
                                                                     $(el).on('change', (e) => {
                                                                        const selected = e.target.selectedOptions[0];
                                                                        r.kode = e.target.value;
                                                                        r.satuan = selected?.dataset.satuan || '';
                                                                        r.harga = parseFloat(selected?.dataset.harga || 0);
                                                                        r.jumlah = (r.qty * r.harga).toFixed(2);
                                                                     });
                                                               })"
                                                               class="select2-obat border rounded px-2 py-1 w-full">
                                                               <option value="">-- Pilih Obat --</option>
                                                               @foreach($obatList ?? [] as $obat)
                                                               <option value="{{ $obat->kode_obat }}" 
                                                                        data-satuan="{{ $obat->satuan }}">
                                                                     {{ $obat->nama_obat }}
                                                               </option>
                                                               @endforeach
                                                            </select>
                                                         </td>
                                                         <td class="border px-2 py-1">
                                                            <input type="text" x-model="r.satuan" class="border rounded px-2 py-1 w-full text-center" readonly>
                                                         </td>
                                                         <td class="border px-2 py-1">
                                                            <input type="number" x-model.number="r.qty" class="border rounded px-2 py-1 w-full text-right" @input="r.jumlah = (r.qty * r.harga).toFixed(2)">
                                                         </td>
                                                         <td class="border px-2 py-1">
                                                            <input type="number" x-model.number="r.harga" class="border rounded px-2 py-1 w-full text-right" @input="r.jumlah = (r.qty * r.harga).toFixed(2)">
                                                         </td>
                                                         <td class="border px-2 py-1">
                                                            <input type="number" x-model.number="r.jumlah" class="border rounded px-2 py-1 w-full text-right" readonly>
                                                         </td>
                                                         <td class="border px-2 py-1">
                                                            <input type="date" x-model="r.expired" class="w-full border rounded p-1">
                                                         </td>
                                                         <td class="border px-2 py-1">
                                                            <input type="text" x-model="r.no_batch" class="w-full border rounded p-1 text-center">
                                                         </td>
                                                         <td class="border px-2 py-1 text-center">
                                                            <button @click="hapusBarisEditLT(i)" class="bg-red-500 text-white px-2 py-1 rounded">✕</button>
                                                         </td>
                                                   </tr>
                                                </template>
                                             </tbody>
                                       </table>
                                    </div>

                                    <div class="mt-3 flex justify-between">
                                       <button @click="tambahBarisEditLT()" class="bg-blue-500 text-white px-4 py-2 rounded">+ Tambah Baris</button>
                                       <div class="space-x-2">
                                             <button @click="simpanEditLT()" class="bg-green-600 text-white px-4 py-2 rounded">💾 Simpan</button>
                                             <button @click="showModalEditLT=false; editRowsLT=[]" class="bg-gray-400 text-white px-4 py-2 rounded">Tutup</button>
                                       </div>
                                    </div>
                                 </div>
                           </div>                         
                        </div>
                        <!-- Tabel -->
                        <div class="flex flex-1 space-x-4 overflow-hidden">
                           <!-- Header (kiri) -->
                           <div class="w-1/4 bg-white rounded-xl shadow p-2 overflow-auto">
                              <h5 class="font-bold mb-2">Header Masuk</h5>
                              <table class="w-full text-m border">
                                 <thead class="bg-gray-200">
                                    <tr>
                                       <th class="border px-2 py-1">Tanggal</th>
                                       <th class="border px-2 py-1">Nomor</th>
                                       <th class="border px-2 py-1">Aksi</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <template x-for="h in headerMasuk" :key="h.nomor">
                                       <tr class="hover:bg-yellow-100 cursor-pointer"
                                          @click="selectedNomor=h.nomor;
                                          fetch(`/explore/masuk/detail/${h.nomor}`)
                                          .then(r=>r.json()).then(d=>detailMasuk=d)">
                                          <td class="border px-2 py-1" x-text="new Date(h.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })"></td>
                                          <td class="border px-2 py-1 font-mono text-blue-600" x-text="h.nomor"></td>
                                          <td class="border px-2 py-1 text-center">
                                             <button @click.stop="hapusDataMasuk(h.nomor)"
                                                   class="text-red-600 hover:text-red-800">❌</button>
                                          </td>
                                       </tr>
                                    </template>
                                 </tbody>
                              </table>
                           </div>
                           <!-- Detail (kanan) -->
                           <div class="w-3/4 bg-white rounded-xl shadow p-2 overflow-auto">
                              <h5 class="font-bold mb-2">Detail Masuk -> <span x-text="selectedNomor"></span></h5>
                              <table class="w-full text-m border">
                                 <thead class="bg-gray-200">
                                    <tr>
                                       <th class="border px-2 py-1">No</th>
                                       <th class="border px-2 py-1">Kode</th>
                                       <th class="border px-2 py-1">Nama Obat</th>
                                       <th class="border px-2 py-1">Qty</th>
                                       <th class="border px-2 py-1">Satuan</th>
                                       <th class="border px-2 py-1">Harga</th>
                                       <th class="border px-2 py-1">Jumlah</th>
                                       <th class="border px-2 py-1">Batch</th>
                                       <th class="border px-2 py-1">Exp</th>
                                    </tr>
                                 </thead>
                                 <tbody class="divide-y divide-gray-200">
                                    <template x-for="d in detailMasuk" :key="d.no">
                                       <tr class="odd:bg-gray-50 even:bg-white">
                                          <td class="border px-2 py-1 text-center" x-text="d.no"></td>
                                          <td class="border px-2 py-1" x-text="d.kode"></td>
                                          <td class="border px-2 py-1" x-text="d.nama_obat"></td>
                                          <td class="border px-2 py-1 text-center" x-text="parseFloat(d.qty).toFixed(0)"></td>
                                          <td class="border px-2 py-1" x-text="d.satuan"></td>
                                          <td class="border px-2 py-1 text-right" x-text="parseFloat(d.harga).toFixed(2)"></td>
                                          <td class="border px-2 py-1 text-right" x-text="parseFloat(d.jumlah).toFixed(2)"></td>
                                          <td class="border px-2 py-1" x-text="d.no_batch"></td>
                                          <td class="border px-2 py-1" x-text="new Date(d.expired).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })"></td>
                                       </tr>
                                    </template>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                     <!-- 📤 Data Keluar -->
                     <div x-show="exploreTab === 'datakeluar'" x-transition
                        class="absolute inset-0 p-4 bg-yellow-50 rounded-lg shadow-inner flex flex-col space-y-4"
                        x-data="{ headerKeluar: [],
                                 detailKeluar: [],
                                 tahunKeluar: '',
                                 bulanKeluar: '',
                                 selectedNomor: '',
                                 selectedNamaPasien: '',
                                 selectedNoRM: '',
                                 selectedTanggal: '',
                                 showModalEdit: false,
                                 editRows: [],

                                 async bukaEditLK(nomor) {
                                       if (!nomor) {
                                          alert('⚠️ Pilih nomor dulu dari daftar sebelum mengedit!');
                                          return;
                                       }
                                    
                                    this.selectedNomor = nomor;
                                    this.showModalEdit = true;

                                    const res = await fetch(`explore/keluar/detail/${encodeURIComponent(nomor)}`);
                                    const data = await res.json();
                                    
                                    this.editRows = data.map(r => ({
                                       id: r.id,
                                       kode: r.kode,
                                       satuan: r.satuan,
                                       qty: r.qty,
                                       harga: r.harga,
                                       jumlah: r.jumlah
                                    }));
                                 },

                                 tambahBarisEdit() {
                                       this.editRows.push({ id: null, kode: '', satuan: '', qty: 0, harga: 0, jumlah: 0 });
                                 },

                                 hapusBarisEdit(index) {
                                       this.editRows.splice(index, 1);
                                 },

                                 async simpanEditLK() {
                                       const payload = { nomor: this.selectedNomor, nama_pasien: this.selectedNamaPasien, no_rm: this.selectedNoRM, tanggal: this.selectedTanggal, rows: this.editRows };

                                       const res = await fetch('/explore/keluar/update', {
                                          method: 'POST',
                                          headers: {
                                             'Content-Type': 'application/json',
                                             'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                          },
                                          body: JSON.stringify(payload)
                                       });

                                       const data = await res.json();
                                       if (data.success) {
                                          alert('✅ Data berhasil diperbarui');
                                          this.showModalEdit = false;
                                       } else {
                                          alert('❌ Gagal memperbarui data: ' + (data.error || 'Unknown error'));
                                       }
                                 }
                              }">
                        <!-- Filter Periode -->
                        <div class="flex space-x-4">
                           <select x-model="bulanKeluar" class="border rounded-lg px-3 py-2">
                              <option value="">-- Bulan --</option>
                              <template x-for="(nama, index) in ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']" :key="index">
                                 <option :value="index+1" x-text="nama"></option>
                              </template>
                           </select>
                           <select x-model="tahunKeluar" class="border rounded-lg px-3 py-2">
                              <option value="">-- Tahun --</option>
                              <template 
                                 x-for="th in Array.from({ length: (new Date().getFullYear() + 1) - 2025 + 1 }, (_, i) => 2025 + i)" 
                                 :key="th"
                              >
                                 <option :value="th" x-text="th"></option>
                              </template>
                           </select>
                           <button @click="if(bulanKeluar && tahunKeluar){
                              fetch(`/explore/keluar/${tahunKeluar}/${bulanKeluar}`)
                              .then(r=>r.json()).then(d=>{headerKeluar=d; detailKeluar=[]});
                              }"
                              class="px-3 py-2 bg-blue-500 text-white rounded-lg">🔍 Tampilkan LK</button>
                              <button class="px-3 py-2 bg-yellow-500 text-white rounded-lg"
                                    @click="bukaEditLK(selectedNomor)">
                                 🖋 Edit Data LK
                              </button>

                              <!-- Modal popup edit -->
            <div x-show="showModalEdit" x-transition
               class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
               <div id="modalEditContainer" class="bg-white rounded-xl shadow-xl p-4 w-[900px]" 
                     @click.outside="showModalEdit=false; editRows=[]">

                  <h2 class="text-lg font-semibold mb-3">Edit Data LK: <span x-text="selectedNomor"></span></h2>

                  <!-- Tabel input -->
                  <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                        <table class="min-w-full border border-gray-300 text-sm">
                           <thead class="bg-gray-100">
                              <tr>
                                    <th class="border px-3 py-2 text-center w-72">Obat</th>
                                    <th class="border px-3 py-2 text-center w-24">Satuan</th>
                                    <th class="border px-3 py-2 text-center w-20">Qty</th>
                                    <th class="border px-3 py-2 text-center w-32">Harga</th>
                                    <th class="border px-3 py-2 text-center w-32">Jumlah</th>
                                    <th class="border px-3 py-2 text-center w-10">Aksi</th>
                              </tr>
                           </thead>
                           <tbody>
                              <template x-for="(r, i) in editRows" :key="r.id ?? i">
                                    <tr x-effect="r.jumlah = (parseFloat(r.qty||0) * parseFloat(r.harga||0)).toFixed(2)">
                                       <td class="border px-2 py-1">
                                          <select x-model="r.kode"
                                                   x-init="$nextTick(() => { 
                                                      const el = $el;
                                                      $(el).select2({
                                                            placeholder: '🔍 Cari obat...',
                                                            allowClear: true,
                                                            width: '100%',
                                                            dropdownParent: $('#modalEditContainer')
                                                      });
                                                      $(el).on('change', (e) => {
                                                            const selected = e.target.selectedOptions[0];
                                                            r.kode = e.target.value;
                                                            r.satuan = selected?.dataset.satuan || '';
                                                            r.harga = parseFloat(selected?.dataset.harga || 0);
                                                            r.jumlah = (r.qty * r.harga).toFixed(2);
                                                      });
                                                   })"
                                                   class="select2-obat border rounded px-2 py-1 w-full">
                                                <option value="">-- Pilih Obat --</option>
                                                @foreach($obatList ?? [] as $obat)
                                                   <option value="{{ $obat->kode_obat }}" 
                                                            data-satuan="{{ $obat->satuan }}">
                                                      {{ $obat->nama_obat }}
                                                   </option>
                                                @endforeach
                                          </select>
                                       </td>
                                       <td class="border px-2 py-1">
                                          <input type="text" x-model="r.satuan" class="border rounded px-2 py-1 w-full text-center" readonly>
                                       </td>
                                       <td class="border px-2 py-1">
                                          <input type="number" x-model.number="r.qty" class="border rounded px-2 py-1 w-full text-right" @input="r.jumlah = (r.qty * r.harga).toFixed(2)">
                                       </td>
                                       <td class="border px-2 py-1">
                                          <input type="number" x-model.number="r.harga" class="border rounded px-2 py-1 w-full text-right" @input="r.jumlah = (r.qty * r.harga).toFixed(2)">
                                       </td>
                                       <td class="border px-2 py-1">
                                          <input type="number" x-model.number="r.jumlah" class="border rounded px-2 py-1 w-full text-right" readonly>
                                       </td>
                                       <td class="border px-2 py-1 text-center">
                                          <button @click="hapusBarisEdit(i)" class="bg-red-500 text-white px-2 py-1 rounded">✕</button>
                                       </td>
                                    </tr>
                              </template>
                           </tbody>
                        </table>
                  </div>

                  <div class="mt-3 flex justify-between">
                        <button @click="tambahBarisEdit()" class="bg-blue-500 text-white px-4 py-2 rounded">+ Tambah Baris</button>
                        <div class="space-x-2">
                           <button @click="simpanEditLK()" class="bg-green-600 text-white px-4 py-2 rounded">💾 Simpan</button>
                           <button @click="showModalEdit=false; editRows=[]" class="bg-gray-400 text-white px-4 py-2 rounded">Tutup</button>
                        </div>
                  </div>

               </div>
            </div>
                        </div>                        
                        
                        <!-- Tabel -->
                        <div class="flex flex-1 space-x-4 overflow-hidden">
                           <!-- Header (kanan) -->
                           <div class="w-[600px] bg-white rounded-xl shadow p-2 overflow-auto"
                           x-data="{
                                    async hapusDataKeluar(nomor) {
                                          if (confirm('Yakin ingin menghapus data KELUAR dengan nomor ' + nomor + '?')) {
                                             try {
                                                const res = await fetch(`/explore/keluar/delete/${encodeURIComponent(nomor)}`, {
                                                      method: 'DELETE',
                                                      headers: {
                                                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                      }
                                                });
                                                const data = await res.json();

                                                if (data.success) {
                                                      alert('✅ Data KELUAR berhasil dihapus');
                                                      // hapus baris dari array headerKeluar
                                                      this.headerKeluar = this.headerKeluar.filter(h => h.nomor !== nomor);
                                                } else {
                                                      alert('⚠️ ' + (data.error || 'Gagal menghapus data KELUAR'));
                                                }
                                             } catch (err) {
                                                alert('Terjadi kesalahan: ' + err.message);
                                             }
                                          }
                                    }
                                 }">
                              <h5 class="font-bold mb-2">Header Keluar</h5>
                              <table class="w-full text-sm border">
                                 <thead class="bg-gray-200">
                                    <tr>
                                       <th class="border px-2 py-1">Tanggal</th>
                                       <th class="border px-2 py-1">Nomor</th>
                                       <th class="border px-2 py-1">Nama Pasien</th>
                                       <th class="border px-2 py-1 text-center">Aksi</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <template x-for="h in headerKeluar" :key="h.nomor">
                                       <tr class="hover:bg-yellow-100 cursor-pointer"
                                          @click="selectedNomor=h.nomor;
                                          selectedNamaPasien=h.nama_pasien;
                                          selectedNoRM=h.no_rm;
                                          selectedTanggal = h.tanggal;
                                          fetch(`/explore/keluar/detail/${h.nomor}`)
                                          .then(r=>r.json()).then(d=>detailKeluar=d)">
                                          <td class="border px-2 py-1" x-text="new Date(h.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })"></td>
                                          <td class="border px-2 py-1 font-mono text-blue-600" x-text="h.nomor"></td>
                                          <td class="border px-2 py-1 font-mono text-blue-600" x-text="h.nama_pasien"></td>
                                          <td class="border px-2 py-1 text-center">
                                             <button @click.stop="hapusDataKeluar(h.nomor)"
                                                   class="text-red-600 hover:text-red-800">❌</button>
                                          </td>
                                       </tr>
                                    </template>
                                 </tbody>
                              </table>
                           </div>
                           <!-- Detail (kiri) -->
                           <div class="w-[900px] bg-white rounded-xl shadow p-2 overflow-auto">
                              <h5 class="font-bold mb-2">Detail Keluar -> <span x-text="selectedNomor"></span></h5>
                              <table class="w-full text-sm border">
                                 <thead class="bg-gray-200">
                                    <tr>
                                       <th class="border px-2 py-1">No</th>
                                       <th class="border px-2 py-1">Kode</th>
                                       <th class="border px-2 py-1">Nama Obat</th>
                                       <th class="border px-2 py-1">Qty</th>
                                       <th class="border px-2 py-1">Satuan</th>
                                       <th class="border px-2 py-1">Harga</th>
                                       <th class="border px-2 py-1">Jumlah</th>
                                    </tr>
                                 </thead>
                                 <tbody class="divide-y divide-gray-200">
                                    <template x-for="d in detailKeluar" :key="d.no">
                                       <tr class="odd:bg-gray-50 even:bg-white">
                                          <td class="border px-2 py-1 text-center" x-text="d.no"></td>
                                          <td class="border px-2 py-1" x-text="d.kode"></td>
                                          <td class="border px-2 py-1" x-text="d.nama_obat"></td>
                                          <td class="border px-2 py-1 text-center" x-text="parseFloat(d.qty).toFixed(0)"></td>
                                          <td class="border px-2 py-1" x-text="d.satuan"></td>
                                          <td class="border px-2 py-1 text-right" x-text="parseFloat(d.harga).toFixed(2)"></td>
                                          <td class="border px-2 py-1 text-right" x-text="parseFloat(d.jumlah).toFixed(2)"></td>
                                       </tr>
                                    </template>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- 3. PP OBAT -->
         <div x-show="tab === 'ppobat'" x-transition
            class="absolute inset-0 p-4 bg-purple-50 rounded-lg shadow-inner flex flex-col"
            x-data="{ ppTab: 'minimal' }"
            @pp-transferred.window="ppTab = 'formatpp'">
            <!-- Sub-tab navigasi -->
            <div class="flex space-x-4 mb-4">
               <button @click="ppTab = 'minimal'"
                  :class="ppTab === 'minimal' ? 'bg-purple-600 text-white shadow-md' : 'bg-purple-100 text-gray-700'"
                  class="px-4 py-2 rounded-lg transition-all duration-300">
               ⚠️ Informasi Stock Minimal
               </button>
               <button @click="ppTab = 'formatpp'"
                  :class="ppTab === 'formatpp' ? 'bg-purple-600 text-white shadow-md' : 'bg-purple-100 text-gray-700'"
                  class="px-4 py-2 rounded-lg transition-all duration-300">
               🏷️ Format PP
               </button>
               <button @click="ppTab = 'history'"
                  :class="ppTab === 'history' ? 'bg-purple-600 text-white shadow-md' : 'bg-purple-100 text-gray-700'"
                  class="px-4 py-2 rounded-lg transition-all duration-300">
               🕒 Daftar PP
               </button>
            </div>
            <div class="relative flex-1 overflow-hidden">
               <!-- Informasi Stock Minimal -->
               <div x-show="ppTab === 'minimal'" x-transition
                  x-data="infoMinimal()" x-init="init()"
                  class="absolute inset-0 p-4 bg-white rounded-lg shadow-inner flex flex-col">
                  <h3 class="text-lg font-bold mb-4">Informasi Stock Minimal</h3>
                  <!-- Filter -->
                  <div class="flex items-center gap-4 mb-4">
                     <select x-model="bulan" @change="loadData()"
                        class="border rounded-lg px-3 py-2 focus:ring focus:ring-yellow-400">
                        <!--<option value="">-- Bulan --</option>-->
                        <template x-for="(nama, num) in bulanList" :key="num">
                           <option :value="num" x-text="nama" :selected="String(num).padStart(2,'0') === bulan"></option>
                        </template>
                     </select>
                     <select x-model="tahun" @change="loadData()"
                        class="border rounded-lg px-3 py-2 focus:ring focus:ring-yellow-400">
                        <!--<option value="">-- Tahun --</option>-->
                        <template x-for="y in tahunList" :key="y">
                           <option :value="y" x-text="y" :selected="y == tahun"></option>
                        </template>
                     </select>
                     <!--<label for="golongan" class="block text-sm font-semibold">Golongan</label>-->
                     <select x-model="selectedGolongan" id="golongan" 
                        @change="onGolonganChange()"
                        class="border rounded-lg px-3 py-2 focus:ring focus:ring-yellow-400">
                        <option value="">-- Pilih Golongan --</option>
                        <template x-for="item in golonganList" :key="item.id_gol">
                           <option :value="item.id_gol" x-text="item.golongan"></option>
                        </template>
                     </select>
                     <button type="button" @click="transferToPP()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                     📤 Transfer ke Format PP
                     </button>
                  </div>
                  <!-- Table -->
                  <div class="overflow-x-auto flex-1">
                     <table class="min-w-full border border-gray-300 text-sm">
                        <thead class="bg-gray-100 sticky top-0 z-10">
                           <tr>
                              <th class="border px-3 py-2">Kode</th>
                              <th class="border px-3 py-2">Nama Obat</th>
                              <th class="border px-3 py-2">Satuan</th>
                              <th class="border px-3 py-2">Stock Minimal</th>
                              <th class="border px-3 py-2">Stock Akhir</th>
                              <th class="border px-3 py-2">Status PP</th>
                              <th class="border px-3 py-2">Checklist</th>
                           </tr>
                        </thead>
                        <tbody>
                           <template x-for="(row, idx) in data" :key="row.kode_obat">
                              <tr :class="idx % 2 ? 'bg-gray-50' : ''">
                                 <td class="border px-2 py-1" x-text="row.kode_obat"></td>
                                 <td class="border px-2 py-1" x-text="row.nama_obat"></td>
                                 <td class="border px-2 py-1 text-center" x-text="row.satuan"></td>
                                 <td class="border px-2 py-1 text-right" x-text="row.stok_minimal"></td>
                                 <td class="border px-2 py-1 text-right" x-text="row.stock_akhir"></td>
                                 <td class="border px-2 py-1 text-center">
                                    <span x-show="row.status_pp === 'YES'" class="text-red-600 font-bold">YES</span>
                                 </td>
                                 <td class="border px-2 py-1 text-center">
                                    <!-- pakai x-model agar perubahan checkbox disimpan ke data -->
                                    <input type="checkbox" x-model="row.checklist">
                                 </td>
                              </tr>
                           </template>
                           <template x-if="data.length === 0">
                              <tr>
                                 <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada data untuk periode ini</td>
                              </tr>
                           </template>
                        </tbody>
                     </table>
                  </div>
               </div>
               <!-- Format PP -->
               <div x-show="ppTab === 'formatpp'" x-transition
                  class="absolute inset-0 p-4 bg-white rounded-lg shadow-inner flex flex-col">
                  <!--<h3 class="text-lg font-bold mb-4">Format PP</h3>-->
                  <!-- Input Atas -->
                  <div class="flex flex-wrap gap-4 mb-4">
                     <div>
                        <label for="no_urut" class="block text-sm font-semibold">Nomor PP</label>
                        <input type="number" id="no_urut" name="no_urut"
                           class="border w-[80px] rounded-lg px-3 py-2 focus:ring focus:ring-green-400">
                     </div>
                     <div>
                        <label for="tgl_pp" class="block text-sm font-semibold">Tanggal PP</label>
                        <input type="text" id="tgl_pp" name="tgl_pp"
                           class="border rounded-lg px-3 py-2 focus:ring focus:ring-green-400" placeholder="dd/MM/yyyy">
                     </div>
                     <div>
                        <label for="no_pp" class="block text-sm font-semibold">.</label>
                        <input type="text" id="no_pp" name="no_pp"
                           class="border rounded-lg px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed" readonly>
                     </div>
                  </div>
                  <!-- Tombol Aksi -->
                  <div class="flex gap-3 mb-4">
                     <button type="button" onclick="hapusBaris()" class="bg-orange-400 text-white px-4 py-2 rounded shadow hover:bg-red-600">❌ Hapus Baris</button>
                     <button @click="$store.pp.clear()" class="bg-red-700 text-white px-4 py-2 rounded shadow hover:bg-red-800">🗑️ Hapus Semua</button>
                     <button type="button" onclick="simpanPP()" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">💾 Simpan</button>
                     <!--<button @click="showModal = true" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">👁️ Lihat Data PP</button>-->
                  </div>
                  <!-- Tabel Format PP -->
                  <div class="overflow-x-auto flex-1">
                     <table id="tabelFormatPP" class="min-w-full border border-gray-300 text-sm">
                        <thead class="bg-gray-100 sticky top-0 z-10">
                           <tr>
                              <th class="border px-3 py-2 text-center w-[20]">✔</th>
                              <th class="border px-3 py-2 text-center w-[80]">Kode</th>
                              <th class="border px-3 py-2 text-center w-[800]">Nama Obat</th>
                              <th class="border px-3 py-2 text-center w-[100]">Satuan</th>
                              <th class="border px-3 py-2 text-center w-[100]">Stock Akhir</th>
                              <th class="border px-3 py-2 text-center w-[100]">Jumlah PP</th>
                              <th class="border px-3 py-2 text-center ">Keterangan</th>
                           </tr>
                        </thead>
                        <tbody>
                           <template x-for="(row, idx) in $store.pp.formatRows" :key="row.kode_obat">
                              <tr :class="idx % 2 ? 'bg-gray-50' : ''">
                                 <td class="border px-2 py-1 text-center">
                                    <input type="checkbox" class="row-check">
                                 </td>
                                 <td class="border px-2 py-1 text-center" x-text="row.kode_obat"></td>
                                 <td class="border px-2 py-1" x-text="row.nama_obat"></td>
                                 <td class="border px-2 py-1 text-center" x-text="row.satuan"></td>
                                 <td class="border px-2 py-1 text-right" x-text="row.stock_akhir"></td>
                                 <td class="border px-2 py-1 text-right">
                                    <input type="number" x-model.number="$store.pp.formatRows[idx].jumlah_pp" class="w-28 border rounded px-1 py-0.5">
                                 </td>
                                 <td class="border px-2 py-1 text-right">
                                    <input type="text" class=" w-full border rounded px-1 py-0.5">
                                 </td>
                              </tr>
                           </template>
                           <template x-if="$store.pp.formatRows.length === 0">
                              <tr>
                                 <td colspan="5" class="text-center py-4 text-gray-500">Belum ada data PP</td>
                              </tr>
                           </template>
                        </tbody>
                     </table>
                  </div>
               </div>
               <!-- Tab Daftar PP -->
               <div x-show="ppTab === 'history'" x-data="daftarPP()" class="space-y-6">
                  <!-- Filter + Toolbar -->
                  <div class="flex flex-wrap items-center justify-between gap-4 bg-purple-50 p-4 rounded-xl shadow">
                     <div class="flex space-x-3">
                        <!-- Dropdown Bulan -->
                        <select id="bulan" x-model="bulan"
                           class="w-40 border rounded-lg px-3 py-2 focus:ring focus:ring-yellow-400">
                           <option value="">-- Bulan --</option>
                           <template x-for="(nama, i) in bulanList" :key="i">
                              <option :value="i+1" x-text="nama"></option>
                           </template>
                        </select>
                        <!-- Dropdown Tahun -->
                        <select x-model="tahun" class="w-32 border rounded-lg px-3 py-2 focus:ring focus:ring-yellow-400">
                           <option value="">-- Tahun --</option>
                           <template x-for="y in tahunList" :key="y">
                              <option :value="y" x-text="y"></option>
                           </template>
                        </select>
                        <!-- Tombol Reload -->
                        <button type="button" @click="loadDaftarPP()"
                           class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition">
                        🔄 Tampilkan
                        </button>
                     </div>
                     <!-- Toolbar -->
                     <div class="flex space-x-2">
                        <button @click="printView()" class="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600">
                        🖨️ Print View
                        </button>
                        <button @click="exportExcel()" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">
                        📊 Export Excel
                        </button>
                        <button @click="exportPDF()" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow hover:bg-red-600">
                        📑 Export PDF
                        </button>
                     </div>
                  </div>
                  <!-- Split view -->
                  <div class="grid grid-cols-2 gap-4">
                     <!-- Header PP (kiri) -->
                     <div class="bg-white rounded-xl shadow overflow-x-auto">
                        <h2 class="text-lg font-semibold px-4 pt-4">📋 Daftar PP</h2>
                        <table class="min-w-full border mt-2">
                           <thead class="bg-purple-100">
                              <tr>
                                 <th class="border px-3 py-2">No</th>
                                 <th class="border px-3 py-2">Nomor</th>
                                 <th class="border px-3 py-2">Tanggal</th>
                              </tr>
                           </thead>
                           <tbody>
                              <template x-for="(h, i) in header" :key="h.nomor">
                                 <tr 
                                    :class="selectedNomor === h.nomor ? 'bg-purple-200 font-semibold' : ''"
                                    class="cursor-pointer hover:bg-purple-100"
                                    @click="selectPP(h.nomor)">
                                    <td class="border px-3 py-2 text-center" x-text="i+1"></td>
                                    <td class="border px-3 py-2" x-text="h.nomor"></td>
                                    <td class="border px-3 py-2" x-text="formatDate(h.tanggal)"></td>
                                 </tr>
                              </template>
                           </tbody>
                        </table>
                     </div>
                     <!-- Detail PP (kanan) -->
                     <div class="bg-white rounded-xl shadow overflow-x-auto">
                        <h2 class="text-lg font-semibold px-4 pt-4">🔎 Detail PP</h2>
                        <template x-if="detail.length > 0">
                           <table id="detailTable" class="min-w-full border mt-2">
                              <thead class="bg-purple-100">
                                 <tr>
                                    <th class="border px-3 py-2">No</th>
                                    <th class="border px-3 py-2">Nama Obat</th>
                                    <th class="border px-3 py-2">Satuan</th>
                                    <th class="border px-3 py-2">Stok Minimal</th>
                                    <th class="border px-3 py-2">Stock Akhir</th>
                                    <th class="border px-3 py-2">Jumlah PP</th>
                                    <th class="border px-3 py-2">Keterangan</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <template x-for="d in detail" :key="d.no_urut">
                                    <tr :class="d.no_urut % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                                       <td class="border px-3 py-2 text-center" x-text="d.no_urut"></td>
                                       <td class="border px-3 py-2" x-text="d.nama_obat"></td>
                                       <td class="border px-3 py-2 text-center" x-text="d.satuan"></td>
                                       <td class="border px-3 py-2 text-right" x-text="d.stok_minimal"></td>
                                       <td class="border px-3 py-2 text-right" x-text="d.stock_akhir"></td>
                                       <td class="border px-3 py-2 text-right" x-text="d.jumlah_pp"></td>
                                       <td class="border px-3 py-2" x-text="d.keterangan"></td>
                                    </tr>
                                 </template>
                              </tbody>
                           </table>
                        </template>
                        <template x-if="detail.length === 0">
                           <p class="p-4 text-gray-500 italic">Pilih salah satu PP dari tabel kiri untuk melihat detail.</p>
                        </template>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- AlpineJS -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery + Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Select2 -->

<script>
   document.addEventListener("DOMContentLoaded", function () {
       let allData = []; // 🔹 simpan semua data hasil fetch
   
       function formatNumber(val, isQty = false) {
           if (val === null || val === undefined) return "";
           let num = Number(val);
           if (isNaN(num) || num === 0) return ""; // kosong kalau 0
   
           return isQty 
               ? num.toLocaleString("id-ID", { minimumFractionDigits: 0, maximumFractionDigits: 0 })
               : num.toLocaleString("id-ID", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
       }
   
       // 🔍 fungsi highlight
       function highlight(text, keyword) {
           if (!keyword) return text;
           let regex = new RegExp(`(${keyword})`, "gi");
           return text.replace(regex, `<span class="bg-yellow-200">$1</span>`);
       }
   
       function renderTable(data, keyword = "") {
           let tbody = document.querySelector("#saldoTable tbody");
   
           if (!data || data.length === 0) {
               tbody.innerHTML = `<tr><td colspan="14" class="text-center py-4 text-gray-500">
                   Tidak ada data
               </td></tr>`;
               return;
           }
   
           let rows = data.map((row, idx) => `
               <tr class="${idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                   <td class="px-2 py-1 border text-center">${idx + 1}</td>
                   <td class="px-2 py-1 border">${highlight(row.kode ?? "", keyword)}</td>
                   <td class="px-2 py-1 border">${highlight(row.nama_obat ?? "", keyword)}</td>
                   <td class="px-2 py-1 border">${highlight(row.satuan ?? "", keyword)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.qty_awal, true)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.nilai_awal)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.hpp_awal)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.qty_masuk, true)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.nilai_masuk)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.qty_keluar, true)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.nilai_keluar)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.qty_akhir, true)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.nilai_akhir)}</td>
                   <td class="px-4 py-2 border text-right">${formatNumber(row.hpp_akhir)}</td>
               </tr>`).join("");
   
           tbody.innerHTML = rows;
       }
   
       function loadData() {   
           let tahun = document.getElementById('tahun').value;
           let lokasi = '{{ session("idpay") }}';
           let bulan = document.getElementById('bulan').value;
           let tbody = document.querySelector("#saldoTable tbody");
   
           tbody.innerHTML = `
           <tr><td colspan="14" class="text-center py-4 text-blue-500 animate-pulse">
               🔄 Sedang memuat data...
           </td></tr>`;
   
           fetch(`{{ route('saldo-obat.data') }}?tahun=${tahun}&lokasi=${lokasi}&bulan=${bulan}&_t=${Date.now()}`, {
               credentials: 'same-origin',
               cache: "no-store"
           })
           .then(res => res.json())
           .then(data => {
               allData = (data || []).sort((a, b) => {
           return (a.nama_obat || "").localeCompare(b.nama_obat || "");
       });
               renderTable(allData);
           })
           .catch(err => {
               console.error(err);
               tbody.innerHTML = `<tr><td colspan="14" class="text-center py-4 text-red-500">
                   ❌ Gagal memuat data
               </td></tr>`;
           });
       }
   
       // 🔍 Event untuk search
       document.getElementById("searchInput").addEventListener("keyup", function () {
           let keyword = this.value.toLowerCase();
           let filtered = allData.filter(row => 
               (row.kode ?? "").toLowerCase().includes(keyword) ||
               (row.nama_obat ?? "").toLowerCase().includes(keyword) ||
               (row.satuan ?? "").toLowerCase().includes(keyword)
           );
           renderTable(filtered, keyword);
       });
   
       // load awal
       loadData();
   
       // reload saat ganti bulan/tahun
       document.getElementById("bulan").addEventListener("change", loadData);
       document.getElementById("tahun").addEventListener("change", loadData);
   
       // sync
       document.getElementById("btnSync").addEventListener("click", function() {
           fetch("{{ route('saldo-obat.sync') }}", {
               method: 'POST',
               headers: {
                   'X-CSRF-TOKEN': '{{ csrf_token() }}',
                   'Content-Type': 'application/json'
               },
               credentials: 'same-origin'
           })
           .then(res => res.json())
           .then(resp => {
               if(resp.success){
                   alert(resp.message);
                   loadData();
               } else {
                   alert("Gagal: " + resp.message);
               }
           })
           .catch(err => console.error(err));
       });
   });
   
   
   document.getElementById("btnExport").addEventListener("click", function () {
       let bulan = document.getElementById("bulan").value || new Date().getMonth() + 1;
       let tahun = document.getElementById("tahun").value || new Date().getFullYear();
   
       // arahkan ke route export dengan query string
       window.location.href = `{{ route('saldo-obat.export') }}?bulan=${bulan}&tahun=${tahun}`;
   });
</script>
<script>
   document.addEventListener("DOMContentLoaded", function() {
       $(document).ready(function() {
       // Aktifkan select2
       $('.obat-lookup').select2({
           placeholder: "-- Pilih Obat --",
           allowClear: true,
           width: '100%' // biar full panjang w-72
       });
   });
   
       // Ketika obat dipilih → isi kode + satuan
       $(document).on("change", ".obat-lookup", function() {
           let option = $(this).find("option:selected");
           let kode = option.val();
           let satuan = option.data("satuan") || "";
   
           $(this).closest("td").find(".kode-obat-hidden").val(kode);
           $(this).closest("tr").find(".satuan-input").val(satuan);
       });
   
       // Hitung otomatis
       $(document).on("input", ".qty-input, .harga-input", function() {
           let row = $(this).closest("tr");
           let qty = parseFloat(row.find(".qty-input").val()) || 0;
           let harga = parseFloat(row.find(".harga-input").val()) || 0;
           row.find(".jumlah-input").val(qty * harga);
       });
   
       // Kalau jumlah diubah manual → update harga
       $(document).on("input", ".jumlah-input", function() {
           let row = $(this).closest("tr");
           let qty = parseFloat(row.find(".qty-input").val()) || 0;
           let jumlah = parseFloat($(this).val()) || 0;
           if (qty > 0) {
               row.find(".harga-input").val(jumlah / qty);
           }
       });
   });
   
   
   document.addEventListener('DOMContentLoaded', function () {
       const tbody = document.getElementById('obatBody');
       const addBtn = document.getElementById('addRow');
   
       // Fungsi untuk mereset nilai input/select di sebuah row
       function resetRowValues(row) {
       row.querySelectorAll('input').forEach(input => {
           if (['number','text','hidden','date'].includes(input.type)) {
               input.value = '';
           }
       });
       row.querySelectorAll('select').forEach(sel => {
           sel.value = ''; // reset ke default (– Pilih Obat –)
       });
       const s = row.querySelector('.satuan-input');
       if (s) s.value = '';
   }
   
   
       // Tambah baris
   addBtn.addEventListener('click', function () {
       const firstRow = tbody.querySelector('tr');
       if (!firstRow) return;
   
       // clone row tanpa select2 container
       const newRow = firstRow.cloneNode(true);
   
       // bersihkan select2 container yang ikut ke-clone
       $(newRow).find('.select2-container').remove();
   
       // reset nilai input & select
       resetRowValues(newRow);
   
       // append ke tabel
       tbody.appendChild(newRow);
   
       // re-init Select2 hanya di select baru
       $(newRow).find('.obat-lookup').select2({
           placeholder: "-- Pilih Obat --",
           allowClear: true,
           width: '100%'
       });
   
   
           // fokus ke select obat di baris baru (opsional)
           const sel = newRow.querySelector('select.obat-lookup');
           if (sel) sel.focus();
       });
   
       // Hapus baris (delegation)
       tbody.addEventListener('click', function (ev) {
           if (ev.target && ev.target.matches('.remove-row')) {
               const rows = tbody.querySelectorAll('tr');
               if (rows.length <= 1) {
                   // kalau tinggal 1 baris, jangan hapus, cukup reset
                   resetRowValues(rows[0]);
                   return;
               }
               ev.target.closest('tr').remove();
           }
       });
   
       // Delegated: change pada select obat -> isi kode_hidden & satuan
       tbody.addEventListener('change', function (ev) {
           const target = ev.target;
           if (target && target.matches('select.obat-lookup')) {
               const sel = target;
               const option = sel.selectedOptions[0];
               const kode = option ? option.value : '';
               const satuan = option ? option.dataset.satuan || '' : '';
   
               const row = sel.closest('tr');
               if (row) {
                   const hidden = row.querySelector('.kode-obat-hidden');
                   if (hidden) hidden.value = kode;
                   const sInput = row.querySelector('.satuan-input');
                   if (sInput) sInput.value = satuan;
               }
           }
       });
   
       // Delegated: input untuk qty/harga/jumlah
       tbody.addEventListener('input', function (ev) {
           const t = ev.target;
           const row = t.closest('tr');
           if (!row) return;
   
           const qtyInput = row.querySelector('.qty-input');
           const hargaInput = row.querySelector('.harga-input');
           const jumlahInput = row.querySelector('.jumlah-input');
   
           // If qty or harga changed -> update jumlah
           if (t.matches('.qty-input') || t.matches('.harga-input')) {
               const qty = parseFloat(qtyInput.value) || 0;
               const harga = parseFloat(hargaInput.value) || 0;
               const jumlah = qty * harga;
               // jika 0, kosongkan agar lebih rapi
               jumlahInput.value = jumlah ? (Math.round((jumlah + Number.EPSILON) * 100) / 100) : '';
           }
   
           // If jumlah changed -> update harga (harga = jumlah / qty)
           if (t.matches('.jumlah-input')) {
               const qty = parseFloat(qtyInput.value) || 0;
               const jumlah = parseFloat(jumlahInput.value) || 0;
               if (qty > 0) {
                   const harga = jumlah / qty;
                   hargaInput.value = isFinite(harga) ? (Math.round((harga + Number.EPSILON) * 100) / 100) : '';
               } else {
                   // jika qty 0, jangan isi harga (hindari NaN)
                   hargaInput.value = '';
               }
           }
       });
   
       // Tombol clear: hapus semua baris, sisakan 1 kosong
   document.getElementById('clearRows').addEventListener('click', function () {
       // ambil tbody
       const rows = tbody.querySelectorAll('tr');
       if (rows.length === 0) return;
   
       // ambil row pertama sebagai template
       const firstRow = rows[0].cloneNode(true);
   
       // bersihkan select2 container (biar tidak double)
       $(firstRow).find('.select2-container').remove();
   
       // reset nilai input/select
       resetRowValues(firstRow);
   
       // kosongkan tbody lalu append row kosong
       tbody.innerHTML = '';
       tbody.appendChild(firstRow);
   
       // re-init select2 di baris kosong
       $(firstRow).find('.obat-lookup').select2({
           placeholder: "-- Pilih Obat --",
           allowClear: true,
           width: '100%'
       });
   });
   });
</script>
<script>
   $('#tgl_masuk').on('change', function () {
       let tgl = $(this).val();
   
       $.get("{{ route('transaksi.generate') }}", { tanggal: tgl }, function (data) {
           if (data.noTransaksi) {
               $('#no_transaksi').val(data.noTransaksi);
           } else {
               $('#no_transaksi').val('');
           }
       });
   });
</script>
<!--obat keluar-->
<script>
   document.addEventListener('DOMContentLoaded', function () {
       const tbodyKeluar = document.getElementById('obatKeluarBody');
       const addBtnKeluar = document.getElementById('addRowKeluar');
   
       // 🔹 Reset value baris baru
       function resetRowValuesKeluar(row) {
           row.querySelectorAll('input').forEach(input => {
               if (['number','text','hidden','date'].includes(input.type)) {
                   input.value = '';
               }
           });
           row.querySelectorAll('select').forEach(sel => {
               sel.value = '';
           });
           const s = row.querySelector('.satuan-input');
           if (s) s.value = '';
       }
   
       // 🔹 Tambah baris
       addBtnKeluar.addEventListener('click', function () {
           const firstRow = tbodyKeluar.querySelector('tr');
           if (!firstRow) return;
   
           const newRow = firstRow.cloneNode(true);
           $(newRow).find('.select2-container').remove();
           resetRowValuesKeluar(newRow);
   
           tbodyKeluar.appendChild(newRow);
   
           // re-init select2 utk baris baru
           $(newRow).find('.obat-lookupLK').select2({
               placeholder: "-- Pilih Obat --",
               allowClear: true,
               width: '100%'
           });
       });
   
       // 🔹 Hapus baris
       tbodyKeluar.addEventListener('click', function (ev) {
           if (ev.target && ev.target.matches('.remove-row')) {
               const rows = tbodyKeluar.querySelectorAll('tr');
               if (rows.length <= 1) {
                   resetRowValuesKeluar(rows[0]);
                   return;
               }
               ev.target.closest('tr').remove();
           }
       });
   
       // 🔹 Change obat → isi kode & satuan
       tbodyKeluar.addEventListener('change', function (ev) {
           if (ev.target && ev.target.matches('select.obat-lookupLK')) {
               const sel = ev.target;
               const option = sel.selectedOptions[0];
               const kode = option ? option.value : '';
               const satuan = option ? option.dataset.satuan || '' : '';
   
               const row = sel.closest('tr');
               if (row) {
                   const hidden = row.querySelector('.kode-obat-hidden');
                   if (hidden) hidden.value = kode;
                   const sInput = row.querySelector('.satuan-input');
                   if (sInput) sInput.value = satuan;
               }
           }
       });
   
       // 🔹 Hitung qty × harga → jumlah
       tbodyKeluar.addEventListener('input', function (ev) {
           const row = ev.target.closest('tr');
           if (!row) return;
   
           const qtyInput = row.querySelector('.qty-input');
           const hargaInput = row.querySelector('.harga-input');
           const jumlahInput = row.querySelector('.jumlah-input');
   
           if (ev.target.matches('.qty-input') || ev.target.matches('.harga-input')) {
               const qty = parseFloat(qtyInput.value) || 0;
               const harga = parseFloat(hargaInput.value) || 0;
               jumlahInput.value = qty && harga ? (qty * harga).toFixed(2) : '';
           }
   
           if (ev.target.matches('.jumlah-input')) {
               const qty = parseFloat(qtyInput.value) || 0;
               const jumlah = parseFloat(jumlahInput.value) || 0;
               hargaInput.value = qty > 0 ? (jumlah / qty).toFixed(2) : '';
           }
       });
   
   });
</script>
<script>
   $('#tgl_keluar').on('change', function () {
       let tgl = $(this).val();
   
       $.get("{{ route('transaksi.generateLK') }}", { tanggal: tgl }, function (data) {
   if (data.no_transaksi_keluar) {
       $('#no_transaksi_keluar').val(data.no_transaksi_keluar);
   } else {
       $('#no_transaksi_keluar').val('');
   }
       });
   });
</script>
<!-- Combo/LookUp Nama Pasien -->
<script>
   $(document).ready(function () {
       // Load data pasien ketika halaman dibuka
       $.get("{{ route('pasien.hariIni') }}", function (data) {
           let pasienSelect = $('#pasien');
           pasienSelect.empty().append('<option value="nama_pasien">-- Pilih Pasien --</option>');
   
           data.forEach(function (item) {
               pasienSelect.append(
                   `<option value="${item.nama}" 
                       data-norm="${item.no_rm}"
                       data-nokunjungan="${item.no_kunjungan}">
                       ${item.nama} (${item.no_kunjungan})
                   </option>`
               );
           });
       });
   
       // Update nomor RM ketika pasien dipilih
       $('#pasien').on('change', function () {
           let noRM = $(this).find(':selected').data('norm') || '';
           let noKunjungan = $(this).find(':selected').data('nokunjungan') || '';
           $('input[name="no_rm"]').val(noRM);
           $('input[name="no_kunjungan"]').val(noKunjungan); // hidden input
       });
       
   });
   
   
</script>
<script>
   document.getElementById('clearRowsLK').addEventListener('click', function () {
       const tbody = document.getElementById('obatKeluarBody');
       const rows = tbody.querySelectorAll('tr');
       if (rows.length === 0) return;
   
       // clone row pertama
       const firstRow = rows[0].cloneNode(true);
   
       // hapus select2 container kalau ada
       $(firstRow).find('.select2-container').remove();
   
       // reset semua input di baris
       resetRowValues(firstRow);
   
       // kosongkan tbody
       tbody.innerHTML = '';
       tbody.appendChild(firstRow);
   
       // re-init select2
       $(firstRow).find('.obat-lookupLK').select2({
           placeholder: "-- Pilih Obat --",
           allowClear: true,
           width: '100%'
       });
   });
   
   // Fungsi reset value input/select
   function resetRowValues(row) {
       // reset select obat
       const select = row.querySelector('.obat-lookupLK');
       if (select) select.value = '';
   
       // reset hidden kode_obat
       const hidden = row.querySelector('.kode-obat-hidden');
       if (hidden) hidden.value = '';
   
       // reset input lainnya
       row.querySelectorAll('input').forEach(input => {
           if (input.type === 'number' || input.type === 'text') {
               input.value = '';
           }
       });
   }
   
  
    // SIMPAN DATA OBAT KELUAR
    // ========================
    document.addEventListener('DOMContentLoaded', function () {
        const formLK = document.querySelector("form[action='{{ route('stock-obat.storeLK') }}']");
        if (!formLK) return; // jaga-jaga kalau elemen belum ada

        const errorDiv = document.getElementById('errorLK');

        formLK.addEventListener('submit', async function (e) {
            e.preventDefault();
            errorDiv.textContent = ""; // reset error

            const submitBtn = formLK.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = "💾 Menyimpan...";

            // 🔹 Pastikan semua <select class="obat-lookupLK"> mengisi input hidden [name="kode_obat[]"]
            document.querySelectorAll('#tableKeluar tbody tr').forEach(tr => {
                const select = tr.querySelector('.obat-lookupLK');
                const hiddenKode = tr.querySelector('.kode-obat-hidden');
                if (select && hiddenKode) hiddenKode.value = select.value;
            });

            const formData = new FormData(formLK);

            try {
                const res = await fetch(formLK.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": formLK.querySelector('input[name="_token"]').value
                    },
                    body: formData
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const resp = await res.json();

                if (resp.success) {
                    alert(resp.message);
                    document.getElementById('clearRowsLK').click(); // bersihkan baris
                    window.location.reload(); // reload halaman agar nomor baru muncul
                } 
                else if (resp.errors) {
                    // tampilkan validasi laravel
                    const messages = Object.entries(resp.errors)
                        .map(([key, val]) => `${key}: ${val.join(", ")}`)
                        .join("<br>");
                    errorDiv.innerHTML = messages;
                } 
                else if (resp.error) {
                    errorDiv.innerHTML = `
                        <b>Error:</b> ${resp.error}<br>
                        <b>File:</b> ${resp.file}<br>
                        <b>Line:</b> ${resp.line}
                    `;
                }
            } catch (err) {
                console.error("Fetch error:", err);
                errorDiv.textContent = "Terjadi kesalahan koneksi, coba lagi.";
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = "💾 Simpan";
            }
        });
    });

   
   
   //dropdown obat keluar
   $('#tgl_keluar').on('change', function () {
       let tanggal = $(this).val(); // format yyyy-mm-dd dari datetimepicker
       if (!tanggal) return;
   
       let parts = tanggal.split('-');
       let tahun = parts[0];
       let bulan = parts[1];
       let lokasi = "{{ session('idpay') }}";
   
       $.get(`/get-obat/${tahun}/${bulan}/${lokasi}`, function (data) {
           // loop semua dropdown obat (setiap baris tabel)
           $('.obat-lookupLK').each(function () {
               let selectObat = $(this);
               selectObat.empty().append('<option value="">-- Pilih Obat --</option>');
   
               data.forEach(function (item) {
                   selectObat.append(
                       `<option value="${item.kode}" 
                                data-satuan="${item.satuan}" 
                                data-harga="${item.harga}" 
                                data-saldo="${item.saldo}">
                           ${item.nama_obat} (Saldo: ${item.saldo})
                        </option>`
                   );
               });
           });
       });
   });
   
   // saat user pilih obat → isi kolom terkait
   $(document).on('change', '.obat-lookupLK', function () {
       let satuan = $(this).find(':selected').data('satuan') || '';
       let harga = parseFloat($(this).find(':selected').data('harga')) || 0;
       let saldo = $(this).find(':selected').data('saldo') || 0;
   
       let row = $(this).closest('tr');
       row.find('.satuan-input').val(satuan); // isi kolom satuan
       row.find('.harga-input').val(harga.toFixed(2));   // isi kolom harga
       //row.find('.saldo-input').val(saldo);   // kalau ada saldo
   });
   
   $(document).ready(function () {
       // inisialisasi select2 pada dropdown obat
       $('.obat-lookupLK').select2({
           placeholder: "-- Pilih Obat --",
           allowClear: true,
           width: '100%'  // agar lebar sesuai elemen
       });
   });
   
   addBtn.addEventListener('click', function () {
       const firstRow = tbody.querySelector('tr');
       if (!firstRow) return;
   
       const newRow = firstRow.cloneNode(true);
       $(newRow).find('.select2-container').remove();
       resetRowValues(newRow);
   
       tbody.appendChild(newRow);
   
       // inisialisasi select2 pada elemen .obat-lookup di row baru
       $(newRow).find('.obat-lookupLK').select2({
           placeholder: "-- Pilih Obat --",
           allowClear: true,
           width: '100%'
       });
   });
</script>
<script>
   document.addEventListener('alpine:init', () => {
       // store untuk menampung rows di Format PP
       Alpine.store('pp', {
           formatRows: [],
           save() {
               // contoh: kirim ke server
               console.log('Simpan ke server:', this.formatRows);
               // implementasi POST di sini
           },
           removeSelected() {
               // hapus rows yang jumlah_pp == null atau 0? atau yang ditandai?
               // contoh hapus yang jumlah_pp falsy
               this.formatRows = this.formatRows.filter(r => r.jumlah_pp && Number(r.jumlah_pp) > 0);
           },
           clear() {
               this.formatRows = [];
           },
           view() {
               console.log('Lihat PP:', this.formatRows);
               alert('Cek console untuk data PP (lihat devtools).');
           }
       });
   
       Alpine.data('infoMinimal', () => ({
       bulan: String(new Date().getMonth() + 1).padStart(2, '0'),
       tahun: new Date().getFullYear(),
       bulanList: {
           1:'Januari',2:'Februari',3:'Maret',4:'April',5:'Mei',6:'Juni',
           7:'Juli',8:'Agustus',9:'September',10:'Oktober',11:'November',12:'Desember'
       },
       tahunList: Array.from({length: (new Date().getFullYear() + 1 - 2025 + 1)}, (_, i) => 2025 + i),
       golonganList: [],           // daftar golongan dari server
       selectedGolongan: '',       // id_gol yang dipilih
       data: [],                   // data asli dari server
       filteredData: [],           // data yang sudah difilter
   
       init() {
           this.loadGolongan();    // load list golongan
           this.loadData();        // load data infoMinimal
       },
   
       loadGolongan() {
           fetch('{{ route("Transaksi.listGolongan") }}') // buat route baru untuk ambil list golongan
               .then(res => res.json())
               .then(json => {
                   this.golonganList = json || [];
               })
               .catch(err => console.error(err));
       },
   
       loadData() {
           const url = '{{ route("Transaksi.infoMinimalData") }}';
           fetch(`${url}?bulan=${this.bulan}&tahun=${this.tahun}`)
               .then(res => res.json())
               .then(json => {
                   this.data = (json || []).map(r => ({
                       ...r,
                       checklist: (r.checklist === true || r.checklist === 't' || r.checklist === 1 || r.checklist === '1')
                   }));
                   this.applyFilter(); // terapkan filter golongan awal
               })
               .catch(err => {
                   console.error(err);
                   this.data = [];
                   this.filteredData = [];
               });
       },
   
       applyFilter() {
           if (this.selectedGolongan) {
               this.filteredData = this.data.filter(r => r.golongan === this.selectedGolongan);
           } else {
               this.filteredData = [...this.data];
           }
       },
   
       // dipanggil saat dropdown golongan berubah
       onGolonganChange() {
           this.applyFilter();
       },
   
       transferToPP() {
           const selected = this.filteredData.filter(r => r.checklist);
           if (selected.length === 0) {
               alert('Pilih minimal satu item dengan checklist.');
               return;
           }
           const store = Alpine.store('pp');
           selected.forEach(r => {
               if (!store.formatRows.find(fr => fr.kode_obat === r.kode_obat)) {
                   store.formatRows.push({
                       kode_obat: r.kode_obat,
                       nama_obat: r.nama_obat,
                       satuan: r.satuan,
                       stock_akhir: r.stock_akhir,
                       jumlah_pp: null
                   });
               }
           });
           this.$dispatch('pp-transferred');
       }
   }));
   
   });
   
   
   function hapusBaris() {
       const targetBody = document.querySelector('#tabelFormatPP tbody');
       const selected = targetBody.querySelectorAll('.row-check:checked');
       
       selected.forEach(cb => {
           cb.closest('tr').remove();
       });
   }
   
   
   function simpanPP() { 
       const rows = document.querySelectorAll('#tabelFormatPP tbody tr');
       const data = [];
   
       rows.forEach(row => {
           data.push({
               kode: row.cells[1].innerText.trim(),
               nama: row.cells[2].innerText.trim(),
               satuan: row.cells[3].innerText.trim(),
               stock: row.cells[4].innerText.trim(),
               jumlah_pp: row.cells[5].querySelector('input').value
           });
       });
   
       const nomor = document.querySelector('#no_pp').value;
       const tanggal = document.querySelector('#tgl_pp').value;
   
       fetch('/transaksi/pp/simpan', {
           method: 'POST',
           headers: {
               'Content-Type': 'application/json',
               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
           },
           body: JSON.stringify({
               nomor,
               tanggal,
               data
           })
       })
       .then(res => res.json())
       .then(res => {
           if (res.success) {
               alert(res.message);
               document.querySelector('#tabelFormatPP tbody').innerHTML = '';
           } else {
               alert('Gagal simpan PP!');
           }
       })
       .catch(err => console.error(err));
   }
   
   // Konversi bulan ke romawi
   function toRoman(month) {
       const romans = ["I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII"];
       return romans[month - 1] || "";
   }
   
   // Generate nomor PP
   function generateNoPP() {
       const noUrut = document.getElementById("no_urut").value;
       const tglPP = document.getElementById("tgl_pp").value;

   
       if (noUrut && tglPP) {
           // parsing value yyyy-mm-dd
           const parts = tglPP.split("-");
           const year = parseInt(parts[0], 10);
           const month = parseInt(parts[1], 10);
           const idpaypp = "{{ session('idpay') }}";
           const lokasipp = idpaypp.slice(-3); // ambil 3 karakter terakhir
            console.log(lokasipp);
           const bulanRomawi = toRoman(month);
   
           document.getElementById("no_pp").value =
               `${noUrut}/PP-Klinik/${lokasipp}/${bulanRomawi}/${year}`;
       } else {
           document.getElementById("no_pp").value = "";
       }
   }
   
</script>
<script>
   flatpickr("#tgl_pp", {
     altInput: true,        // tampilkan format cantik di input
     altFormat: "d/m/Y",    // yang ditampilkan user
     dateFormat: "Y-m-d",   // value asli (agar JS new Date() tidak error)
     allowInput: false,      // user tidak bisa ketik manual, wajib pilih dari kalender
     onChange: generateNoPP
   });
   
   // Event listener
   document.getElementById("no_urut").addEventListener("input", generateNoPP);
   document.getElementById("tgl_pp").addEventListener("change", generateNoPP);
   
   
</script>

<script>
   function daftarPP() {
       return {
           bulan: '',
           tahun: '',
           bulanList: [
               'Januari','Februari','Maret','April','Mei','Juni',
               'Juli','Agustus','September','Oktober','November','Desember'
           ],
           tahunList: [],
           header: [],
           allDetail: [],
           detail: [],
           selectedNomor: null,
   
           init() {
               let thn = new Date().getFullYear();
               this.tahunList = [thn, thn + 1];
           },
   
           async loadDaftarPP() {
           if (!this.bulan || !this.tahun) {
               alert("Pilih bulan dan tahun dulu!");
               return;
           }
   
           try {
               let res = await fetch(`/api/daftar-pp?bulan=${this.bulan}&tahun=${this.tahun}`);
               let data = await res.json();
               this.header = data.header ?? [];
               this.detail = [];
               this.selectedNomor = null;
           } catch (e) {
               alert("Gagal memuat daftar PP");
               console.error(e);
           }
       },
   
           selectPP(nomor) {
           this.selectedNomor = nomor;
           this.detail = [];
   
           fetch(`/api/detail-pp?nomor=${nomor}`)
               .then(r => r.json())
               .then(data => {
                   console.log("Response detail:", data);
               this.detail = Array.isArray(data) ? data : (data.detail ?? []);
               })
               .catch(err => console.error(err));
       },
   
           formatDate(dt) {
               if (!dt) return '';
               let d = new Date(dt);
               return d.toLocaleDateString('id-ID');
           },
   
           // 🔹 CETAK
           printView() {
           let tableEl = document.getElementById("detailTable");
           if (!tableEl) return alert("Tidak ada data untuk dicetak");
   
           let bulanText = this.bulanList[this.bulan - 1] || '';
           let tahunText = this.tahun || '';
           let periode = bulanText && tahunText ? `${bulanText} ${tahunText}` : '';
           let lokasi = "{{ session('lokasi') }}";
           let nomorPP = this.selectedNomor || '-';
   
           let headerInfo = `
               <div style="text-align:left; font-weight:bold;">PP Klinik - ${lokasi}</div>
               <div style="text-align:left;">Periode : ${periode}</div>
               <div style="text-align:left;">Golongan : </div>
               <div style="text-align:left; font-weight:bold;">Nomor : ${nomorPP}</div>
               <br>
           `;
   
           let style = `
               <style>
                   table { border-collapse: collapse; width:100%; }
                   th, td { border: 1px solid #ccc; padding: 5px; }
                   th { background: #f0f0f0; }
                   td { text-align: left; }
                   td:nth-child(1) { text-align: center; } /* No. di tengah */
               </style>
           `;
   
           let html = `<html><head>${style}</head><body>${headerInfo}${tableEl.outerHTML}</body></html>`;
   
           let w = window.open("", "_blank");
           w.document.write(html);
           w.document.close();
           w.print();
       },
   
           exportExcel() {
           let tableEl = document.getElementById("detailTable");
   
           // ambil thead & tbody tanpa baris kosong
           let head = tableEl.tHead ? tableEl.tHead.outerHTML : "";
           let bodyRows = Array.from(tableEl.tBodies[0].rows)
           .filter(r => r.innerText.trim() !== "")
           .map(r => {
               let cells = Array.from(r.cells).map((c, idx) => {
                   if (idx === 4) { // misal stok_akhir ada di kolom ke-5
                       let val = c.innerText.trim();
                   if (val === "0" || val === "0,00") {
                       return `<td style="text-align:right;">-</td>`;
                   }
                   return `<td style="mso-number-format:'0'; text-align:right;">${val}</td>`;
               }
               return c.outerHTML;
           });
           return `<tr>${cells.join("")}</tr>`;
       })
       .join("");
   
   
       let table = `<table>${head}<tbody>${bodyRows}</tbody></table>`;
   
       // ambil nilai bulan & tahun dari dropdown
       let bulanText = this.bulanList[this.bulan - 1] || '';
       let tahunText = this.tahun || '';
       let periode = bulanText && tahunText ? `${bulanText} ${tahunText}` : '';
       let lokasi = "{{ session('lokasi') }}";
       let nomorPP = this.selectedNomor || '-';
   
       // header tambahan
       let headerInfo = `
           <div style="text-align:left; font-weight:bold;">PP Klinik - ${lokasi}</div>
           <div style="text-align:left;">Periode : ${periode}</div>
           <div style="text-align:left;">Golongan : </div>
           <div style="text-align:left; font-weight:bold;">Nomor : ${nomorPP}</div>
       `;
   
       // CSS untuk border & gridline
       let style = `
           <style>
               table {
                   border-collapse: collapse;
                   table-layout: auto;
               }
               table, th, td {
                   border: 1px solid #ccc;
                   padding: 5px;
                   white-space: nowrap;
               }
               th {
                   background: #e6e6e6;
               }
               td:first-child, th:first-child {
                   text-align: center;
               }
           </style>
       `;
   
       let html = `
           <html>
           <head>${style}</head>
           <body>
               ${headerInfo}
               ${table}
           </body>
           </html>
       `;
   
       let blob = new Blob([html], { type: "application/vnd.ms-excel" });
       let url = URL.createObjectURL(blob);
   
       let a = document.createElement("a");
       a.href = url;
       a.download = "Daftar_PP.xls";
       a.click();
   
       URL.revokeObjectURL(url);
   },
   
   
           // 🔹 EXPORT PDF (butuh jsPDF + autotable)
           exportPDF() {
           if (this.detail.length === 0) {
               alert("Tidak ada data untuk diexport!");
               return;
           }
   
           const { jsPDF } = window.jspdf;
           const doc = new jsPDF();
   
           let bulanText = this.bulanList[this.bulan - 1] || '';
           let tahunText = this.tahun || '';
           let periode = bulanText && tahunText ? `${bulanText} ${tahunText}` : '';
           let lokasi = "{{ session('lokasi') }}";
           let nomorPP = this.selectedNomor || '-';
   
           // Header PDF
           let y = 10;
           doc.setFontSize(12);
           doc.text(`PP Klinik - ${lokasi}`, 14, y); y += 7;
           doc.text(`Periode : ${periode}`, 14, y); y += 7;
           doc.text(`Golongan :`, 14, y); y += 7;
           doc.text(`Nomor : ${nomorPP}`, 14, y); y += 10;
   
           // Tabel
           doc.autoTable({
               startY: y,
               head: [["No", "Nama Obat", "Satuan", "Stok Minimal", "Stock Akhir", "Jumlah PP", "Keterangan"]],
               body: this.detail.map((d,i) => [
                   i+1,
                   d.nama_obat,
                   d.satuan,
                   d.stok_minimal,
                   d.stock_akhir === 0 ? '-' : d.stock_akhir,
                   d.jumlah_pp,
                   d.keterangan ?? ''
               ]),
               headStyles: { fillColor: [220, 220, 220] },
               styles: { fontSize: 10, cellPadding: 3 },
               columnStyles: {
                   0: { halign: 'center' }, // No. di tengah
                   4: { halign: 'right' },   // Stock Akhir kanan
                   3: { halign: 'right' },   // Stok Minimal kanan
                   5: { halign: 'right' }    // Jumlah PP kanan
               },
           });
   
           doc.save(`Daftar_PP_${nomorPP}.pdf`);
       }
   
       }
   }
   
</script>

<script>
window.hapusDataKeluar = async function(nomor) {
   if (!confirm(`Yakin hapus data dengan nomor: ${nomor}?`)) return;

   try {
      const encodedNomor = encodeURIComponent(nomor);
      const url = `/explore/keluar/delete/${encodedNomor}`;

      console.log('Mengirim DELETE ke:', url);

      const res = await fetch(`/explore/keluar/delete/${encodeURIComponent(nomor)}`, {
         method: 'DELETE',
         headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
         },
         credentials: 'same-origin' // pastikan cookie session dikirim
      });

      console.log('Response status:', res.status);

      let text;
      try {
         text = await res.text();
         // coba parse json jika bisa
         try { 
            const json = JSON.parse(text);
            console.log('Response JSON:', json);
         } catch(e) {
            console.log('Response text:', text);
         }
      } catch (e) {
         console.error('Gagal membaca body response', e);
      }

      if (!res.ok) {
         // tampilkan pesan spesifik
         if (res.status === 403) alert('403 Forbidden — kemungkinan CSRF token salah atau akses ditolak.');
         else if (res.status === 419) alert('419 CSRF token mismatch / session expired. Silakan reload halaman lalu coba lagi.');
         else if (res.status === 404) alert('404 Not Found — cek route di server.');
         else if (res.status >= 500) alert('Server error (' + res.status + '). Cek log laravel (storage/logs/laravel.log).');
         else alert('Gagal menghapus data. Status: ' + res.status + '. Lihat console untuk detail respon.');
         return;
      }

      // kalau sukses
      const data = text ? JSON.parse(text) : { success: true };
      if (data.success) {
         alert('✅ Data berhasil dihapus');
         // hapus dari tampilan jika ada headerKeluar di scope Alpine
         try {
            // jika tombol dipanggil di dalam Alpine component, this tidak menunjuk ke component—jadi coba update global DOM:
            document.querySelectorAll('td.font-mono').forEach(td => {
               if (td.innerText.trim() === nomor) {
                  const tr = td.closest('tr');
                  if (tr) tr.remove();
               }
            });
         } catch(e){ console.warn(e) }
      } else {
         alert('Gagal menghapus data: ' + (data.error ?? JSON.stringify(data)));
      }

   } catch (err) {
      console.error('Fetch error:', err);
      alert('Terjadi kesalahan saat menghapus data (cek console untuk detail).');
   }
}
</script>

<script>
async function hapusDataMasuk(nomor) {
    if (confirm('Yakin ingin menghapus data MASUK dengan nomor ' + nomor + '?')) {
        try {
            const res = await fetch(`/explore/masuk/delete/${encodeURIComponent(nomor)}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            });

            const data = await res.json();

            if (data.success) {
                alert('✅ Data MASUK berhasil dihapus');

                // cari dan hapus baris tabel tanpa reload
                const row = document.querySelector(`tr[data-nomor="${CSS.escape(nomor)}"]`);
                if (row) row.remove();
            } else {
                alert('⚠️ ' + (data.error || 'Gagal menghapus data MASUK'));
            }
        } catch (err) {
            alert('Terjadi kesalahan: ' + err.message);
        }
    }
}
</script>

@endsection