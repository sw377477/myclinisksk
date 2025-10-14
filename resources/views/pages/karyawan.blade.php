@extends('layouts.app')
@section('content')
<div class="p-2 bg-gray-100 min-h-screen">

   <!-- <h1 class="text-2xl font-bold text-gray-800 mb-4">🧑‍💼 Data Karyawan</h1> -->

    <div x-data="{ tab: 'utama', selectedNik: null }" class="bg-white rounded-2xl shadow-md p-4">

        <!-- Navigasi Tab -->
        <div class="border-b border-gray-300 flex flex-wrap">
            <button @click="tab = 'utama'"
                :class="tab === 'utama' ? 'border-b-4 border-yellow-400 text-yellow-600 font-bold' : 'text-gray-600 hover:text-yellow-500'"
                class="px-4 py-2 text-lg transition-all duration-200">
                👨‍👩‍👧‍👦 Karyawan & Keluarga
            </button>

            <button @click="tab = 'detail'"
                :class="tab === 'detail' ? 'border-b-4 border-yellow-400 text-yellow-600 font-bold' : 'text-gray-600 hover:text-yellow-500'"
                class="px-4 py-2 text-lg transition-all duration-200">
                👥 Detail Karyawan
            </button>

            <button @click="tab = 'hris'"
                :class="tab === 'hris' ? 'border-b-4 border-yellow-400 text-yellow-600 font-bold' : 'text-gray-600 hover:text-yellow-500'"
                class="px-4 py-2 text-lg transition-all duration-200">
                🔗 Integrasi HRIS
            </button>
        </div>

        <!-- Isi Tab -->
        <div class="mt-6">

            <!-- Tab 1: Karyawan & Keluarga -->
            <div x-show="tab === 'utama'" x-transition>
                <h2 class="text-xl font-semibold text-gray-700 mb-4">📋 Karyawan & Keluarga</h2>

                <!-- Filter & Search -->
                <div class="flex flex-col md:flex-row items-start md:items-center gap-4 mb-4">
                    <select id="golSelect" class="border rounded p-2">
                        <option value="">-- Semua Gol --</option>
                        @php
                            $golOptions = ['02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI'];
                        @endphp
                        @foreach($golOptions as $value=>$roman)
                            <option value="{{ $value }}" {{ $gol == $value ? 'selected' : '' }}>Gol {{ $roman }}</option>
                        @endforeach
                    </select>

                    <input type="text" id="searchInput" placeholder="🔍 Cari NIK atau Nama karyawan" 
                        class="border rounded p-2 w-full md:w-[500px]">
                </div>

                <!-- Tabel Karyawan & Keluarga -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- Karyawan -->
                    <div>
                        <h3 class="font-semibold mb-2">Daftar Karyawan</h3>
                        <div class="overflow-auto border rounded-lg max-h-[450px]">
                            <table class="w-full border-collapse text-sm" id="tblKaryawan">
                                <thead class="bg-gray-200 text-gray-700 sticky top-0 z-10">
                                    <tr>
                                        <th class="border px-2 py-1">NIK</th>
                                        <th class="border px-2 py-1">Nama</th>
                                        <th class="border px-2 py-1">LP</th>
                                        <th class="border px-2 py-1">BPJS</th>
                                        <th class="border px-2 py-1">KK</th>
                                        <th class="border px-2 py-1">KTP</th>
                                        <th class="border px-2 py-1">UNIT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($karyawan as $row)
                                    <tr class="cursor-pointer even:bg-gray-100 hover:bg-blue-100"
                                        data-nik="{{ $row->nik }}"
                                        @click="selectedNik = '{{ $row->nik }}'; loadKeluarga('{{ $row->nik }}')"
                                        :class="selectedNik === '{{ $row->nik }}' ? 'bg-yellow-100' : ''">
                                        <td class="border px-2 py-1">{{ $row->nik }}</td>
                                        <td class="border px-2 py-1">{{ $row->nama }}</td>
                                        <td class="border px-2 py-1">{{ $row->lp }}</td>
                                        <td class="border px-2 py-1">{{ $row->no_bpjs_kesehatan }}</td>
                                        <td class="border px-2 py-1">{{ $row->kk }}</td>
                                        <td class="border px-2 py-1">{{ $row->ktp }}</td>
                                        <td class="border px-2 py-1">{{ $row->idpt }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Keluarga -->
                    <div>
                        <h3 class="font-semibold mb-2">Data Keluarga</h3>
                        <div class="overflow-auto border rounded-lg max-h-[450px]">
                            <table class="w-full border-collapse text-sm" id="tblKeluarga">
                                <thead class="bg-gray-200 text-gray-700 sticky top-0 z-10">
                                    <tr>
                                        <th class="border px-2 py-1">Nama</th>
                                        <th class="border px-2 py-1">Hubungan</th>
                                        <th class="border px-2 py-1">TTL</th>
                                        <th class="border px-2 py-1">Umur</th>
                                        <th class="border px-2 py-1">BPJS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center text-gray-400 py-2">
                                            Klik karyawan untuk melihat keluarga
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2 -->
            <div x-show="tab === 'detail'" class="space-y-4" x-transition>
                <h2 class="text-xl font-semibold text-gray-700 mb-4">🧾 Detail Karyawan</h2>

                <div x-data="{ subtab: 'bulanan' }" class="bg-gray-50 p-4 rounded-lg border">
                    <!-- Navigasi Sub-Tab -->
                    <div class="border-b border-gray-300 flex flex-wrap mb-4">
                        <button @click="subtab='bulanan'" 
                            :class="subtab==='bulanan' ? 'border-b-4 border-yellow-400 text-yellow-600 font-bold' : 'text-gray-600 hover:text-yellow-500'"
                            class="px-4 py-2 text-lg transition-all duration-200">Karyawan Bulanan</button>
                        <button @click="subtab='harian'" 
                            :class="subtab==='harian' ? 'border-b-4 border-yellow-400 text-yellow-600 font-bold' : 'text-gray-600 hover:text-yellow-500'"
                            class="px-4 py-2 text-lg transition-all duration-200">Karyawan Harian</button>
                        <button @click="subtab='borongan'" 
                            :class="subtab==='borongan' ? 'border-b-4 border-yellow-400 text-yellow-600 font-bold' : 'text-gray-600 hover:text-yellow-500'"
                            class="px-4 py-2 text-lg transition-all duration-200">Karyawan Borongan</button>
                    </div>

                    <!-- Sub-Tab Content -->
                    <div class="space-y-4">
                        <!-- Bulanan -->
                        <div x-show="subtab==='bulanan'" x-transition>
                            <div class="flex items-center gap-4 mb-2">
                                <select id="filterLokasiBulanan" class="border rounded p-2">
                                    <option value="">-- Semua Lokasi --</option>
                                </select>

                                <select id="filterDepartemenBulanan" class="border rounded p-2">
                                    <option value="">-- Semua Departemen --</option>
                                </select>
                                <input type="text" id="searchBulanan" class="border rounded p-2 w-full md:w-[500px]" placeholder="🔍 Cari karyawan...">
                                <button id="exportExcel" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Export Excel</button>
                            </div>
                            <div class="max-h-[400px] overflow-auto border rounded-lg">
                                <table id="tabelKaryawanBulanan" class="w-full border-collapse text-sm">
                                    <thead class="bg-gray-200 text-gray-700 sticky top-0 z-10">
                                        <tr>
                                            <th class="border px-2 py-1">NIK</th>
                                            <th class="border px-2 py-1">Nama</th>
                                            <th class="border px-2 py-1">L/P</th>
                                            <th class="border px-2 py-1">TTL</th>
                                            <th class="border px-2 py-1">No.KTP</th>
                                            <th class="border px-2 py-1">No.BPJS</th>
                                            <th class="border px-2 py-1">No.HP</th>
                                            <th class="border px-2 py-1">Jabatan</th>
                                            <th class="border px-2 py-1">Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabelBodyKaryawan"></tbody>
                                </table>

                            </div>
                        </div>

                        <!-- Harian -->
                        <div x-show="subtab==='harian'" x-transition>
                            <div class="flex items-center gap-4 mb-2">
                                <select id="filterEstateHarian" class="border rounded p-2">
                                    <option value="">-- Semua Estate --</option>
                                </select>

                                <select id="filterDivisiHarian" class="border rounded p-2">
                                    <option value="">-- Semua Divisi --</option>
                                </select>
                                <input type="text" id="searchHarian" class="border rounded p-2 w-full md:w-[500px]" placeholder="🔍 Cari karyawan...">
                                <button id="exportExcel" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Export Excel</button>
                            </div>
                            <div class="max-h-[400px] overflow-auto border rounded-lg">
                                <table id="tabelKaryawanHarian" class="w-full border-collapse text-sm">
                                    <thead class="bg-gray-200 text-gray-700 sticky top-0 z-10">
                                        <tr>
                                            <th class="border px-2 py-1">NIK</th>
                                            <th class="border px-2 py-1">Nama</th>
                                            <th class="border px-2 py-1">LP</th>
                                            <th class="border px-2 py-1">TTL</th>
                                            <th class="border px-2 py-1">Status</th>
                                            <th class="border px-2 py-1">No.KK</th>
                                            <th class="border px-2 py-1">No.KTP</th>
                                            <th class="border px-2 py-1">No.BPJS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Borongan -->
                        <div x-show="subtab==='borongan'" x-transition>
                            <div class="flex items-center gap-4 mb-2">
                                <select id="filterEstateBorongan" class="border rounded p-2">
                                    <option value="">-- Semua Estate --</option>
                                </select>

                                <select id="filterDivisiBorongan" class="border rounded p-2">
                                    <option value="">-- Semua Divisi --</option>
                                </select>
                                <input type="text" id="searchBorongan" class="border rounded p-2 w-full md:w-[500px]" placeholder="🔍 Cari karyawan...">
                                <button id="exportExcel" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Export Excel</button>
                            </div>
                            <div class="max-h-[400px] overflow-auto border rounded-lg">
                                <table id="tabelKaryawanBorongan" class="w-full border-collapse text-sm">
                                    <thead class="bg-gray-200 text-gray-700 sticky top-0 z-10">
                                        <tr>
                                            <th class="border px-2 py-1">NIK</th>
                                            <th class="border px-2 py-1">Nama</th>
                                            <th class="border px-2 py-1">LP</th>
                                            <th class="border px-2 py-1">TTL</th>
                                            <th class="border px-2 py-1">Status</th>
                                            <th class="border px-2 py-1">No.KK</th>
                                            <th class="border px-2 py-1">No.KTP</th>
                                            <th class="border px-2 py-1">No.BPJS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Tab 3: Integrasi HRIS -->
            <div x-show="tab === 'hris'" x-transition class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-700">🔗 Integrasi HRIS</h2>
                <div class="bg-gray-50 p-4 rounded-lg border">
                    <p class="text-gray-600 mb-3">Sinkronisasi data dengan HRIS. Klik tombol untuk memulai.</p>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" 
                        @click="alert('Sinkronisasi dimulai!')">♻ Sinkronisasi Sekarang</button>
                    <p class="mt-3 text-sm text-gray-500 italic">Terakhir sinkron: {{ now() }}</p>
                </div>
            </div>

        </div>
        
    </div>
</div>

<style>
.highlight {
    background-color: #fff59d; /* kuning lembut */
    padding: 0 2px;
    border-radius: 3px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
function loadKeluarga(nik) {
    console.log("Klik NIK:", nik);
    fetch('/karyawan/keluarga/' + nik)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector("#tblKeluarga tbody");
            tbody.innerHTML = "";

            if(data.length === 0){
                tbody.innerHTML = `<tr><td colspan="6" class="text-center text-gray-400 py-2">Tidak ada data keluarga dari karyawan yang dipilih, mohon hubungi HRD</td></tr>`;
                return;
            }

            data.forEach((row,index)=>{
                tbody.innerHTML += `
                    <tr class="bg-white even:bg-gray-100">
                        <td class="border px-2 py-1">${row.nama}</td>
                        <td class="border px-2 py-1">${row.hubungan}</td>
                        <td class="border px-2 py-1">${row.ttl ?? '-'}</td>
                        <td class="border px-2 py-1">${row.umur ?? '-'}</td>
                        <td class="border px-2 py-1">${row.bpjskes ?? '-'}</td>
                    </tr>`;
            });
        });
}

