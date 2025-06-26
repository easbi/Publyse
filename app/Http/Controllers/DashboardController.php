<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Ambil data publikasi yang dibuat oleh user ini
        // Kita juga 'eager load' relasi reviewers untuk efisiensi
        $myCreations = $user->createdPublications()
                            ->with('reviewers')
                            ->latest()
                            ->get();

        // Ambil data publikasi yang ditugaskan untuk direview oleh user ini
        $myAssignments = $user->assignedPublications()
                              ->with('creator')
                              ->latest()
                              ->get();

        // Kirim kedua koleksi data tersebut ke view
        return view('dashboard', compact('myCreations', 'myAssignments'));
    }
}
