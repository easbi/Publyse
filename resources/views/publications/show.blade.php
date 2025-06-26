@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-bold mb-4">Review: {{ $publication->name }}</h2>

    {{-- INI ADALAH BAGIAN PENTING YANG MENCEGAH ERROR --}}
    @if ($latestDocument)
        {{-- Blok ini hanya akan berjalan jika $latestDocument TIDAK null --}}
        <div 
            id="pdf-reviewer-app" 
            data-document='{{ $latestDocument->toJson() }}'
            data-user='{{ auth()->user()->toJson() }}'
            data-api-store-url="{{ route('api.comments.store') }}"
            data-api-update-url-template="{{ route('api.comments.update', ['comment' => 'COMMENT_ID']) }}"
            data-api-delete-url-template="{{ route('api.comments.destroy', ['comment' => 'COMMENT_ID']) }}"
            data-api-status-url-template="{{ route('api.comments.updateStatus', ['comment' => 'COMMENT_ID']) }}">
            
            {{-- Pesan Loading Awal untuk Vue --}}
            <div class="flex items-center justify-center bg-white p-20 rounded-lg shadow">
                <p class="text-lg text-gray-500">Memuat Antarmuka Reviewer...</p>
            </div>
        </div>

    @else
        {{-- Blok ini akan berjalan jika $latestDocument ADALAH null --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <p class="font-semibold text-gray-700">Publikasi ini belum memiliki dokumen PDF untuk direview.</p>
                {{-- Di sini nanti bisa ditambahkan tombol/form untuk upload PDF --}}
            </div>
        </div>
    @endif
@endsection