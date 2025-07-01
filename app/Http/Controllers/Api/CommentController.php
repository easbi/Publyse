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
        // Validasi data dasar yang selalu ada
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Siapkan data dasar untuk disimpan
        $dataToCreate = [
            'user_id' => auth()->id(),
            'status' => 'open',
            'content' => $validated['content'],
            'document_id' => $validated['document_id'],
        ];

        // Cek apakah ini sebuah balasan (reply)
        if (!empty($validated['parent_id'])) {
            $parentComment = Comment::find($validated['parent_id']);

            // Jika komentar induk tidak ditemukan, hentikan proses dengan aman
            if (!$parentComment) {
                return response()->json(['message' => 'Komentar induk tidak ditemukan.'], 404);
            }

            // Warisi atribut dari komentar induk ke balasan yang baru
            $dataToCreate['parent_id'] = $parentComment->id;
            $dataToCreate['page_number'] = $parentComment->page_number;
            $dataToCreate['type'] = $parentComment->type;
            $dataToCreate['position'] = $parentComment->position;

        } else {
            // Jika ini komentar utama, validasi atribut anotasi yang wajib ada
            $annotationData = $request->validate([
                'page_number' => 'required|integer',
                'type' => 'required|in:point,area',
                'position' => 'required|json',
            ]);
            // Gabungkan data anotasi ke data yang akan disimpan
            $dataToCreate = array_merge($dataToCreate, $annotationData);
        }

        // Buat komentar baru dengan data yang sudah lengkap
        $comment = Comment::create($dataToCreate);

        // Muat relasi user untuk dikirim kembali ke frontend
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

    /**
     * Mengubah status sebuah komentar (misal: dari 'open' ke 'done').
     */
    public function updateStatus(Request $request, Comment $comment)
    {
        // Otorisasi: Pastikan user yang login boleh melihat publikasi ini
        // Ini mencegah user sembarangan mengubah status komentar di publikasi lain
        Gate::authorize('view-publication', $comment->document->publication);

        // Validasi input
        $validated = $request->validate([
            'status' => 'required|in:open,done',
        ]);

        // Update status dan simpan
        $comment->status = $validated['status'];
        $comment->save();

        // Kirim respons sukses
        return response()->json(['message' => 'Status berhasil diperbarui.']);
    }
}
