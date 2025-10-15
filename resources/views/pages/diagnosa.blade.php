@extends('layouts.app')

@section('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .tab-container { display: flex; border-bottom: 2px solid #ddd; margin-bottom: 15px; }
    .tab-button { padding: 10px 20px; font-weight: 600; background: #f5f5f5; border: none; cursor: pointer; border-bottom: 2px solid transparent; transition: 0.2s; }
    .tab-button.active { background: #fff; border-bottom: 3px solid #007bff; color: #007bff; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    .section-box { border: 1px solid #ddd; border-radius: 6px; padding: 12px; background: #fafafa; margin-bottom: 12px; }
    .section-title { font-weight: 600; font-size: 13px; color: #555; margin-bottom: 5px; }
    textarea, select, input { width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 6px 8px; font-size: 14px; }
    label { font-size: 13px; font-weight: 500; color: #333; }

    /* Wrapper untuk menahan posisi dropdown select2 */
    .select-wrapper {
        position: relative;
        display: block;
    }

    /* Select2 sejajar penuh dan nempel di posisi select */
    .select2-container {
        width: 100% !important;
    }

    .select2-container--open .select2-dropdown {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        width: 100% !important;
        margin-top: 2px !important;
        z-index: 9999 !important;
    }

    /* Tinggi & tampilan select2 biar sama kayak select biasa */
    .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ccc !important;
        border-radius: 4px !important;
        display: flex !important;
        align-items: center !important;
        padding: 4px 6px !important;
    }

    .select2-selection__rendered {
        line-height: 28px !important;
        font-size: 14px !important;
    }

    .select2-selection__arrow {
        height: 38px !important;
        right: 8px !important;
    }
</style>
@endsection

@section('content')
<div class="max-w-full max-h-[700px] mx-auto p-3 bg-gray-50 rounded-lg shadow-sm">

    <!-- Tabs -->
    <div class="tab-container">
        <button class="tab-button active" data-tab="anamnesa">Anamnesa</button>
        <button class="tab-button" data-tab="resep">Pemberian Resep</button>
        <button class="tab-button" data-tab="riwayat">Riwayat Pasien</button>
    </div>

    <!-- TAB ANAMNESA -->
    <div id="anamnesa" class="tab-content active">
        <form>
            <div class="grid grid-cols-2 gap-5">
                <!-- KIRI -->
                <div class="space-y-4">

                    <!-- Nama Pasien -->
                    <div class="mb-4">
                        <label class="block font-semibold mb-2 text-gray-700">Nama Pasien</label>
                        <div class="select-wrapper">
                            <select id="namaPasien" class="w-full border rounded p-2">
                                <option value="">-- Pilih Pasien --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Informasi -->
                    <div class="section-box">
                        <div class="section-title">Information</div>
                        <textarea id="infoPasien" class="w-full border p-3 rounded bg-gray-50 font-mono text-lg" rows="9" readonly></textarea>
                    </div>

                    <!-- Diagnosa -->
                    <div class="mb-4">
                        <label class="block font-semibold mb-2 text-gray-700">Diagnosa</label>
                        <div class="select-wrapper">
                            <select id="diagnosa" class="w-full border rounded p-2">
                                <option value="">-- Pilih Diagnosa --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Optional -->
                    <div>
                        <div style="font-style: italic; color:#2f2fa2; font-weight:600; font-size:13px; margin-bottom:4px;">Optional</div>
                        <div class="border-t border-blue-700 mb-3"></div>

                        <div class="mb-2">
                            <label>Catatan Alergi :</label>
                            <input type="text" id="alergi" class="border rounded p-1 w-full">
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-2">
                                <label>BB :</label>
                                <input type="text" id="berat" style="width:60px" class="border rounded p-1">
                                <span>Kg</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <label>TB :</label>
                                <input type="text" id="tinggi" style="width:60px" class="border rounded p-1">
                                <span>Cm</span>
                            </div>

                            <div class="flex items-center space-x-3 ml-10">
                                <button id="btnSimpan" type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    💾 Simpan Diagnosa
                                </button>
                                <button type="reset" class="bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500">
                                    ❌ Batal
                                </button> 
                            </div>                           
                        </div>
                    </div>

                </div>

                <!-- KANAN -->
                <div class="space-y-2">
                    <div class="section-box">
                        <div class="section-title">Keluhan Utama</div>
                        <textarea id="keluhanUtama" rows="2" class="w-full border rounded p-2"></textarea>
                    </div>

                    <div class="section-box">
                        <div class="section-title">Riwayat Penyakit Sekarang</div>
                        <textarea id="riwayatSekarang" rows="2" class="w-full border rounded p-2"></textarea>
                    </div>

                    <div class="section-box">
                        <div class="section-title">Riwayat Penyakit Dahulu</div>
                        <textarea id="riwayatDahulu" rows="2" class="w-full border rounded p-2"></textarea>
                    </div>

                    <div class="section-box">
                        <div class="section-title">Riwayat Keluarga</div>
                        <textarea id="riwayatKeluarga" rows="2" class="w-full border rounded p-2"></textarea>
                    </div>

                    <div class="section-box">
                        <div class="section-title">Riwayat Sosial</div>
                        <textarea id="riwayatSosial" rows="2" class="w-full border rounded p-2"></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB RESEP -->
    <div id="resep" class="tab-content">
        <div class="bg-white p-4 rounded shadow">
            <h4 class="font-semibold text-gray-700 mb-2">Pemberian Resep</h4>
            <input type="text" id="searchResep" class="border px-3 py-1 mb-2 w-1/3" placeholder="Cari obat...">

            <table id="tabelResep" class="table-auto w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1">Kode Obat</th>
                        <th class="border px-2 py-1">Nama Obat</th>
                        <th class="border px-2 py-1">Dosis</th>
                        <th class="border px-2 py-1">Frekuensi</th>
                        <th class="border px-2 py-1">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="border px-2 py-1">OB001</td><td class="border px-2 py-1">Paracetamol</td><td class="border px-2 py-1">500mg</td><td class="border px-2 py-1">3x sehari</td><td class="border px-2 py-1">-</td></tr>
                    <tr><td class="border px-2 py-1">OB002</td><td class="border px-2 py-1">Amoxicillin</td><td class="border px-2 py-1">250mg</td><td class="border px-2 py-1">2x sehari</td><td class="border px-2 py-1">Sesudah makan</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat -->
    <div id="riwayat" class="tab-content">
        <div class="bg-white p-4 rounded shadow">
            <h4 class="font-semibold text-gray-700 mb-2">Riwayat Pasien</h4>
            <input type="text" id="searchResep" class="border px-3 py-1 mb-2 w-1/3" placeholder="Cari obat...">

            <table class="table-auto w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1">Nama Pasien</th>
                        <th class="border px-2 py-1">No RM</th>
                        <th class="border px-2 py-1">Tgl Kunjungan</th>
                        <th class="border px-2 py-1">Diagnosa</th>
                        <th class="border px-2 py-1">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Select2 JS (pastikan jQuery sudah diload di layout) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", async () => {

    // ===== Tab Switching =====
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(btn.dataset.tab).classList.add('active');
        });
    });

    // ===== Search + Highlight (Tabel Resep) =====
    const searchInput = document.getElementById('searchResep');
    const table = document.getElementById('tabelResep');
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            table.querySelectorAll('tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
                row.style.background = text.includes(filter) ? '#fff3cd' : '';
            });
        });
    }

    // ===== Pastikan jQuery & Select2 tersedia =====
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        console.warn('⚠️ jQuery atau Select2 belum dimuat.');
        return;
    }

    const $nama = $('#namaPasien');
    const $diagn = $('#diagnosa');
    const infoBox = document.getElementById('infoPasien');

    // Kosongkan dan pasang placeholder awal
    $nama.empty().append($('<option>', { value: '', text: '-- Pilih Pasien --' }));
    $diagn.empty().append($('<option>', { value: '', text: '-- Pilih Diagnosa --' }));

    // ===== Load dropdown pasien =====
    try {
        const resPasien = await fetch("/get-pasien-hariini");
        const pasienData = await resPasien.json();
        pasienData.forEach(p => {
            $nama.append(new Option(`${p.nm_member} (${p.no_rm})`, p.no_rm, false, false));
        });
    } catch (err) {
        console.error("Gagal load pasien:", err);
    }

    // ===== Load dropdown diagnosa =====
    try {
        const resDiag = await fetch("/get-diagnosa");
        const diagnosaData = await resDiag.json();
        diagnosaData.forEach(d => {
            $diagn.append(new Option(`${d.kode_icd} - ${d.diagnosis}`, d.kode_icd, false, false));
        });
    } catch (err) {
        console.error("Gagal load diagnosa:", err);
    }

    // ===== Inisialisasi Select2 =====
    $nama.select2({
        placeholder: "🔍 Cari pasien hari ini...",
        allowClear: true,
        width: '100%',
        dropdownParent: $nama.closest('.select-wrapper')
    });

    $diagn.select2({
        placeholder: "🔍 Cari diagnosa...",
        allowClear: true,
        width: '100%',
        dropdownParent: $diagn.closest('.select-wrapper')
    });

    // ===== Saat pasien dipilih, ambil detail lengkap =====
    $nama.on('change', async function () {
        const no_rm = $(this).val();
        if (!no_rm) {
            infoBox.value = "";
            return;
        }

        console.log("no_rm dikirim:", no_rm);

        try {
            const res = await fetch(`/get-detail-pasien?no_rm=${encodeURIComponent(no_rm.trim())}`);
            const data = await res.json();

            if (data) {
                infoBox.value =
`No. RM              : ${data.no_rm}
Nama                : ${data.nm_member}
Umur                : ${data.umur}
Jenis Kelamin       : ${data.gender}
Golongan Darah      : ${data.gol_darah}
Tgl Kunjungan       : ${data.tgl_kunjungan}
Jam Kunjungan       : ${data.jam_kunjungan}
Jenis Kunjungan     : ${data.jenis_kunjungan}
Poli                : ${data.poli}`;
            } else {
                infoBox.value = "❌ Data pasien tidak ditemukan.";
            }
        } catch (err) {
            console.error("Gagal mengambil detail pasien:", err);
            infoBox.value = "⚠️ Terjadi kesalahan saat mengambil data.";
        }
    });
});



