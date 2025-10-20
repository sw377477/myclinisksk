@extends('layouts.app')

@section('content')
<div class="p-1 space-y-6 bg-gray-600">

    {{-- === 🗞️ RSS FEED BERJALAN === --}}
    <div class="bg-gray-100 p-1 rounded-xl shadow mt-8">
        <h2 class="text-xl font-bold mb-2">🗞️ RSS Feed Kesehatan</h2>
        <div class="overflow-hidden bg-white border rounded-md p-3">
            <marquee 
                behavior="scroll" 
                direction="left" 
                scrollamount="5" 
                class="text-gray-700 text-l space-x-6" 
                id="rssMarquee"
                onmouseover="this.stop();" 
                onmouseout="this.start();">
                Memuat berita RSS...
            </marquee>
        </div>
    </div>

    {{-- === 🎫 NOMOR ANTRIAN & KUNJUNGAN DALAM BLOK BESAR KIRI === --}}
    <div class="flex flex-col lg:flex-row justify-between items-start gap-4 mt-6">

        {{-- === KIRI: Nomor Antrian + Jenis Kunjungan === --}}
        <div class="flex flex-col gap-4 w-full lg:w-1/3">

            {{-- === NOMOR ANTRIAN === --}}
            <div class="bg-gray-700 text-white p-6 rounded-2xl shadow-lg text-center h-80 flex flex-col justify-center">
                <p class="text-sm opacity-80">🎫 Nomor Antrian</p>
                <h2 id="nomorAntrian" class="text-9xl font-bold">{{ $lastQueue ?? '-' }}</h2>
                <div class="mt-3 space-x-1">
                    <button id="btnPrev" class="bg-white text-green-700 px-2 py-1 rounded">⟨ Prev</button>
                    <button id="btnNext" class="bg-white text-green-700 px-2 py-1 rounded">Next ⟩</button>
                    <button id="btnReset" class="bg-white text-green-700 px-2 py-1 rounded">Reset</button>
                
                    <button id="btnCetak" class="bg-white text-green-700 px-2 py-1 rounded">📠Print</button>
                    <button id="btnPanggil" class="bg-white text-green-700 px-2 py-1 rounded">📢Call</button>
                </div>
            </div>

            {{-- === PER JENIS KUNJUNGAN === --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-gray-100 p-3 rounded-xl shadow text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Umum</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $jenisToday['1'] ?? 0 }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-xl shadow text-center">
                    <h3 class="text-lg font-semibold text-gray-700">BPJS</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $jenisToday['2'] ?? 0 }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-xl shadow text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Asuransi</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ $jenisToday['3'] ?? 0 }}</p>
                </div>
            </div>

            {{-- === KPI LAIN (hari ini, bulan, tahun) === --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-blue-500 text-white p-3 rounded-2xl shadow-lg text-center">
                    <p class="text-sm opacity-80">Kunjungan Hari Ini</p>
                    <h2 class="text-3xl font-bold">{{ $todayCount }}</h2>
                </div>

                <div class="bg-orange-500 text-white p-3 rounded-2xl shadow-lg text-center">
                    <p class="text-sm opacity-80">s/d Bulan Ini</p>
                    <h2 class="text-3xl font-bold">{{ $monthCount }}</h2>
                </div>

                <div class="bg-purple-600 text-white p-3 rounded-2xl shadow-lg text-center">
                    <p class="text-sm opacity-80">s/d Tahun Ini</p>
                    <h2 class="text-3xl font-bold">{{ $yearCount }}</h2>
                </div>
            </div>
        </div>

        {{-- === KANAN: GRAFIK & DROPDOWN === --}}
        <div class="flex-1 bg-blue-100 rounded-2xl shadow p-4">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-semibold text-gray-800 text-lg">Grafik Kunjungan per Bulan</h3>
                <form id="yearForm" method="GET">
                    <select id="year" name="year" class="border px-3 py-1 rounded-md text-sm" onchange="this.form.submit()">
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="chart-container" style="position: relative; height: 450px; overflow: hidden;">
                <canvas id="chartKunjungan"></canvas>
            </div>
        </div>
    </div>
</div>



{{-- === SCRIPT GRAFIK === --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('chartKunjungan');
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const data = @json($chartData);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Kunjungan per Bulan ({{ $selectedYear }})',
                data: data,
                backgroundColor: '#f38721ff',
                borderRadius: 6,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 40 } }, // tambah ruang di atas supaya label tidak terpotong
            plugins: {
                legend: { display: false },
                title: {
                    //display: true,
                    //text: 'Grafik Jumlah Kunjungan per Bulan',
                    //font: { size: 12, weight: 'regular' },
                    //padding: { bottom: 8 }
                },
                datalabels: {
                    anchor: 'end', // menempel di ujung atas batang
                    align: 'top',  // posisikan di atas batang
                    offset: 6,     // jarak sedikit di atas batang
                    color: '#000',
                    font: {
                        weight: 'bold',
                        size: 13
                    },
                    formatter: function(value) {
                        return value > 0 ? value : '';
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Jumlah Kunjungan' },
                    ticks: { precision: 0 }
                },
                x: {
                    //title: { display: true, text: 'Bulan' }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            }
        },
        plugins: [ChartDataLabels]
    });

    // === Dropdown tahun ===
    const yearDropdown = document.getElementById('year');
    if (yearDropdown) {
        yearDropdown.addEventListener('change', () => {
            document.getElementById('yearForm').submit();
        });
    }
});
</script>

