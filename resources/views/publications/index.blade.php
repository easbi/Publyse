@extends('layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">

            {{-- Header dengan Tombol Tambah --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Daftar Publikasi</h2>
                <a href="{{ route('publications.create') }}" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    + Tambah Publikasi
                </a>
            </div>

            {{-- Notifikasi Pesan Sukses --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="space-y-4">
                @forelse ($publications as $publication)
                    <div class="p-4 border rounded-lg flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $publication->name }}</h3>
                            <p class="text-sm text-gray-600">Batas Waktu Review: {{ \Carbon\Carbon::parse($publication->review_deadline)->isoFormat('D MMMM YYYY') }}</p>
                        </div>
                        <div class="flex gap-2">
                            {{-- Tombol "Tugaskan" hanya muncul jika user boleh 'mengelola' publikasi --}}
                            @can('manage-publication', $publication)
                                <a href="{{ route('publications.assign.form', $publication) }}" class="px-3 py-2 bg-yellow-500 text-white text-sm rounded-md hover:bg-yellow-600">
                                    Tugaskan
                                </a>
                            @endcan

                            @can('view-publication', $publication)
                                <a href="{{ route('publications.show', $publication) }}" class="px-3 py-2 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600">
                                    Lihat & Review
                                </a>
                            @endcan
                        </div>
                    </div>
                @empty
                    <p>Belum ada publikasi yang ditambahkan.</p>
                @endforelse
            </div>
            
        </div>
    </div>
@endsection