// Insert ke tabel anamnesa dan diagnosa
document.getElementById('btnSimpan').addEventListener('click', async () => {
    const no_rm = document.getElementById('namaPasien').value;
    const alergi = document.getElementById('alergi').value;
    const berat = document.getElementById('berat').value;
    const tinggi = document.getElementById('tinggi').value;
    const keluhanUtama = document.getElementById('keluhanUtama').value;
    const riwayatSekarang = document.getElementById('riwayatSekarang').value;
    const riwayatDahulu = document.getElementById('riwayatDahulu').value;
    const riwayatKeluarga = document.getElementById('riwayatKeluarga').value;
    const riwayatSosial = document.getElementById('riwayatSosial').value;

    const diagnosaValue = document.getElementById('diagnosa').value;
    let kode_icd = "";
    let diagnosaText = "";

    // Jika formatnya "KODE - NAMA"
    if (diagnosaValue.includes(" - ")) {
        const parts = diagnosaValue.split(" - ");
        kode_icd = parts[0].trim();
        diagnosaText = parts[1].trim();
    } else {
        // fallback kalau value-nya cuma kode
        kode_icd = diagnosaValue;
        diagnosaText = $("#diagnosa option:selected").text().split(" - ")[1]?.trim() || "";
    }

    // Validasi sederhana
    if (!no_rm) {
        alert('Silakan pilih pasien terlebih dahulu!');
        return;
    }

    if (!kode_icd) {
        alert('Silakan pilih diagnosa terlebih dahulu!');
        return;
    }

    try {
        const response = await fetch('/simpan-anamnesa', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                no_rm,
                alergi,
                berat,
                tinggi,
                keluhan_utama: keluhanUtama,
                riwayat_penyakit_sekarang: riwayatSekarang,
                riwayat_penyakit_dahulu: riwayatDahulu,
                riwayat_keluarga: riwayatKeluarga,
                riwayat_sosial: riwayatSosial,
                kode_icd,
                diagnosa: diagnosaText
            })
        });

        const result = await response.json();

        if (result.success) {
            alert('✅ Data berhasil disimpan!');
        } else {
            alert('⚠️ Gagal menyimpan data: ' + (result.message || 'Tidak diketahui'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan saat menyimpan data.');
    }
});

</script>



@endsection
