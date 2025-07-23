<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Publication;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PublicationController extends Controller
{
    /**
     * Menampilkan daftar semua publikasi.
     */
    public function index()
    {
        $publications = Publication::latest()->get();
        // dd($publications);
        return view('publications.index', compact('publications'));
    }

    /**
     * Menampilkan form untuk membuat publikasi baru.
     */
    public function create()
    {
        // Method ini hanya bertugas menampilkan view
        return view('publications.create');
    }

    /**
     * Menyimpan publikasi baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'release_date' => 'required|date',
            'review_deadline' => 'required|date|after_or_equal:today',
            'document_file' => 'required|file|mimes:pdf|max:10240', // max 10MB
        ]);

        $publication = Publication::create([
            'name' => $validated['name'],
            'release_date' => $validated['release_date'],
            'review_deadline' => $validated['review_deadline'],
            'creator_id' => auth()->id(),
        ]);

        if ($publication) {
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
                'version' => 1,
                'uploader_id' => auth()->id(),
            ]);
        }

        return redirect()->route('publications.index')
                        ->with('success', 'Publikasi baru berhasil ditambahkan!');
    }


    /**
     * Menampilkan form untuk mengedit publikasi yang sudah ada.
     */
    public function edit(Publication $publication)
    {
        // Otorisasi: Hanya pembuat yang boleh mengedit
        $this->authorize('manage-publication', $publication);

        return view('publications.edit', compact('publication'));
    }

    /**
     * Memperbarui data publikasi di database.
     */
    public function update(Request $request, Publication $publication)
    {
        $this->authorize('manage-publication', $publication);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'release_date' => 'required|date',
            'review_deadline' => 'required|date|after_or_equal:today',
        ]);

        $publication->update($validated);

        return redirect()->route('dashboard')->with('success', 'Data publikasi berhasil diperbarui.');
    }

    /**
     * Menghapus publikasi dan semua file terkait.
     */
    public function destroy(Publication $publication)
    {
        $this->authorize('manage-publication', $publication);

        // Praktik terbaik: Hapus file fisik dari storage sebelum menghapus record database
        foreach ($publication->documents as $document) {
            Storage::disk('public')->delete($document->stored_path);
        }

        $publication->delete();

        return redirect()->route('dashboard')->with('success', 'Publikasi berhasil dihapus.');

        // Hapus re redirect()->route('dashboard')->with('success', 'Publikasi berhasil dihapus.');
    }


    /**
     * Menampilkan detail satu publikasi, termasuk antarmuka reviewer PDF.
     */
    public function show(Request $request, Publication $publication)
    {
        Gate::authorize('view-publication', $publication);

        // Ambil semua versi dokumen untuk ditampilkan di dropdown
        $allVersions = $publication->documents()->orderBy('version', 'desc')->get();

        if ($allVersions->isEmpty()) {
            // Jika tidak ada dokumen sama sekali
            return view('publications.show', compact('publication', 'allVersions'));
        }

        // Tentukan dokumen mana yang akan ditampilkan
        // Jika ada parameter 'version' di URL, cari versi itu. Jika tidak, ambil yang terbaru.
        $versionToShow = $request->query('version')
            ? $allVersions->where('version', $request->query('version'))->first()
            : $allVersions->first();

        // Jika versi yang diminta tidak ada, alihkan ke versi terbaru
        if (!$versionToShow) {
            return redirect()->route('publications.show', $publication);
        }

        // Eager load komentar untuk versi yang akan ditampilkan
        $versionToShow->load('comments.user');

        // Pastikan accessor 'pdf_url' disertakan
        $versionToShow->makeVisible(['pdf_url']);

        return view('publications.show', compact('publication', 'allVersions', 'versionToShow'));
    }


    /**
     * Menampilkan form untuk menugaskan pemeriksa ke sebuah publikasi.
     */
    public function assignForm(Publication $publication)
    {
        Gate::authorize('manage-publication', $publication);

        // Ambil semua user untuk ditampilkan sebagai calon pemeriksa
        // Di aplikasi nyata, Anda mungkin ingin memfilter hanya user dengan role 'pemeriksa'
        $users = User::orderBy('fullname')->get();

        // Kirim data publikasi dan daftar user ke view
        return view('publications.assign', compact('publication', 'users'));
    }

    /**
     * Memperbarui daftar pemeriksa untuk sebuah publikasi.
     */
    public function syncReviewers(Request $request, Publication $publication)
    {
        Gate::authorize('manage-publication', $publication);

        // Validasi input, pastikan 'reviewers' adalah sebuah array (bisa juga kosong)
        $request->validate([
            'reviewers' => 'nullable|array',
            'reviewers.*' => 'exists:users,id', // Pastikan setiap ID user ada di database
        ]);

        // Gunakan sync() untuk cara yang efisien dalam memperbarui pivot table.
        // Metode ini akan otomatis menambah/menghapus relasi sesuai input.
        // Kita juga bisa menambahkan ID user yang menugaskan.
        $reviewers = [];
        if ($request->has('reviewers')) {
            foreach ($request->reviewers as $reviewerId) {
                $reviewers[$reviewerId] = ['assignor_id' => auth()->id()];
            }
        }

        $publication->reviewers()->sync($reviewers);


        // Alihkan kembali ke halaman daftar dengan pesan sukses
        return redirect()->route('publications.index')
                         ->with('success', 'Daftar pemeriksa untuk publikasi "' . $publication->name . '" berhasil diperbarui.');
    }

    public function summary(Publication $publication)
    {
        // Otorisasi: Hanya yang boleh melihat publikasi yang bisa mengakses ringkasan
        Gate::authorize('view-publication', $publication);

        // Ambil SEMUA dokumen dari publikasi ini (semua versi)
        $documents = $publication->documents()->orderBy('version', 'desc')->get();

        // Jika tidak ada dokumen sama sekali, alihkan kembali dengan pesan
        if ($documents->isEmpty()) {
            return redirect()->back()->with('error', 'Publikasi ini tidak memiliki dokumen untuk diringkas.');
        }

        // Ambil semua komentar dari SEMUA dokumen/versi
        $allComments = collect();
        $statsByVersion = collect();

        foreach ($documents as $document) {
            // Ambil komentar untuk dokumen ini (hanya parent comments, bukan replies)
            $comments = $document->comments()
                ->whereNull('parent_id')
                ->with(['user', 'replies.user']) // Include replies juga
                ->get();

            // Tambahkan informasi versi ke setiap komentar
            $comments->each(function ($comment) use ($document) {
                $comment->document_version = $document->version;
                $comment->document_filename = $document->original_filename;
            });

            // Hitung statistik per versi
            $totalComments = $comments->count();
            $doneComments = $comments->where('status', 'done')->count();
            $openComments = $totalComments - $doneComments;
            $completionPercentage = ($totalComments > 0) ? round(($doneComments / $totalComments) * 100) : 0;

            $statsByVersion->put($document->version, [
                'total' => $totalComments,
                'done' => $doneComments,
                'open' => $openComments,
                'percentage' => $completionPercentage,
                'document' => $document
            ]);

            // Tambahkan ke koleksi semua komentar
            $allComments = $allComments->merge($comments);
        }

        // Hitung statistik keseluruhan (semua versi)
        $totalCommentsAll = $allComments->count();
        $doneCommentsAll = $allComments->where('status', 'done')->count();
        $openCommentsAll = $totalCommentsAll - $doneCommentsAll;
        $completionPercentageAll = ($totalCommentsAll > 0) ? round(($doneCommentsAll / $totalCommentsAll) * 100) : 0;

        $overallStats = [
            'total' => $totalCommentsAll,
            'done' => $doneCommentsAll,
            'open' => $openCommentsAll,
            'percentage' => $completionPercentageAll,
        ];

        // Statistik tambahan
        $additionalStats = [
            'total_versions' => $documents->count(),
            'latest_version' => $documents->first()->version,
        ];

        // Kirim data ke view yang sudah dimodifikasi
        return view('publications.summary', compact(
            'publication',
            'allComments',
            'statsByVersion',
            'overallStats',
            'additionalStats'
        ));
    }
}

