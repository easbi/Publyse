<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Publication;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Menyimpan dokumen versi baru untuk sebuah publikasi.
     */
    public function store(Request $request, Publication $publication)
    {
        // Otorisasi: Hanya pembuat publikasi yang boleh mengunggah versi baru
        $this->authorize('manage-publication', $publication);

        // Validasi file yang diunggah
        $request->validate([
            'document_file' => 'required|file|mimes:pdf|max:10240', // max 10MB
        ]);

        // Tentukan nomor versi baru
        // Ambil versi terakhir, lalu tambahkan 1. Jika belum ada, mulai dari 1.
        $lastVersion = $publication->documents()->max('version') ?? 0;
        $newVersion = $lastVersion + 1;

        // Simpan file ke storage
        $path = $request->file('document_file')->store('documents', 'public');

        // Buat record baru di tabel documents
        Document::create([
            'publication_id' => $publication->id,
            'original_filename' => $request->file('document_file')->getClientOriginalName(),
            'stored_path' => $path,
            'version' => $newVersion,
            'uploader_id' => auth()->id(),
        ]);

        // Alihkan kembali ke halaman review (sekarang akan menampilkan versi terbaru)
        return redirect()->route('publications.show', $publication)
                         ->with('success', 'Versi baru (v'.$newVersion.') berhasil diunggah!');
    }
}
