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
    public function store(Request $request, $moduleId = null)
    {
        if ($moduleId) {
            $request->validate([
                'title'           => 'required|string|max:255',
                'description'     => 'nullable|string',
                'due_date'        => 'required|date',
                'max_score'       => 'required|integer|min:1|max:100',
                'submission_type' => 'required|in:text,file',
            ]);

            // Ambil data modul untuk mendapatkan course_id secara otomatis
            $module = Module::findOrFail($moduleId);

            Assignment::create([
                'module_id'       => $moduleId,
                'course_id'       => $module->course_id,
                'title'           => $request->title,
                'instructions'    => $request->description, // Memetakan 'description' form ke 'instructions' DB
                'due_date'        => $request->due_date,
                'max_score'       => $request->max_score,
                'submission_type' => $request->submission_type, // 👈 Tambahan simpan ke database
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
    public function submit(Request $request, int $assignmentId) // 👈 Hapus ': JsonResponse' di sini
    {
        // 1. Ambil assignment DULU untuk mengecek tipenya
        $assignment = Assignment::findOrFail($assignmentId);

        // 2. Siapkan aturan dasar
        $rules = [];

        // 3. Validasi dinamis sesuai tipe tugas
        if ($assignment->submission_type == 'text') {
            $rules['answer'] = 'required|string';
        } elseif ($assignment->submission_type == 'file') {
            $rules['file_path'] = 'required|file|max:10000';
        }
        // Jalankan validasi
        $request->validate($rules);

        $filePath = null;

        // Proses unggah file fisik jika ada
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