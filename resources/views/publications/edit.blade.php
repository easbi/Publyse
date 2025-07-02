@extends('layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold mb-6">Edit Publikasi</h2>

            {{-- Perhatikan action dan method-nya --}}
            <form action="{{ route('publications.update', $publication) }}" method="POST">
                @csrf
                @method('PUT') {{-- Wajib untuk request UPDATE --}}

                <!-- Nama Publikasi -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Publikasi</label>
                    {{-- 'old' akan mengambil data lama jika validasi gagal, jika tidak, ambil dari $publication --}}
                    <input type="text" name="name" id="name" value="{{ old('name', $publication->name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Waktu Rilis -->
                <div class="mb-4">
                    <label for="release_date" class="block text-sm font-medium text-gray-700">Waktu Rilis</label>
                    <input type="date" name="release_date" id="release_date" value="{{ old('release_date', $publication->release_date) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('release_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batas Pemeriksaan -->
                <div class="mb-6">
                    <label for="review_deadline" class="block text-sm font-medium text-gray-700">Batas Waktu Pemeriksaan</label>
                    <input type="date" name="review_deadline" id="review_deadline" value="{{ old('review_deadline', $publication->review_deadline) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('review_deadline')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Simpan -->
                <div class="flex items-center justify-end">
                    <a href="{{ route('dashboard') }}" class="text-gray-600 mr-4">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update Publikasi
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection
