@extends('layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold mb-2">Tugaskan Pemeriksa</h2>
            <p class="text-gray-600 mb-6">Untuk Publikasi: <span class="font-semibold">{{ $publication->name }}</span></p>

            <form action="{{ route('publications.assign.sync', $publication) }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <p class="font-medium text-gray-800">Pilih satu atau lebih pemeriksa:</p>

                    {{-- Loop melalui semua user untuk menampilkannya --}}
                    @foreach ($users as $user)
                        <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 transition-colors">
                            <input type="checkbox" name="reviewers[]" value="{{ $user->id }}"
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                   {{-- Cek apakah user ini sudah ditugaskan sebelumnya --}}
                                   @if($publication->reviewers->contains($user->id)) checked @endif
                            >
                            <span class="ml-3 text-sm text-gray-700">{{ $user->name }} ({{ $user->email }})</span>
                        </label>
                    @endforeach
                </div>

                <!-- Tombol Simpan -->
                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('publications.index') }}" class="text-gray-600 mr-4">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Simpan Penugasan
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection