<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollment;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademicController extends Controller
{
    public function enroll(Request $request) {
        return response()->json(CourseEnrollment::firstOrCreate(['user_id' => Auth::id(), 'course_id' => $request->course_id]));
    }

    public function storeAssignment(Request $request) {
        $validated = $request->validate(['course_id' => 'required', 'title' => 'required', 'instructions' => 'required', 'due_date' => 'required|date']);
        return response()->json(Assignment::create($validated), 201);
    }

    public function submitAssignment(Request $request, $assignmentId) {
        return response()->json(Submission::create(['assignment_id' => $assignmentId, 'student_id' => Auth::id(), 'file_path' => $request->file_path]));
    }

    public function giveGrade(Request $request) {
        // Polymorphic: gradeable_type bisa 'App\Models\Assignment' atau 'App\Models\QuizAttempt'
        return response()->json(Grade::create($request->all()), 201);
    }

    // ── Review Submissions (dipindah dari ReviewController) ──────────────────

    /**
     * Menampilkan halaman review submissions.
     * Jika ada query ?submission=id, tampilkan detail submission tersebut.
     */
    public function reviewIndex(Request $request)
    {
        $submissions = Submission::whereHas('assignment.module.course', function ($q) {
            $q->where('teacher_id', Auth::id());
        })->with('student')->latest()->get();

        $selected = null;
        if ($request->has('submission')) {
            $selected = Submission::with('student')
                ->whereHas('assignment.module.course', function ($q) {
                    $q->where('teacher_id', Auth::id());
                })
                ->findOrFail($request->submission);
        }

        return view('teacher.reviews.index', compact('submissions', 'selected'));
    }

    /**
     * Update score dan feedback dari guru (approve AI atau kirim custom review).
     */
    public function reviewUpdate(Request $request, $id)
    {
        $submission = Submission::whereHas('assignment.module.course', function ($q) {
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