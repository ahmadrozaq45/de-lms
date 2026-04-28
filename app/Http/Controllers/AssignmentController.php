<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    /**
     * Guru membuat assignment baru.
     * POST /api/assignments
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id'    => 'required|integer|exists:courses,id',
            'title'        => 'required|string|max:255',
            'instructions' => 'required|string',
            'due_date'     => 'required|date',
        ]);

        return response()->json(Assignment::create($validated), 201);
    }

    /**
     * Siswa mengumpulkan jawaban assignment.
     * POST /api/assignments/{assignmentId}/submit
     */
    public function submit(Request $request, int $assignmentId)
    {
        $validated = $request->validate([
            'file_path' => 'required|string|max:500',
        ]);

        $submission = Submission::create([
            'assignment_id' => $assignmentId,
            'student_id'    => Auth::id(),
            'file_path'     => $validated['file_path'],
        ]);

        return response()->json($submission, 201);
    }
}
