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
        $this->authorize('manage-publication', $publication);

        $request->validate([
            'document_file' => 'required|file|mimes:pdf|max:30720',
        ]);

        $lastVersion = $publication->documents()->max('version') ?? 0;
        $newVersion = $lastVersion + 1;

        // SAMA PERSIS dengan PublicationController - menggunakan kondisi if
        if ($publication) { // Selalu true, tapi konsisten dengan PublicationController
            // Simpan file ke storage/app/public/documents
            $path = $request->file('document_file')->store('documents', 'public');

            // Copy file ke public/storage/documents/
            $source = storage_path('app/public/' . $path);
            $destination = public_path('storage/' . $path);

            // Buat folder tujuan jika belum ada
            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0755, true);
            }

            copy($source, $destination);

            // Buat record dokumen di database
            Document::create([
                'publication_id' => $publication->id,
                'original_filename' => $request->file('document_file')->getClientOriginalName(),
                'stored_path' => $path,
                'version' => $newVersion,
                'uploader_id' => auth()->id(),
            ]);
        }

        return redirect()->route('publications.show', $publication)
                        ->with('success', 'Versi baru (v'.$newVersion.') berhasil diunggah!');
    }
}
?>
