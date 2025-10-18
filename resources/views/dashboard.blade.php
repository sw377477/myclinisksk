@extends('layouts.app')

@section('content')
<div class="p-1 space-y-6">
    {{-- === RSS FEED BERJALAN === --}}
    <div class="bg-gray-100 p-4 rounded-xl shadow mt-8">
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

    {{-- === KPI CARDS === --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-green-600 text-white p-4 rounded-2xl shadow-lg text-center">
            <p class="text-sm opacity-80">🎫 Nomor Antrian</p>
            <h2 id="nomorAntrian" class="text-4xl font-bold">{{ $lastQueue ?? '-' }}</h2>
            <button id="btnPrev" class="bg-white text-green-700 px-1 py-1 rounded">⟨ Prev</button>
            <button id="btnNext" class="bg-white text-green-700 px-1 py-1 rounded">Next ⟩</button>
            <button id="btnReset" class="bg-red-500 text-white px-1 py-1 rounded">Reset</button>
            <button id="btnCetak" class="bg-yellow-400 text-black px-1 py-1 rounded mt-1">🖨️</button>
            <button id="btnPanggil" class="bg-indigo-500 text-white px-1 py-1 rounded mt-1">🔊</button>
        </div>

        <div class="bg-blue-500 text-white p-4 rounded-2xl shadow-lg text-center">
            <p class="text-sm opacity-80">Kunjungan Hari Ini</p>
            <h2 class="text-4xl font-bold">{{ $todayCount }}</h2>
        </div>

        <div class="bg-orange-500 text-white p-4 rounded-2xl shadow-lg text-center">
            <p class="text-sm opacity-80">s/d Bulan Ini</p>
            <h2 class="text-4xl font-bold">{{ $monthCount }}</h2>
        </div>

        <div class="bg-purple-600 text-white p-4 rounded-2xl shadow-lg text-center">
            <p class="text-sm opacity-80">s/d Tahun Ini</p>
            <h2 class="text-4xl font-bold">{{ $yearCount }}</h2>
        </div>
    </div>

    {{-- === PER JENIS KUNJUNGAN === --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-gray-100 p-4 rounded-xl shadow text-center">
            <h3 class="text-lg font-semibold text-gray-700">Umum</h3>
            <p class="text-3xl font-bold text-green-600">{{ $jenisToday['Umum'] ?? 0 }}</p>
        </div>
        <div class="bg-gray-100 p-4 rounded-xl shadow text-center">
            <h3 class="text-lg font-semibold text-gray-700">BPJS</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $jenisToday['BPJS'] ?? 0 }}</p>
        </div>
        <div class="bg-gray-100 p-4 rounded-xl shadow text-center">
            <h3 class="text-lg font-semibold text-gray-700">Asuransi</h3>
            <p class="text-3xl font-bold text-purple-600">{{ $jenisToday['Asuransi'] ?? 0 }}</p>
        </div>
    </div>

    {{-- === GRAFIK KUNJUNGAN === --}}
    <div class="bg-white p-6 rounded-xl shadow mt-8">
        <h2 class="text-xl font-bold mb-4">Grafik Kunjungan s/d Tahun Ini</h2>
        <div class="relative h-64 w-full">
            <canvas id="chartKunjungan"></canvas>
        </div>
    </div>

    

</div>

{{-- === SCRIPT SECTION === --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

// === Render Chart ===
new Chart(document.getElementById('chartKunjungan'), {
    type: 'bar',
    data: {
        labels,
        datasets: Object.keys(grouped).map((jenis, i) => ({
            label: jenis,
            data: grouped[jenis],
            backgroundColor: ['#4CAF50', '#2196F3', '#9C27B0'][i % 3],
        }))
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } }
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

    // Ambil data antrian dari server
    async function getAntrian() {
        try {
            const res = await fetch(`/antrian?nocache=${Date.now()}`, { cache: "no-store" });
            const text = await res.text();
            const [nomorStr] = text.trim().split("|").map(s => s.trim());
            nomorEl.textContent = nomorStr || "0";
        } catch (e) {
            console.error("Gagal memuat antrian:", e);
            nomorEl.textContent = "0";
        }
    }

    // Kirim perubahan ke server
    async function simpanAntrian(action) {
        try {
            const res = await fetch("/antrian/update", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Cache-Control": "no-cache"
                },
                body: JSON.stringify({ action })
            });

            const data = await res.json();
            if (data && typeof data.nomor !== "undefined") {
                nomorEl.textContent = data.nomor;
            } else {
                await getAntrian(); // fallback
            }
        } catch (e) {
            console.error("Gagal menyimpan antrian:", e);
        }
    }

    // Pasang event listener
    prevBtn.addEventListener("click", () => simpanAntrian("prev"));
    nextBtn.addEventListener("click", () => simpanAntrian("next"));
    resetBtn.addEventListener("click", () => simpanAntrian("reset"));

    // Muat data pertama kali
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

    // fungsi untuk memainkan suara ding-dong sebelum panggilan
    function playDingDong() {
        const audio = new Audio("https://actions.google.com/sounds/v1/alarms/digital_watch_alarm_long.ogg");
        audio.play();
        return new Promise(resolve => {
            audio.onended = resolve;
        });
    }

    // fungsi untuk memanggil antrian
    function panggilNomor() {
        if (!nomorAntrianEl) {
            alert("Nomor antrian tidak ditemukan di halaman.");
            return;
        }

        const nomor = nomorAntrianEl.innerText.trim();
        const pesan = `Nomor antrian ${nomor}, silakan menuju loket.`;

        const speak = () => {
            const utterance = new SpeechSynthesisUtterance(pesan);
            utterance.lang = "id-ID";
            utterance.rate = 0.9;
            utterance.pitch = 1.1;
            utterance.volume = 1;

            // tunggu daftar voice selesai dimuat
            const voices = speechSynthesis.getVoices();

            // cari suara wanita Indonesia
            const femaleVoice = voices.find(v =>
                v.lang === "id-ID" &&
                (v.name.toLowerCase().includes("google") ||
                 v.name.toLowerCase().includes("female") ||
                 v.name.toLowerCase().includes("wanita"))
            );


            if (femaleVoice) {
                utterance.voice = femaleVoice;
            }

            speechSynthesis.cancel();
            speechSynthesis.speak(utterance);
        };

        // mainkan ding-dong dulu baru bicara
        playDingDong().then(speak);
    }

    // tombol klik panggil
    btnPanggil.addEventListener("click", () => {
        // kalau voice belum siap, tunggu dulu
        if (speechSynthesis.getVoices().length === 0) {
            speechSynthesis.onvoiceschanged = panggilNomor;
        } else {
            panggilNomor();
        }
    });
});
</script>

@endsection
