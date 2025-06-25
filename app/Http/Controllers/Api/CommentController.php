<?php

// app/Http/Controllers/Api/CommentController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'page_number' => 'required|integer',
            'type' => 'required|in:point,area',
            'position' => 'required|json',
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'document_id' => $validated['document_id'],
            'page_number' => $validated['page_number'],
            'type' => $validated['type'],
            'position' => $validated['position'],
            'content' => $validated['content'],
            'status' => 'open',
        ]);

        // Muat relasi user agar bisa ditampilkan di frontend
        $comment->load('user');

        return response()->json($comment, 201);
    }

    public function updateStatus(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,done',
        ]);

        // Mungkin tambahkan policy/gate di sini untuk otorisasi
        // Gate::authorize('update', $comment);

        $comment->status = $validated['status'];
        $comment->save();

        return response()->json(['message' => 'Status berhasil diperbarui.']);
    }
}