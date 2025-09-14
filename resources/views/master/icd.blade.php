@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-700">Master ICD</h2>
        <a href="{{ route('master.icd.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Kode ICD</th>
                    <th class="px-4 py-2">Diagnosis</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $row)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $row->kode_icd }}</td>
                        <td class="px-4 py-2">{{ $row->diagnosis }}</td>
                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('master.icd.edit',$row->kode_icd) }}" class="text-yellow-600 hover:text-yellow-800 mx-1"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('master.icd.destroy',$row->kode_icd) }}" method="POST" class="inline">
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
