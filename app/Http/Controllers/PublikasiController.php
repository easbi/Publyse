<?php

namespace App\Http\Controllers;

use App\Models\Publikasi;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use DB;
use Carbon\Carbon;

class PublikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $publikasi =  DB::table('master_publikasi')->get();
        return view('publikasi.index', compact('publikasi'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('publikasi.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_publikasi' => 'required|string|max:255',
            'batas_upload' => 'required|date',
            'waktu_rilis' => 'required|date',
            'batas_pemeriksaan' => 'required|date',
            'batas_tl' => 'required|date',
        ]);

        $result = Publikasi::create([
            'nama_publikasi' => $request->nama_publikasi,
            'batas_upload' => $request->batas_upload,
            'waktu_rilis' => $request->waktu_rilis,
            'batas_pemeriksaan' => $request->batas_pemeriksaan,
            'batas_tl' => $request->batas_tl,
        ]);

        return redirect()->route('publikasi.index')
                        ->with('success','Publikasi Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Publikasi  $publikasi
     * @return \Illuminate\Http\Response
     */
    public function show(Publikasi $publikasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Publikasi  $publikasi
     * @return \Illuminate\Http\Response
     */
    public function edit(Publikasi $publikasi)
    {
        return view('publikasi.edit', compact('publikasi'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Publikasi  $publikasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Publikasi $publikasi)
    {
        $request->validate([
            'nama_publikasi' => 'required|string|max:255',
            'batas_upload' => 'required|date',
            'waktu_rilis' => 'required|date',
            'batas_pemeriksaan' => 'required|date',
            'batas_tl' => 'required|date',
        ]);

        $publikasi->update([
            'nama_publikasi' => $request->nama_publikasi,
            'batas_upload' => $request->batas_upload,
            'waktu_rilis' => $request->waktu_rilis,
            'batas_pemeriksaan' => $request->batas_pemeriksaan,
            'batas_tl' => $request->batas_tl,
        ]);

        // Redirect ke halaman yang sesuai dengan pesan sukses
        return redirect()->route('publikasi.index')->with('success', 'publikasi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Publikasi  $publikasi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Publikasi $publikasi)
    {
        $publikasi->delete();
        return redirect('/publikasi');
    }
}
