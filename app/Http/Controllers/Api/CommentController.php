<?php

// app/Http/Controllers/Api/CommentController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Document;
use Carbon\Carbon;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data dasar yang selalu ada
        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
            'created_at_scale' => 'nullable|numeric|between:0.1,10.0',
            'page_dimensions' => 'nullable|array',
            'original_position' => 'nullable|string',
        ]);

        // --- LOGIKA PEMERIKSAAN BATAS WAKTU ---
        $document = Document::find($validated['document_id']);

        // Set batas waktu ke akhir hari (23:59:59) sebelum membandingkan
        $deadline = Carbon::parse($document->publication->review_deadline)->endOfDay();

        // Cek jika waktu saat ini sudah melewati akhir hari dari tanggal deadline
        if (Carbon::now()->isAfter($deadline)) {
            // Jika sudah lewat, kirim respons error 403 (Forbidden)
            return response()->json(['message' => 'Batas waktu pemeriksaan untuk publikasi ini telah berakhir.'], 403);
        }
        // --- AKHIR LOGIKA BATAS WAKTU ---

        // Siapkan data dasar untuk disimpan
        $dataToCreate = [
            'user_id' => auth()->id(),
            'status' => 'open',
            'content' => $validated['content'],
            'document_id' => $validated['document_id'],
            'created_at_scale' => $validated['created_at_scale'] ?? 1.0,
            'page_dimensions' => isset($validated['page_dimensions'])
                ? json_encode($validated['page_dimensions'])
                : null,
            'original_position' => $validated['original_position'] ?? null,
        ];

        // Cek apakah ini sebuah balasan (reply)
        if (!empty($validated['parent_id'])) {
            $parentComment = Comment::find($validated['parent_id']);
            if (!$parentComment) {
                 return response()->json(['message' => 'Komentar induk tidak ditemukan.'], 404);
            }
            // Warisi atribut dari komentar induk
            $dataToCreate['parent_id'] = $parentComment->id;
            $dataToCreate['page_number'] = $parentComment->page_number;
            $dataToCreate['type'] = $parentComment->type;
            $dataToCreate['position'] = $parentComment->position;
            if (!isset($validated['created_at_scale']) && $parentComment->created_at_scale) {
                $dataToCreate['created_at_scale'] = $parentComment->created_at_scale;
            }

        } else {
            // Jika ini komentar utama, validasi atribut anotasi
            $annotationData = $request->validate([
                'page_number' => 'required|integer',
                'type' => 'required|in:point,area',
                'position' => 'required|json',
            ]);
            // Gabungkan data anotasi
            $dataToCreate = array_merge($dataToCreate, $annotationData);
        }

        // Buat komentar baru
        $comment = Comment::create($dataToCreate);

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

        // Hanya yang membuat komen dan pemilik publikasi yang bisa mengubah status komentar
        Gate::authorize('resolve-comment', $comment);

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
