<?php

namespace App\Http\Controllers;

use App\Models\{Assignment, Submission};
use App\Services\BadgeService;
use Illuminate\Http\Request; 
use Illumiate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            // Validasi: Jika answer kosong, maka file harus ada, dan sebaliknya[cite: 3]
            'file_path' => 'required_without:answer|nullable|file|max:10000',
            'answer'    => 'required_without:file_path|nullable|string', 
        ]);

        $assignment = Assignment::findOrFail($assignmentId);
        $filePath = null;

        // Proses unggah file fisik jika ada[cite: 1]
        if ($request->hasFile('file_path')) {
            $filePath = $request->file('file_path')->store('submissions', 'public');
        }   

        /* $this->validate($request, [
            'file_path|answer' => 'required_without:answer',
        ]); */

        $submission = Submission::create([
            'assignment_id' => $assignmentId,
            'student_id'    => Auth::id(),
            'course_id'     => $assignment->course_id,
            'file_path'     => $filePath,
            'answer'        => $request->answer,
            'status'        => 'pending',
        ]);

        // Award badge submission pertama
        $this->badgeService->checkAfterSubmission(Auth::user());

        if ($request->expectsJson()) {
            return response()->json($submission, 201);
        }

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}
