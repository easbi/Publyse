<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use DB;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assignment =  DB::table('assign_pemeriksa')
            ->join('users', 'assign_pemeriksa.pemeriksa_nip', '=', 'users.nip')
            ->join('master_publikasi', 'master_publikasi.id', '=', 'assign_pemeriksa.publikasi_id')
            ->select(
                'users.fullname AS nama_pemeriksa', 
                'master_publikasi.nama_publikasi as nama_publikasi',
                'assign_pemeriksa.*')
            ->get();
        return view('assignment.index', compact('assignment'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $publikasi =  DB::table('master_publikasi')->get();
        $users =  DB::table('users')->select('nip', 'fullname')->get();
        return view('assignment.create', compact('publikasi', 'users'));
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
            'pemeriksa_nip' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (count($value) !== count(array_unique($value))) {
                        $fail('Anggota pemeriksa tidak boleh sama.');
                    }
                },
            ],
            'pemeriksa_nip.*' => 'exists:users,nip', // Pastikan NIP valid
        ]);

        foreach ($request->pemeriksa_nip as $nip) {
            Assignment::create([
                'publikasi_id' => $request->publikasi_id,
                'pemeriksa_nip' => $nip,
            ]);
        }

        return redirect()->route('assignment.index')
                        ->with('success','assignment Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function show(Assignment $assignment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function edit(Assignment $assignment)
    {
        $publikasi =  DB::table('master_publikasi')->get();
        $users =  DB::table('users')->select('nip', 'fullname')->get();
        return view('assignment.edit', compact('assignment', 'publikasi', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'publikasi_id' => 'required',
            'pemeriksa_nip' => 'required',
        ]);
        
        $assignment->update([
            'publikasi_id' => $request->publikasi_id,
            'pemeriksa_nip' => $request->pemeriksa_nip,
        ]);

        // Redirect ke halaman yang sesuai dengan pesan sukses
        return redirect()->route('assignment.index')->with('success', 'assignment berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Assignment $assignment)
    {
        $assignment->delete();
        return redirect('/assignment');
    }
}
