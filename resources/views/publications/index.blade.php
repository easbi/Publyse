
@extends('layouts.app')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold mb-4">Daftar Publikasi</h2>
            <div class="space-y-4">
                @forelse ($publications as $publication)
                    <div class="p-4 border rounded-lg flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-lg">{{ $publication->name }}</h3>
                            <p class="text-sm text-gray-600">Batas Waktu Review: {{ $publication->review_deadline }}</p>
                        </div>
                        <a href="{{ route('publications.show', $publication) }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            Lihat & Review
                        </a>
                    </div>
                @empty
                    <p>Belum ada publikasi yang ditambahkan.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection