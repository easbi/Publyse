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
            'document_file' => 'required|file|mimes:pdf|max:10240',
            'thumbnail_data' => 'nullable|string', // Validasi data gambar base64
        ]);

        $lastVersion = $publication->documents()->max('version') ?? 0;
        $newVersion = $lastVersion + 1;

        $file = $request->file('document_file');
        $path = $file->store('documents', 'public');

        $thumbnailPath = null;
        // Cek jika ada data thumbnail yang dikirim
        if ($request->filled('thumbnail_data')) {
            try {
                // Decode data base64 dan simpan sebagai file
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('thumbnail_data')));
                $thumbnailPath = 'thumbnails/' . pathinfo($path, PATHINFO_FILENAME) . '.jpg';
                Storage::disk('public')->put($thumbnailPath, $imageData);
            } catch (\Exception $e) {
                report($e);
                $thumbnailPath = null;
            }
        }

        Document::create([
            'publication_id' => $publication->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'version' => $newVersion,
            'uploader_id' => auth()->id(),
        ]);

        return redirect()->route('publications.show', $publication)
                        ->with('success', 'Versi baru (v'.$newVersion.') berhasil diunggah!');
    }
}
