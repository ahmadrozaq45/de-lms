<?php

namespace App\Http\Controllers;

use App\Models\{Course, CourseEnrollment};
use App\Services\BadgeService;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function __construct(private BadgeService $badgeService) {}

    /**
     * Daftar kursus.
     * - API: semua kursus (bisa difilter by teacher)
     * - Web teacher: kursus milik guru yang login
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $query = Course::with('teacher:id,name')->withCount('enrollments');

            if ($request->has('teacher_id')) {
                $query->where('teacher_id', $request->teacher_id);
            }

            return response()->json($query->latest()->get());
        }

        // Web (guru)
        $courses = Course::with('modules')->where('teacher_id', Auth::id())->latest()->get();
        return view('teacher.courses.index', compact('courses'));
    }

    /**
     * Form pembuatan kursus baru (web saja).
     */
    public function create()
    {
        return view('teacher.courses.create');
    }

    /**
     * Simpan kursus baru.
     * POST /teacher/courses | POST /api/courses
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['teacher_id'] = Auth::id();
        $course = Course::create($validated);

        if ($request->expectsJson()) {
            return response()->json($course->load('teacher:id,name'), 201);
        }

        return redirect()->route('teacher.courses.show', $course->id)
                         ->with('success', 'Kursus berhasil dibuat!');
    }

    /**
     * Detail kursus.
     * GET /api/courses/{id}  |  GET /teacher/courses/{id}  |  GET /student/courses/{id}
     */
    public function show(Request $request, int $id)
    {
        $course = Course::with(['modules.materials', 'teacher:id,name'])->findOrFail($id);

        if ($request->expectsJson()) {
            $course->loadCount('enrollments');
            return response()->json($course);
        }

        $user = Auth::user();

        if ($user->role === 'teacher') {
            // Pastikan kursus milik guru ini
            abort_if($course->teacher_id !== $user->id, 403);
            return view('teacher.courses.show', compact('course'));
        }

        // Student
        $isEnrolled = CourseEnrollment::where('user_id', $user->id)
            ->where('course_id', $id)
            ->exists();

        return view('student.course-detail', compact('course', 'isEnrolled'));
    }

    /**
     * Form edit kursus (web, guru).
     */
    public function edit(int $id)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($id);
        return view('teacher.courses.edit', compact('course'));
    }

    /**
     * Update kursus.
     */
    public function update(Request $request, int $id)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($id);

        $course->update($request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]));

        if ($request->expectsJson()) {
            return response()->json($course);
        }

        return redirect()->route('teacher.courses.index')
                         ->with('success', 'Kursus berhasil diperbarui!');
    }

    /**
     * Hapus kursus.
     */
    public function destroy(Request $request, int $id)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($id);
        $course->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Kursus berhasil dihapus.']);
        }

        return redirect()->route('teacher.courses.index')
                         ->with('success', 'Kursus berhasil dihapus!');
    }
}
