@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Ringkasan Pemeriksaan</h2>
            <p class="text-gray-600">Publikasi: {{ $publication->name }}</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Total Komentar</h4>
            <p class="text-3xl font-bold mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Komentar Terbuka</h4>
            <p class="text-3xl font-bold mt-1 text-red-500">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="text-sm font-medium text-gray-500">Komentar Selesai</h4>
            <p class="text-3xl font-bold mt-1 text-green-500">{{ $stats['done'] }}</p>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <h4 class="text-md font-semibold mb-3">Progres Penyelesaian</h4>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-blue-600 h-4 rounded-full text-center text-white text-xs leading-4" style="width: {{ $stats['percentage'] }}%">
                {{ $stats['percentage'] }}%
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Komentar -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold mb-4">Detail Komentar</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hal.</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemeriksa</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentar</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($comments as $comment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $comment->page_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $comment->user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800">{{ $comment->content }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($comment->status == 'done')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Terbuka
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada komentar untuk dokumen ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection