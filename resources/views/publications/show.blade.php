@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Review: {{ $publication->name }}</h2>
        @can('manage-publication', $publication)
            <button onclick="document.getElementById('upload-modal').classList.remove('hidden')" class="px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600">
                + Unggah Versi Baru
            </button>
        @endcan
    </div>

    @if ($allVersions->isNotEmpty())
        <div class="mb-6 flex items-center gap-4 bg-white p-4 rounded-lg shadow-sm">
            <label for="version-selector" class="font-semibold">Pilih Versi:</label>
            <select id="version-selector" onchange="window.location.href = this.value;" class="rounded-md border-gray-300 shadow-sm">
                @foreach ($allVersions as $version)
                    <option value="{{ route('publications.show', ['publication' => $publication, 'version' => $version->version]) }}"
                            @if($version->id === $versionToShow->id) selected @endif>
                        Versi {{ $version->version }} (diunggah {{ $version->created_at->diffForHumans() }})
                    </option>
                @endforeach
            </select>
        </div>

        <div
            id="pdf-reviewer-app"
            data-document='{{ $versionToShow->toJson() }}'
            data-user='{{ auth()->user()->toJson() }}'
            data-api-store-url="{{ route('api.api.comments.store') }}"
            data-api-update-url-template="{{ route('api.api.comments.update', ['comment' => 'COMMENT_ID']) }}"
            data-api-delete-url-template="{{ route('api.api.comments.destroy', ['comment' => 'COMMENT_ID']) }}"
            data-api-status-url-template="{{ route('api.api.comments.updateStatus', ['comment' => 'COMMENT_ID']) }}"
            >
            <div class="flex items-center justify-center bg-white p-20 rounded-lg shadow">
                <p class="text-lg text-gray-500">Memuat Antarmuka Reviewer...</p>
            </div>
        </div>

    @else
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <p class="font-semibold text-gray-700">Publikasi ini belum memiliki dokumen PDF untuk direview.</p>
        </div>
    @endif


    <!-- Modal untuk Upload Versi Baru -->
    <div id="upload-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Unggah Dokumen Versi Baru</h3>
            <form action="{{ route('documents.store', $publication) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label for="document_file" class="block text-sm font-medium text-gray-700">Pilih File PDF</label>
                    <input type="file" name="document_file" id="document_file" required
                           class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">Hanya format .PDF, maksimal ukuran 10MB.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Unggah</button>
                </div>
            </form>
        </div>
    </div>
@endsection
