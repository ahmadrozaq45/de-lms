<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Menampilkan halaman daftar submission milik kursus guru yang login.
     * Jika ada query ?submission=id, tampilkan detail submission tersebut.
     *
     * GET /teacher/reviews
     */
    public function index(Request $request)
    {
        // Assignment langsung punya course_id (bukan via module),
        // chain yang benar: assignment.course
        $submissions = Submission::whereHas('assignment.course', function ($q) {
            $q->where('teacher_id', Auth::id());
        })->with('student')->latest()->get();

        $selected = null;
        if ($request->filled('submission')) {
            $selected = Submission::with('student')
                ->whereHas('assignment.course', function ($q) {
                    $q->where('teacher_id', Auth::id());
                })
                ->findOrFail($request->submission);
        }

        return view('teacher.reviews.index', compact('submissions', 'selected'));
    }

    /**
     * Guru mengupdate score & feedback submission siswa.
     * PATCH /teacher/reviews/{id}
     *
     * FIX: chain relationship diperbaiki dari assignment.module.course
     *      menjadi assignment.course (Assignment tidak punya relasi ke Module).
     */
    public function update(Request $request, int $id)
    {
        $submission = Submission::whereHas('assignment.course', function ($q) {
            $q->where('teacher_id', Auth::id());
        })->findOrFail($id);

        $validated = $request->validate([
            'score'    => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
            'action'   => 'required|in:approve,send',
        ]);

        $submission->score            = $validated['score'];
        $submission->teacher_feedback = $validated['feedback'];
        $submission->status           = $validated['action'] === 'approve' ? 'graded' : 'reviewed';
        $submission->save();

        return redirect()->back()->with('success', 'Review berhasil disimpan!');
    }
}