<script>
const rawData = @json($chartData);
const labels = [...new Set(rawData.map(d => d.bulan))];

// === Kelompokkan data per jenis kunjungan ===
const grouped = {};
rawData.forEach(d => {
    if (!grouped[d.jenis_kunjungan]) grouped[d.jenis_kunjungan] = [];
});
labels.forEach(bulan => {
    for (const jenis in grouped) {
        const item = rawData.find(d => d.bulan === bulan && d.jenis_kunjungan === jenis);
        grouped[jenis].push(item ? item.total : 0);
    }
});


// === RSS Feed ===
async function loadRSS() {
    try {
        const res = await fetch("/rss-health");
        const text = await res.text();
        const parser = new DOMParser();
        const xml = parser.parseFromString(text, "text/xml");
        const items = xml.querySelectorAll("item");
        const headlines = [];
        items.forEach((item, i) => {
            if (i < 10) {
                const title = item.querySelector("title")?.textContent || "Tanpa Judul";
                const link = item.querySelector("link")?.textContent || "#";
                headlines.push(`<a href="${link}" target="_blank" class="hover:underline">${title}</a>`);
            }
        });
        document.getElementById("rssMarquee").innerHTML = headlines.join(" &nbsp;&nbsp;&nbsp;📰&nbsp;&nbsp;&nbsp; ");
    } catch (err) {
        document.getElementById("rssMarquee").innerText = "Gagal memuat RSS.";
    }
}
loadRSS();
</script>

{{-- === ANTRIAN === --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const nomorEl = document.getElementById("nomorAntrian");
    const prevBtn = document.getElementById("btnPrev");
    const nextBtn = document.getElementById("btnNext");
    const resetBtn = document.getElementById("btnReset");

    // ambil idpay dari session yang dikirim Laravel ke Blade
    const idpay = "{{ session('idpay') ?? 'DEFAULT' }}";

    // === fungsi ambil antrian per idpay ===
    async function getAntrian() {
        try {
            const res = await fetch(`/antrian?idpay=${idpay}&nocache=${Date.now()}`, { cache: "no-store" });
            const data = await res.json();

            nomorEl.textContent = data.nomor ?? "0";
        } catch (e) {
            console.error("Gagal memuat antrian:", e);
            nomorEl.textContent = "0";
        }
    }

    // === fungsi simpan perubahan (next/prev/reset) ===
    async function simpanAntrian(action) {
        try {
            const res = await fetch("/antrian/update", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Cache-Control": "no-cache"
                },
                body: JSON.stringify({ action, idpay })
            });

            const data = await res.json();
            nomorEl.textContent = data.nomor ?? "0";
        } catch (e) {
            console.error("Gagal menyimpan antrian:", e);
        }
    }

    // event button
    prevBtn.addEventListener("click", () => simpanAntrian("prev"));
    nextBtn.addEventListener("click", () => simpanAntrian("next"));
    resetBtn.addEventListener("click", () => simpanAntrian("reset"));

    // load awal
    getAntrian();
});
</script>

<script>
    //cetak antrian
document.addEventListener("DOMContentLoaded", () => {
    const cetakBtn = document.getElementById("btnCetak");
    const nomorEl = document.getElementById("nomorAntrian");
    // Hitung posisi tengah layar
    const width = 350;
    const height = 500;
    const left = (window.screen.width / 2) - (width / 2);
    const top = (window.screen.height / 2) - (height / 2);

    cetakBtn.addEventListener("click", () => {
        const nomor = nomorEl.textContent.trim();
        if (!nomor || nomor === "0" || nomor === "-") {
            alert("Belum ada nomor antrian!");
            return;
        }
        //window.open(`/antrian/cetak/${nomor}`, "_blank", "width=400,height=500");
        window.open(`/antrian/cetak/${nomor}`, "_blank", `width=${width},height=${height},top=${top},left=${left}`);
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btnPanggil = document.getElementById("btnPanggil");
    const nomorAntrianEl = document.getElementById("nomorAntrian");

    function panggilNomor() {
        if (!nomorAntrianEl) {
            alert("Nomor antrian tidak ditemukan di halaman.");
            return;
        }

        const nomor = nomorAntrianEl.innerText.trim();
        const pesan = `Nomor antrian ${nomor}, silakan menuju ruang periksa.`;

        const speak = () => {
            const utterance = new SpeechSynthesisUtterance(pesan);
            utterance.lang = "id-ID";
            utterance.rate = 0.9;
            utterance.pitch = 1.1;
            utterance.volume = 1;

            // tunggu daftar voice selesai dimuat
            const voices = speechSynthesis.getVoices();

            // cari suara wanita Indonesia (Chrome support)
            const femaleVoice = voices.find(v =>
                v.lang === "id-ID" &&
                (v.name.toLowerCase().includes("google") ||
                 v.name.toLowerCase().includes("female") ||
                 v.name.toLowerCase().includes("wanita"))
            );

            if (femaleVoice) {
                utterance.voice = femaleVoice;
            }

            speechSynthesis.cancel(); // hentikan suara sebelumnya
            speechSynthesis.speak(utterance);
        };

        // langsung bicara
        speak();
    }

    // klik tombol panggil
    btnPanggil.addEventListener("click", () => {
        if (speechSynthesis.getVoices().length === 0) {
            // jika voice belum siap, tunggu dulu
            speechSynthesis.onvoiceschanged = panggilNomor;
        } else {
            panggilNomor();
        }
    });
});
</script>

@endsection
