<?php

namespace App\Http\Controllers;

use App\Models\Masternonkonten;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use DB;
use Carbon\Carbon;

class MasternonkontenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rulepemeriksaan =  DB::table('master_non_konten')->get();
        return view('masternonkonten.index', compact('rulepemeriksaan'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('masternonkonten.create');
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
            'bagian_publikasi' => 'required',
            'rincian' => 'required',
        ]);

        $result = Masternonkonten::create([
            'bagian_publikasi' => $request->bagian_publikasi,
            'rincian' => $request->rincian,
        ]);

        return redirect()->route('masternonkonten.create')
                        ->with('success','Rule Pemeriksaan Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Masternonkonten  $masternonkonten
     * @return \Illuminate\Http\Response
     */
    public function show(Masternonkonten $masternonkonten)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Masternonkonten  $masternonkonten
     * @return \Illuminate\Http\Response
     */
    public function edit(Masternonkonten $masternonkonten)
    {
        return view('masternonkonten.edit', compact('masternonkonten'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Masternonkonten  $masternonkonten
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Masternonkonten $masternonkonten)
    {        
        $request->validate([
            'bagian_publikasi' => 'required',
            'rincian' => 'required',
        ]);
        
        $masternonkonten->update([
            'bagian_publikasi' => $request->bagian_publikasi,
            'rincian' => $request->rincian,
        ]);

        // Redirect ke halaman yang sesuai dengan pesan sukses
        return redirect()->route('masternonkonten.index')->with('success', 'masternonkonten berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Masternonkonten  $masternonkonten
     * @return \Illuminate\Http\Response
     */
    public function destroy(Masternonkonten $masternonkonten)
    {
        $masternonkonten->delete();
        return redirect('/masternonkonten');
    }
}
