@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 dark:bg-green-800/20 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Kolom: Publikasi yang Saya Buat -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Header kartu dibuat flex -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Publikasi yang Saya Buat</h3>
                            <a href="{{ route('publications.create') }}" class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-md hover:bg-green-600 flex-shrink-0">+ Tambah</a>
                        </div>
                        <div class="space-y-4">
                            @forelse ($myCreations as $publication)
                                <div class="p-4 border rounded-lg dark:border-gray-700">
                                    <p class="font-semibold">{{ $publication->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Jumlah Pemeriksa: {{ $publication->reviewers->count() }}
                                    </p>
                                    <!-- Tombol dibungkus dengan div yang bisa wrap -->
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @can('view-publication', $publication)
                                            <a href="{{ route('publications.summary', $publication) }}" class="px-3 py-1 bg-gray-500 text-white text-xs rounded-md hover:bg-gray-600">Ringkasan</a>
                                        @endcan
                                        @can('manage-publication', $publication)
                                            <a href="{{ route('publications.assign.form', $publication) }}" class="px-3 py-1 bg-yellow-500 text-white text-xs rounded-md hover:bg-yellow-600">Tugaskan</a>
                                            <a href="{{ route('publications.edit', $publication) }}" class="px-3 py-1 bg-slate-500 text-white text-xs rounded-md hover:bg-slate-600">Edit</a>
                                            <form action="{{ route('publications.destroy', $publication) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus publikasi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded-md hover:bg-red-700">Hapus</button>
                                            </form>
                                        @endcan
                                        @can('view-publication', $publication)
                                            <a href="{{ route('publications.show', $publication) }}" class="px-3 py-1 bg-blue-500 text-white text-xs rounded-md hover:bg-blue-600">Review</a>
                                        @endcan
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Anda belum membuat publikasi apapun.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Kolom: Tugas Pemeriksaan Saya -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Tugas Pemeriksaan Saya</h3>
                        <div class="space-y-4">
                            @forelse ($myAssignments as $publication)
                                <div class="p-4 border rounded-lg dark:border-gray-700">
                                    <p class="font-semibold">{{ $publication->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Dibuat oleh: {{ $publication->creator->name }}
                                    </p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <a href="{{ route('publications.summary', $publication) }}" class="px-3 py-1 bg-gray-500 text-white text-xs rounded-md hover:bg-gray-600">Ringkasan</a>
                                        <a href="{{ route('publications.show', $publication) }}" class="px-3 py-1 bg-indigo-500 text-white text-xs rounded-md hover:bg-indigo-600">Mulai Review</a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Tidak ada tugas pemeriksaan untuk Anda saat ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
