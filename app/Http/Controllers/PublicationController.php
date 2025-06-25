<?php

namespace App\Http\Controllers;
use App\Models\Publication;
use Illuminate\Http\Request;

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
     * Menampilkan detail satu publikasi, termasuk antarmuka reviewer PDF.
     */
    public function show(Publication $publication)
    {
        $latestDocument = $publication->documents()
                                  ->with(['comments.user'])
                                  ->latest()
                                  ->first();
        if ($latestDocument) {
            $latestDocument->pdf_url = asset('storage/' . $latestDocument->stored_path);
        }

        return view('publications.show', compact('publication', 'latestDocument'));
        }

    // Method lain (create, store, edit, update, destroy) akan kita isi nanti.
}

