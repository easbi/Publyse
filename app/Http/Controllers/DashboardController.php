<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Publication;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

         $isAdmin = $user->id === 1;

        if ($isAdmin) {
            // Admin melihat semua publikasi
            $myCreations = Publication::with('reviewers', 'creator')
                                    ->latest()
                                    ->get();

            $myAssignments = Publication::with('creator', 'reviewers')
                                      ->latest()
                                      ->get();
        } else {
            // User biasa hanya melihat publikasi mereka sendiri
            $myCreations = $user->createdPublications()
                                ->with('reviewers')
                                ->latest()
                                ->get();

            $myAssignments = $user->assignedPublications()
                                  ->with('creator')
                                  ->latest()
                                  ->get();
        }

        // Kirim data ke view beserta status admin
        return view('dashboard', compact('myCreations', 'myAssignments', 'isAdmin'));
    }
}
