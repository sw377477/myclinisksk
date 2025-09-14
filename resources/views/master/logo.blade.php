@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-700">Master Logo</h2>
        <a href="{{ route('master.logo.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Logo</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $row->iddata }}</td>
                        <td class="px-4 py-2">
                            @if($row->logo)
                                <img src="data:image/png;base64,{{ base64_encode(stream_get_contents($row->logo)) }}" alt="Logo" class="h-10">
                            @else
                                <span class="text-gray-400">Tidak ada logo</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('master.logo.edit',$row->iddata) }}" class="text-yellow-600 hover:text-yellow-800 mx-1"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('master.logo.destroy',$row->iddata) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Yakin hapus?')" class="text-red-600 hover:text-red-800 mx-1"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center py-4 text-gray-500">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
