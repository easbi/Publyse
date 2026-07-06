<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $pendingTokenSessionKey = 'document-upload-token:' . $publication->id;
        $usedTokenSessionKey = 'document-upload-used:' . $publication->id;
        $uploadToken = $request->input('upload_token') ?: session()->get($pendingTokenSessionKey, (string) Str::uuid());
        $usedTokens = session()->get($usedTokenSessionKey, []);

        if (in_array($uploadToken, $usedTokens, true)) {
            return redirect()->route('publications.show', $publication)
                ->with('error', 'Unggahan ini sudah diproses. Silakan tunggu sebentar atau muat ulang halaman.');
        }

        session()->put($usedTokenSessionKey, array_values(array_unique(array_merge($usedTokens, [$uploadToken]))));

        $lastVersion = $publication->documents()->max('version') ?? 0;
        $newVersion = $lastVersion + 1;

        $path = $request->file('document_file')->store('documents', 'public');
        $source = storage_path('app/public/' . $path);
        $destination = public_path('storage/' . $path);

        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        if (file_exists($source)) {
            copy($source, $destination);
        }

        Document::create([
            'publication_id' => $publication->id,
            'original_filename' => $request->file('document_file')->getClientOriginalName(),
            'stored_path' => $path,
            'version' => $newVersion,
            'uploader_id' => auth()->id(),
        ]);

        session()->put($pendingTokenSessionKey, (string) Str::uuid());

        return redirect()->route('publications.show', $publication)
                        ->with('success', 'Versi baru (v'.$newVersion.') berhasil diunggah!');
    }
}
?>
