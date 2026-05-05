<?php

namespace App\Http\Controllers;

use App\Models\{Assignment, Submission};
use App\Services\BadgeService;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function __construct(private BadgeService $badgeService) {}

    /**
     * Guru membuat assignment baru.
     * POST /api/assignments
     */
    public function store(Request $request): JsonResponse
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
    public function submit(Request $request, int $assignmentId): JsonResponse
    {
        $request->validate([
            'file_path' => 'nullable|string|max:500',
            'answer'    => 'nullable|string',
        ]);

        $this->validate($request, [
            'file_path|answer' => 'required_without:answer',
        ]);

        $assignment = Assignment::findOrFail($assignmentId);

        $submission = Submission::create([
            'assignment_id' => $assignmentId,
            'student_id'    => Auth::id(),
            'course_id'     => $assignment->course_id,
            'file_path'     => $request->file_path,
            'answer'        => $request->answer,
            'status'        => 'pending',
        ]);

        // Award badge submission pertama
        $this->badgeService->checkAfterSubmission(Auth::user());

        return response()->json($submission, 201);
    }
}
