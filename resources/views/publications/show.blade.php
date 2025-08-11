@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Header Card - Konsisten dengan index.blade.php -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">Review: {{ $publication->name }}</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Kelola dan review dokumen publikasi dengan sistem komentar interaktif
                        </p>
                    </div>
                    @can('manage-publication', $publication)
                        <button onclick="document.getElementById('upload-modal').classList.remove('hidden')"
                                class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm font-semibold">
                            + Unggah Versi Baru
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        @if ($allVersions->isNotEmpty())
            <!-- Version Selector Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center gap-4">
                        <label for="version-selector" class="text-sm font-semibold text-gray-700 whitespace-nowrap">
                            Pilih Versi:
                        </label>
                        <select id="version-selector"
                                onchange="window.location.href = this.value;"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @foreach ($allVersions as $version)
                                <option value="{{ route('publications.show', ['publication' => $publication, 'version' => $version->version]) }}"
                                        @if($version->id === $versionToShow->id) selected @endif>
                                    Versi {{ $version->version }} (diunggah {{ $version->created_at->diffForHumans() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- PDF Reviewer Card - Tanpa padding untuk full container -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div id="pdf-reviewer-app"
                     data-document='{{ $versionToShow->toJson() }}'
                     data-user='{{ auth()->user()->toJson() }}'
                     data-api-store-url="{{ route('api.api.comments.store') }}"
                     data-api-update-url-template="{{ route('api.api.comments.update', ['comment' => 'COMMENT_ID']) }}"
                     data-api-delete-url-template="{{ route('api.api.comments.destroy', ['comment' => 'COMMENT_ID']) }}"
                     data-api-status-url-template="{{ route('api.api.comments.updateStatus', ['comment' => 'COMMENT_ID']) }}"
                     class="w-full">

                    <!-- Loading State -->
                    <div class="flex items-center justify-center bg-gray-50 p-20">
                        <div class="text-center">
                            <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-900">Memuat Antarmuka Reviewer...</p>
                            <p class="text-sm text-gray-500 mt-2">Harap tunggu sebentar, sistem sedang mempersiapkan dokumen PDF</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 bg-blue-50 border-l-4 border-blue-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Caraa Menggunakan Reviewer</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Klik ikon <strong>titik</strong> untuk menambah komentar pada lokasi tertentu</li>
                                    <li>Klik ikon <strong>area</strong> untuk menambah komentar pada area tertentu</li>
                                    <li>Gunakan checkbox untuk menandai komentar sebagai selesai</li>
                                    <li>Filter komentar berdasarkan status: Semua, Terbuka, atau Selesai</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- Empty State -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Dokumen</h3>
                        <p class="text-gray-600 mb-6">Publikasi ini belum memiliki dokumen PDF untuk direview.</p>
                        @can('manage-publication', $publication)
                            <button onclick="document.getElementById('upload-modal').classList.remove('hidden')"
                                    class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm font-semibold">
                                + Unggah Dokumen Pertama
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal untuk Upload Versi Baru -->
<div id="upload-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Unggah Dokumen Versi Baru</h3>
                <button onclick="document.getElementById('upload-modal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('documents.store', $publication) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label for="document_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File PDF
                    </label>
                    <input type="file" name="document_file" id="document_file" required accept=".pdf"
                           class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    <p class="mt-1 text-xs text-gray-500">Hanya format .PDF, maksimal ukuran 10MB.</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button"
                            onclick="document.getElementById('upload-modal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400 text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm font-semibold">
                        Unggah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Close modal when clicking outside
document.getElementById('upload-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
