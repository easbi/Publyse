@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h2 class="text-2xl font-bold mb-6">Cetak Surat Persetujuan</h2>
        <p class="mb-4 text-sm text-gray-600">
            <span class="font-semibold">Publikasi:</span> {{ $publication->name }}
        </p>

        @if ($errors->any())
            <div class="mb-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('superilis.generate', $publication) }}" method="POST" class="space-y-5">
            @csrf

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="nomor_surat" class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                    <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat', $nomorSurat) }}" class="form-input w-full bg-gray-100" readonly>
                </div>
                <div class="flex-1">
                    <label for="tanggal_surat" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat</label>
                    <input type="date" name="tanggal_surat" id="tanggal_surat" value="{{ old('tanggal_surat', now()->format('Y-m-d')) }}" required class="form-input w-full">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label for="nama_kepala" class="block text-sm font-medium text-gray-700 mb-1">Nama Kepala</label>
                    <input type="text" name="nama_kepala" id="nama_kepala" value="{{ old('nama_kepala', \App\Models\Setting::where('key', 'last_head_name')->first()->value ?? '') }}" required class="form-input w-full">
                </div>
                <div class="flex-1">
                    <label for="nip_kepala" class="block text-sm font-medium text-gray-700 mb-1">NIP Kepala</label>
                    <input type="text" name="nip_kepala" id="nip_kepala" value="{{ old('nip_kepala', \App\Models\Setting::where('key', 'last_head_nip')->first()->value ?? '') }}" required class="form-input w-full">
                </div>
            </div>

            <div>
                <label for="tipe_buku" class="block text-sm font-medium text-gray-700 mb-1">Tipe Buku</label>
                <select name="tipe_buku" id="tipe_buku" class="form-select w-full">
                    <option value="ARC" {{ old('tipe_buku') == 'ARC' ? 'selected' : '' }}>ARC</option>
                    <option value="Non-ARC" {{ old('tipe_buku') == 'Non-ARC' ? 'selected' : '' }}>Non-ARC</option>
                </select>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">
                    Generate PDF
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
