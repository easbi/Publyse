<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use DB;
use Carbon\Carbon;

class PemeriksaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pemeriksaan =  DB::table('transaksi_pemeriksaan')
            ->join('users', 'transaksi_pemeriksaan.pemeriksa_nip', '=', 'users.nip')
            ->join('master_publikasi', 'master_publikasi.id', '=', 'transaksi_pemeriksaan.publikasi_id')
            ->select(
                'users.fullname AS nama_pemeriksa', 
                'master_publikasi.nama_publikasi as nama_publikasi',
                'transaksi_pemeriksaan.*')
            ->get();
        return view('pemeriksaan.index', compact('pemeriksaan'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $publikasi =  DB::table('master_publikasi')->get();
        $users =  DB::table('users')->select('nip', 'fullname')->get();

        $publikasi=  DB::table('assign_pemeriksa')
            ->where('assign_pemeriksa.pemeriksa_nip','=', '199602182019011002' ) #Auth::user()->nip
            ->join('master_publikasi', 'assign_pemeriksa.publikasi_id', '=', 'master_publikasi.id')
            ->join('users', 'assign_pemeriksa.pemeriksa_nip', '=', 'users.nip')
            ->select(
                'users.fullname AS nama_pemeriksa', 
                'master_publikasi.nama_publikasi as nama_publikasi',
                'assign_pemeriksa.*')
            ->get();
        // dd($assigntim);
        
        return view('pemeriksaan.create', compact('publikasi', 'users'));
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
            'publikasi_id' => 'required',
            'pemeriksa_nip' => 'required',
            'bagian_pemeriksaan' => 'required',
            'halaman' => 'required',
            'hasil_pemeriksaan' => 'required',
        ]);

        $result = Pemeriksaan::create([
            'publikasi_id' => $request->publikasi_id,
            'pemeriksa_nip' => $request->pemeriksa_nip,
            'bagian_pemeriksaan' => $request->bagian_pemeriksaan,
            'halaman' => $request->halaman,
            'hasil_pemeriksaan' => $request->hasil_pemeriksaan,
        ]);

        return redirect()->route('pemeriksaan.index')
                        ->with('success','assignment Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pemeriksaan  $pemeriksaan
     * @return \Illuminate\Http\Response
     */
    public function show(Pemeriksaan $pemeriksaan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pemeriksaan  $pemeriksaan
     * @return \Illuminate\Http\Response
     */
    public function edit(Pemeriksaan $pemeriksaan)
    {
        $publikasi =  DB::table('master_publikasi')->get();
        $users =  DB::table('users')->select('nip', 'fullname')->get();
        return view('pemeriksaan.edit', compact('pemeriksaan', 'publikasi', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pemeriksaan  $pemeriksaan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pemeriksaan $pemeriksaan)
    {
        $request->validate([
            'publikasi_id' => 'required',
            'pemeriksa_nip' => 'required',
            'bagian_pemeriksaan' => 'required',
            'halaman' => 'required',
            'hasil_pemeriksaan' => 'required',
        ]);
        
        $pemeriksaan->update([
            'publikasi_id' => $request->publikasi_id,
            'pemeriksa_nip' => $request->pemeriksa_nip,
            'bagian_pemeriksaan' => $request->bagian_pemeriksaan,
            'halaman' => $request->halaman,
            'hasil_pemeriksaan' => $request->hasil_pemeriksaan,
        ]);

        // Redirect ke halaman yang sesuai dengan pesan sukses
        return redirect()->route('pemeriksaan.index')->with('success', 'pemeriksaan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pemeriksaan  $pemeriksaan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pemeriksaan $pemeriksaan)
    {
        $pemeriksaan->delete();
        return redirect('/pemeriksaan');
    }
}
