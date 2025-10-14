@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-xl">

    <h1 class="text-3xl font-bold text-center mb-6 bg-gradient-to-r from-blue-400 to-blue-600 text-white py-3 rounded-xl shadow-lg">
        🩺 MyClinis - Data Kunjungan
    </h1>

    <!-- Tabs -->
    <div x-data="{ tab: 'kunjungan' }" class="mb-6">
        <ul class="flex space-x-2 text-white text-lg font-semibold">
            <li>
                <button 
                    :class="tab === 'kunjungan' ? 'bg-blue-600 shadow-lg' : 'bg-blue-300'"
                    class="px-4 py-2 rounded-t-lg transition-all"
                    @click="tab = 'kunjungan'">
                    📝 Data Kunjungan
                </button>
            </li>
            <li>
                <button 
                    :class="tab === 'pendaftaran' ? 'bg-blue-600 shadow-lg' : 'bg-blue-300'"
                    class="px-4 py-2 rounded-t-lg transition-all"
                    @click="tab = 'pendaftaran'">
                    📋 Data Pendaftaran
                </button>
            </li>
            <li>
                <button 
                    :class="tab === 'explore' ? 'bg-blue-600 shadow-lg' : 'bg-blue-300'"
                    class="px-4 py-2 rounded-t-lg transition-all"
                    @click="tab = 'explore'">
                    🔎 Explore Data
                </button>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="mt-4">

            <!-- Tab Data Kunjungan -->
            <div x-show="tab === 'kunjungan'" x-transition class="space-y-4">
                <div x-data="kunjunganForm()" class="space-y-4">

                    <!-- Grid Entry (Atas) -->
                    <div class="p-4 bg-blue-50 rounded-lg shadow-inner">
                        <h2 class="font-bold mb-2">📋 Entry Kunjungan</h2>
                        <button @click="addRow()" class="px-3 py-1 bg-blue-600 text-white rounded mb-2">Tambah Baris</button>

                        <table class="w-full border border-gray-300 table-auto">
                            <thead class="bg-blue-100">
                                <tr>
                                    <th class="border px-2 py-1">#</th>
                                    <th class="border px-2 py-1">Member</th>
                                    <th class="border px-2 py-1">No RM</th>
                                    <th class="border px-2 py-1">Jenis Kunjungan</th>
                                    <th class="border px-2 py-1">Poli</th>
                                    <th class="border px-2 py-1">Payment</th>
                                    <th class="border px-2 py-1">Tanggal</th>
                                    <th class="border px-2 py-1">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row,index) in rows" :key="index">
                                    <tr>
                                        <td class="border px-2 py-1" x-text="index+1"></td>
                                        <td class="border px-2 py-1">
                                            <select x-model="row.member" class="w-full border rounded px-1 py-1">
                                                <option value="">-- Pilih Member --</option>
                                                <option value="Member A">Member A</option>
                                                <option value="Member B">Member B</option>
                                                <option value="Member C">Member C</option>
                                            </select>
                                        </td>
                                        <td class="border px-2 py-1">
                                            <input type="text" x-model="row.no_rm" class="w-full border rounded px-1 py-1" placeholder="No RM">
                                        </td>
                                        <td class="border px-2 py-1">
                                            <select x-model="row.jenis" class="w-full border rounded px-1 py-1">
                                                <option value="">-- Jenis Kunjungan --</option>
                                                <option value="Umum">Umum</option>
                                                <option value="Gigi">Gigi</option>
                                                <option value="KIA">KIA</option>
                                            </select>
                                        </td>
                                        <td class="border px-2 py-1">
                                            <select x-model="row.poli" class="w-full border rounded px-1 py-1">
                                                <option value="">-- Poli --</option>
                                                <option value="Poli A">Poli A</option>
                                                <option value="Poli B">Poli B</option>
                                            </select>
                                        </td>
                                        <td class="border px-2 py-1">
                                            <select x-model="row.payment" class="w-full border rounded px-1 py-1">
                                                <option value="">-- Payment --</option>
                                                <option value="Cash">Cash</option>
                                                <option value="BPJS">BPJS</option>
                                            </select>
                                        </td>
                                        <td class="border px-2 py-1">
                                            <input type="date" x-model="row.tanggal" class="w-full border rounded px-1 py-1">
                                        </td>
                                        <td class="border px-2 py-1 text-center">
                                            <button @click="removeRow(index)" class="px-2 py-1 bg-red-600 text-white rounded">Hapus</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <button @click="saveRows()" class="mt-3 px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
                    </div>

                    <!-- Grid Saved (Bawah) -->
                    <div class="p-4 bg-white rounded-lg shadow-lg">
                        <h2 class="font-bold mb-2">💾 Data Tersimpan</h2>
                        <table class="w-full border border-gray-300 table-auto">
                            <thead class="bg-blue-100">
                                <tr>
                                    <th class="border px-2 py-1">#</th>
                                    <th class="border px-2 py-1">Member</th>
                                    <th class="border px-2 py-1">No RM</th>
                                    <th class="border px-2 py-1">Jenis Kunjungan</th>
                                    <th class="border px-2 py-1">Poli</th>
                                    <th class="border px-2 py-1">Payment</th>
                                    <th class="border px-2 py-1">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row,index) in savedRows" :key="index">
                                    <tr>
                                        <td class="border px-2 py-1" x-text="index+1"></td>
                                        <td class="border px-2 py-1" x-text="row.member"></td>
                                        <td class="border px-2 py-1" x-text="row.no_rm"></td>
                                        <td class="border px-2 py-1" x-text="row.jenis"></td>
                                        <td class="border px-2 py-1" x-text="row.poli"></td>
                                        <td class="border px-2 py-1" x-text="row.payment"></td>
                                        <td class="border px-2 py-1" x-text="row.tanggal"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab Data Pendaftaran -->
            <div x-show="tab === 'pendaftaran'" x-transition class="p-4 bg-gray-50 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold">📋 Data Pendaftaran</h2>
                <p class="mt-2">Konten Data Pendaftaran akan diisi nanti.</p>
            </div>

            <!-- Tab Explore Data -->
            <div x-show="tab === 'explore'" x-transition class="p-4 bg-gray-50 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold">🔎 Explore Data</h2>
                <p class="mt-2">Konten Explore Data akan diisi nanti.</p>
            </div>

        </div>
    </div>
</div>

<script>
function kunjunganForm() {
    return {
        rows: [
            {member:'', no_rm:'', jenis:'', poli:'', payment:'', tanggal: new Date().toISOString().substr(0,10)}
        ],
        savedRows: [],
        addRow() { this.rows.push({member:'', no_rm:'', jenis:'', poli:'', payment:'', tanggal: new Date().toISOString().substr(0,10)}); },
        removeRow(index) { this.rows.splice(index,1); },
        saveRows() {
            this.savedRows.push(...this.rows);
            this.rows = [];
            this.addRow();
        }
    }
}
</script>
@endsection
