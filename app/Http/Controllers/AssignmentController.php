<?php

namespace App\Http\Controllers;

use App\Models\{Assignment, Submission, Module};
use App\Services\BadgeService;
use Illuminate\Http\Request; 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function __construct(private BadgeService $badgeService) {}

    /**
     * Tampilkan halaman detail tugas untuk siswa.
     * GET /student/assignments/{id}
     */
    public function show(int $id)
    {
        $assignment = Assignment::with(['module', 'course'])->findOrFail($id);

        $submission = Submission::where('assignment_id', $id)
            ->where('student_id', Auth::id())
            ->latest()
            ->first();

        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    /**
     * Guru membuat assignment baru.
     * POST /api/assignments
     */
    public function store(Request $request, $moduleId = null) // 🚀 2. TAMBAH PARAMETER $moduleId & HAPUS ': JsonResponse'
    {
        if ($moduleId) {
            $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date'    => 'required|date',
                'max_score'   => 'required|integer|min:1|max:100',
            ]);

            // Ambil data modul untuk mendapatkan course_id secara otomatis
            $module = Module::findOrFail($moduleId);

            Assignment::create([
                'module_id'    => $moduleId,
                'course_id'    => $module->course_id,
                'title'        => $request->title,
                'instructions' => $request->description, // Memetakan 'description' form ke 'instructions' DB
                'due_date'     => $request->due_date,
                'max_score'    => $request->max_score,
            ]);

            return redirect()->back()->with('success', 'Tugas baru berhasil ditambahkan!');
        }

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

        return redirect()->route('student.assignments.show', $assignmentId)->with('success', 'Tugas berhasil dikumpulkan!');
    }
}