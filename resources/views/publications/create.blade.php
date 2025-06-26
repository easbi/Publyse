@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h2 class="text-2xl font-bold mb-6">Tambah Publikasi Baru</h2>

        <form action="{{ route('publications.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Nama Publikasi -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Publikasi</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label for="release_date" class="block text-sm font-medium text-gray-700">Waktu Rilis</label>
                    <input type="date" name="release_date" id="release_date" value="{{ old('release_date') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('release_date')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="review_deadline" class="block text-sm font-medium text-gray-700">Batas Waktu Pemeriksaan</label>
                    <input type="date" name="review_deadline" id="review_deadline" value="{{ old('review_deadline') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('review_deadline')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            <div class="mb-6">
                <label for="document_file" class="block text-sm font-medium text-gray-700">File Dokumen PDF</label>
                <input type="file" name="document_file" id="document_file" required
                class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                <p class="mt-1 text-xs text-gray-500">Hanya format .PDF, maksimal ukuran 10MB.</p>
                @error('document_file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>


            <!-- Tombol Simpan -->
            <div class="flex items-center justify-end">
                <a href="{{ route('publications.index') }}" class="text-gray-600 mr-4">Batal</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Simpan Publikasi
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
