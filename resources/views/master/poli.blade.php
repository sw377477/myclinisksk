@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Master Poli</h2>
        <a href="{{ route('master.poli.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Tambah
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">ID Poli</th>
                    <th class="px-4 py-2">Poli</th>
                    <th class="px-4 py-2">Nama Medis</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $row->id_poli }}</td>
                        <td class="px-4 py-2">{{ $row->poli }}</td>
                        <td class="px-4 py-2">{{ $row->nama_medis }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('master.poli.edit', $row->id_poli) }}" class="text-yellow-600">Edit</a>
                            <form action="{{ route('master.poli.destroy', $row->id_poli) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Yakin hapus?')" class="text-red-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
