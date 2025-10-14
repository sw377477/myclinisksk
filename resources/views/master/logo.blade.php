@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] bg-gradient-to-br from-gray-100 to-gray-200">
    <div class="w-full max-w-lg bg-white shadow-xl rounded-2xl p-8 border border-gray-200">
        <h2 class="text-3xl font-extrabold text-center mb-8 text-gray-800">
            ID lokasi <span class="text-blue-600">{{ session('idpay') }}</span>
        </h2>

        @php
            $iddata = session('iddata');
            $logo = \App\Models\Logo::where('iddata', $iddata)->first();
        @endphp

        @if ($logo && $logo->logo)
            <!-- Logo saat ini -->
            <div class="flex flex-col items-center mb-8">
                <p class="text-gray-500 mb-3">Logo Saat Ini:</p>
                <div class="w-48 h-48 flex items-center justify-center bg-gray-50 border rounded-xl shadow-[0_0_15px_3px_rgba(59,130,246,0.4)] overflow-hidden">
                    <img src="{{ asset('storage/'.$logo->logo) }}" 
                         alt="Logo {{ $iddata }}" 
                         class="w-full h-full object-contain transition-transform duration-300 hover:scale-105">
                </div>
            </div>

            <!-- Form Update -->
            <form action="{{ route('master.logo.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="iddata" value="{{ $iddata }}">

                <label id="dropZoneLogo" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50 transition group shadow-[0_0_10px_2px_rgba(59,130,246,0.3)]">
                    <input type="file" name="logo" id="fileInputLogo" class="hidden" required>
                    <span class="text-gray-500 group-hover:text-blue-600">Klik atau drag & drop untuk ganti logo</span>
                </label>

                <div id="previewLogo" class="flex justify-center mt-4"></div>

                <div class="flex justify-center gap-3">
                    <button type="submit" 
                        class="px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                        Simpan / Ganti
                    </button>
            </form>

            <!-- Form Hapus -->
            <form action="{{ route('master.logo.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" 
                    class="px-5 py-2 bg-red-500 text-white font-semibold rounded-lg shadow hover:bg-red-600 transition">
                    Hapus
                </button>
            </form>
        @else
            <!-- Form Upload Baru -->
            <form action="{{ route('master.logo.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="iddata" value="{{ $iddata }}">

                <div id="dropZoneLogo" 
                class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-xl bg-gray-50 hover:bg-blue-50 transition group shadow-[0_0_10px_2px_rgba(59,130,246,0.3)]">
                    <input type="file" name="logo" id="fileInputLogo" class="hidden" required>
                    <span class="text-gray-500 group-hover:text-blue-600">Drag & drop untuk upload logo</span>
                </div>

                <div id="previewLogo" class="flex justify-center mt-4"></div>

                <div class="flex justify-center">
                    <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                        Upload Logo
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("Script drag & drop jalan ✅"); // Debug log

    const dropZone = document.getElementById("dropZoneLogo");
    const fileInput = document.getElementById("fileInputLogo");
    const preview = document.getElementById("previewLogo");

    if (!dropZone || !fileInput || !preview) {
        console.warn("Elemen drag & drop tidak ditemukan ❌");
        return;
    }

    // drag over
    dropZone.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropZone.classList.add("bg-blue-50");
    });

    dropZone.addEventListener("dragleave", () => {
        dropZone.classList.remove("bg-blue-50");
    });

    // drop file
    dropZone.addEventListener("drop", (e) => {
        e.preventDefault();
        dropZone.classList.remove("bg-blue-50");

        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showPreview(fileInput.files[0]);
        }
    });

    // preview ketika pilih file lewat klik
    fileInput.addEventListener("change", () => {
        if (fileInput.files.length) {
            showPreview(fileInput.files[0]);
        }
    });

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.innerHTML = `<img src="${e.target.result}" class="w-48 h-48 object-contain border rounded-lg shadow-[0_0_10px_2px_rgba(59,130,246,0.4)]">`;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection

