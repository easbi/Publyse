<?php

// app/Http/Controllers/Api/CommentController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'content' => 'required|string',
            'page_number' => 'nullable|integer', // Nullable karena balasan tidak butuh halaman
            'type' => 'nullable|in:point,area',
            'position' => 'nullable|json',
            'parent_id' => 'nullable|exists:comments,id', // Validasi untuk balasan
        ]);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'status' => 'open',
            // Gabungkan semua data tervalidasi
            ...$validated 
        ]);

        $comment->load('user');
        return response()->json($comment, 201);
    }

    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('update-comment', $comment);

        $validated = $request->validate(['content' => 'required|string']);
        $comment->update($validated);

        $comment->load('user');
        return response()->json($comment);
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete-comment', $comment);

        $comment->delete();
        return response()->json(null, 204); // 204 No Content
    }
}