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
        // dd(config('app.url'));
        // Mengambil dokumen versi terakhir dari publikasi ini
        $latestDocument = $publication->documents()->latest()->first();

        // Pastikan accessor 'pdf_url' kita ikut di dalam data JSON jika dokumen ada
        if ($latestDocument) {
            $latestDocument->makeVisible(['pdf_url']);
            // HENTIKAN DAN TAMPILKAN URL YANG DIHASILKAN
            // dd($latestDocument->pdf_url);
        }
                                      
        return view('publications.show', compact('publication', 'latestDocument'));
    }

    // Method lain (create, store, edit, update, destroy) akan kita isi nanti.
}