// Search + filter gol secara live
const searchInput = document.getElementById('searchInput');
const golSelect = document.getElementById('golSelect');

function filterKaryawan() {
    const search = searchInput.value;
    const gol = golSelect.value;

    fetch(`{{ route('karyawan.index') }}?search=${encodeURIComponent(search)}&gol=${gol}`,{
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res=>res.text())
    .then(html=>{
        const parser = new DOMParser();
        const doc = parser.parseFromString(html,'text/html');
        const newTbody = doc.querySelector('#tblKaryawan tbody').innerHTML;
        document.querySelector('#tblKaryawan tbody').innerHTML = newTbody;
    });
}

searchInput.addEventListener('input',filterKaryawan);
golSelect.addEventListener('change',filterKaryawan);
</script>


<script>
document.addEventListener("DOMContentLoaded", async () => {
    const lokasiSelect = document.getElementById("filterLokasiBulanan");
    const departemenSelect = document.getElementById("filterDepartemenBulanan");
    const tableBody = document.getElementById("tabelBodyKaryawan");

    // === Load Lokasi ===
    try {
        const lokasiResponse = await fetch("/get-lokasi");
        const lokasiData = await lokasiResponse.json();

        if (!lokasiData.error && lokasiData.length > 0) {
            lokasiData.forEach(lokasi => {
                const option = document.createElement("option");
                option.value = lokasi.iddata;
                option.textContent = lokasi.iddata;
                lokasiSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error("Gagal memuat lokasi:", error);
    }

    // === Ketika Lokasi Berubah ===
    lokasiSelect.addEventListener("change", async (e) => {
        const iddata = e.target.value;

        // 🧹 Kosongkan departemen & tabel karyawan setiap kali lokasi diganti
        departemenSelect.innerHTML = '<option value="">-- Semua Departemen --</option>';
        tableBody.innerHTML = "<tr><td colspan='9' class='text-center py-2 text-gray-500'>Silakan pilih departemen</td></tr>";

        if (iddata) {
            const depResponse = await fetch(`/get-departemen/${iddata}`);
            const depData = await depResponse.json();

            depData.forEach(dep => {
                const option = document.createElement("option");
                option.value = dep.kode;
                option.textContent = dep.ket;
                departemenSelect.appendChild(option);
            });
        }
    });

    // === Ketika Departemen Berubah ===
    departemenSelect.addEventListener("change", async (e) => {
        const kode = e.target.value;
        const iddata = lokasiSelect.value;
        await loadKaryawan(iddata, kode);
    });

    // === Fungsi Load Karyawan ===
    async function loadKaryawan(iddata, kode) {
        tableBody.innerHTML = `
            <tr><td colspan="9" class="text-center py-2 text-gray-500">⏳ Memuat data...</td></tr>
        `;

        try {
            const res = await fetch(`/get-karyawan-bulanan?iddata=${encodeURIComponent(iddata)}&kode=${encodeURIComponent(kode)}`);
            const data = await res.json();

            if (data.length === 0) {
                tableBody.innerHTML = `
                    <tr><td colspan="9" class="text-center py-2 text-gray-500">Tidak ada data</td></tr>
                `;
                return;
            }

            tableBody.innerHTML = "";
            data.forEach(row => {
                const tr = document.createElement("tr");
                tr.classList.add("bg-white", "even:bg-gray-50");
                tr.innerHTML = `
                    <td class="border px-2 py-1">${row.nik ?? ""}</td>
                    <td class="border px-2 py-1">${row.nama ?? ""}</td>
                    <td class="border px-2 py-1">${row.lp ?? ""}</td>
                    <td class="border px-2 py-1">${row.ttl ?? ""}</td>
                    <td class="border px-2 py-1">${row.ktp ?? ""}</td>
                    <td class="border px-2 py-1">${row.bpjs ?? ""}</td>
                    <td class="border px-2 py-1">${row.no_hp ?? ""}</td>
                    <td class="border px-2 py-1">${row.jabatan ?? ""}</td>
                    <td class="border px-2 py-1">${row.idpt ?? ""}</td>
                `;
                tableBody.appendChild(tr);
            });
        } catch (error) {
            console.error("Gagal memuat data karyawan:", error);
            tableBody.innerHTML = `
                <tr><td colspan="9" class="text-center py-2 text-red-500">❌ Gagal memuat data</td></tr>
            `;
        }
    }

    // === Tombol Export Excel ===
    const exportBtn = document.getElementById("exportExcel");
    if (exportBtn) {
        exportBtn.addEventListener("click", () => {
            alert("🔜 Fitur Export Excel akan segera ditambahkan!");
        });
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", async () => {
    // ==========================
    // 🔹 HAR IAN
    // ==========================
    const estateHarian = document.getElementById("filterEstateHarian");
    const divisiHarian = document.getElementById("filterDivisiHarian");
    const tabelHarianBody = document.querySelector('[x-show="subtab===\'harian\'"] tbody');

    // 🔹 Load Estate (Lokasi)
    const estateRes = await fetch("/get-estate");
    const estateData = await estateRes.json();

    if (!estateData.error) {
        estateData.forEach(est => {
            const opt = document.createElement("option");
            opt.value = est.estateid;
            opt.textContent = est.nama;
            estateHarian.appendChild(opt);
        });
    }

    // 🔹 Saat estate dipilih → load divisi
    estateHarian.addEventListener("change", async e => {
        const estateid = e.target.value;
        divisiHarian.innerHTML = '<option value="">-- Semua Divisi --</option>';
        tabelHarianBody.innerHTML = ""; // Hapus isi tabel saat ganti lokasi

        if (estateid) {
            const divRes = await fetch(`/get-divisi/${estateid}`);
            const divData = await divRes.json();

            divData.forEach(div => {
                const opt = document.createElement("option");
                opt.value = div.divisiid;
                opt.textContent = div.divisi;
                divisiHarian.appendChild(opt);
            });
        }
    });

    // 🔹 Saat divisi dipilih → tampilkan data karyawan harian
    divisiHarian.addEventListener("change", async e => {
        const divisiid = e.target.value;
        tabelHarianBody.innerHTML = "<tr><td colspan='9' class='text-center py-2'>Loading...</td></tr>";

        if (!divisiid) {
            tabelHarianBody.innerHTML = "<tr><td colspan='9' class='text-center text-gray-500 py-2'>Pilih divisi terlebih dahulu</td></tr>";
            return;
        }

        const res = await fetch(`/get-karyawan-harian?divisiid=${divisiid}`);
        const data = await res.json();

        if (!data.length) {
            tabelHarianBody.innerHTML = "<tr><td colspan='9' class='text-center text-gray-500 py-2'>Tidak ada data karyawan</td></tr>";
            return;
        }

        tabelHarianBody.innerHTML = "";
        data.forEach(row => {
            const tr = document.createElement("tr");
            tr.classList.add("bg-white", "even:bg-gray-50");
            tr.innerHTML = `
                <td class="border px-2 py-1">${row.nik}</td>
                <td class="border px-2 py-1">${row.nama}</td>
                <td class="border px-2 py-1">${row.lp}</td>
                <td class="border px-2 py-1">${row.ttl}</td>
                <td class="border px-2 py-1">${row.ket}</td>
                <td class="border px-2 py-1">${row.kk ??''}</td>
                <td class="border px-2 py-1">${row.ktp ?? ''}</td>
                <td class="border px-2 py-1">${row.bpjs ?? ''}</td>
            `;
            tabelHarianBody.appendChild(tr);
        });
    });

    // ==========================
    // 🔹 BORONGAN
    // ==========================
    const estateBorongan = document.getElementById("filterEstateBorongan");
    const divisiBorongan = document.getElementById("filterDivisiBorongan");
    const tabelBoronganBody = document.querySelector('[x-show="subtab===\'borongan\'"] tbody');

    // 🔹 Load Estate (Lokasi)
    if (!estateData.error) {
        estateData.forEach(est => {
            const opt = document.createElement("option");
            opt.value = est.estateid;
            opt.textContent = est.nama;
            estateBorongan.appendChild(opt.cloneNode(true));
        });
    }

    // 🔹 Saat estate dipilih → load divisi
    estateBorongan.addEventListener("change", async e => {
        const estateid = e.target.value;
        divisiBorongan.innerHTML = '<option value="">-- Semua Divisi --</option>';
        tabelBoronganBody.innerHTML = ""; // Kosongkan tabel saat lokasi berubah

        if (estateid) {
            const divRes = await fetch(`/get-divisi/${estateid}`);
            const divData = await divRes.json();

            divData.forEach(div => {
                const opt = document.createElement("option");
                opt.value = div.divisiid;
                opt.textContent = div.divisi;
                divisiBorongan.appendChild(opt);
            });
        }
    });

    // 🔹 Saat divisi dipilih → tampilkan data karyawan borongan
    divisiBorongan.addEventListener("change", async e => {
        const divisiid = e.target.value;
        tabelBoronganBody.innerHTML = "<tr><td colspan='9' class='text-center py-2'>Loading...</td></tr>";

        if (!divisiid) {
            tabelBoronganBody.innerHTML = "<tr><td colspan='9' class='text-center text-gray-500 py-2'>Pilih divisi terlebih dahulu</td></tr>";
            return;
        }

        const res = await fetch(`/get-karyawan-borongan?divisiid=${divisiid}`);
        const data = await res.json();

        if (!data.length) {
            tabelBoronganBody.innerHTML = "<tr><td colspan='9' class='text-center text-gray-500 py-2'>Tidak ada data karyawan</td></tr>";
            return;
        }

        tabelBoronganBody.innerHTML = "";
        data.forEach(row => {
            const tr = document.createElement("tr");
            tr.classList.add("bg-white", "even:bg-gray-50");
            tr.innerHTML = `
                <td class="border px-2 py-1">${row.nik}</td>
                <td class="border px-2 py-1">${row.nama}</td>
                <td class="border px-2 py-1">${row.lp}</td>
                <td class="border px-2 py-1">${row.ttl}</td>
                <td class="border px-2 py-1">${row.ket}</td>
                <td class="border px-2 py-1">${row.kk ??''}</td>
                <td class="border px-2 py-1">${row.ktp ?? ''}</td>
                <td class="border px-2 py-1">${row.bpjs ?? ''}</td>
            `;
            tabelBoronganBody.appendChild(tr);
        });
    });
    
});

// ================== FUNGSI SEARCH DENGAN HIGHLIGHT ==================
function setupTableSearchWithHighlight(inputId, tableSelector) {
    const input = document.getElementById(inputId);
    const table = document.querySelector(tableSelector);
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const filter = this.value.toLowerCase().trim();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(row => {
            const cells = row.querySelectorAll("td");
            let rowMatch = false;

            cells.forEach(cell => {
                // Kembalikan teks asli tanpa highlight
                const text = cell.textContent;
                cell.innerHTML = text;

                // Cek apakah cocok dengan filter
                const lowerText = text.toLowerCase();
                if (filter && lowerText.includes(filter)) {
                    rowMatch = true;

                    // Buat regex untuk highlight bagian yang cocok
                    const regex = new RegExp(`(${filter})`, "gi");
                    cell.innerHTML = text.replace(regex, `<span class="highlight">$1</span>`);
                }
            });

            // Sembunyikan baris jika tidak cocok
            row.style.display = rowMatch || filter === "" ? "" : "none";
        });
    });
}

// ================== AKTIFKAN UNTUK SEMUA TAB ==================
document.addEventListener("DOMContentLoaded", () => {
    setupTableSearchWithHighlight("searchBulanan", "#tabelKaryawanBulanan");
    setupTableSearchWithHighlight("searchHarian", "#tabelKaryawanHarian");
    setupTableSearchWithHighlight("searchBorongan", "#tabelKaryawanBorongan");
});
</script>

@endsection
