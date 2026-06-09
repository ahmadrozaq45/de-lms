<?php

namespace App\Http\Controllers;

use App\Models\{Course, CourseEnrollment};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
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
        $course = Course::with(['modules.materials', 'modules.assignments', 'teacher:id,name', 'quizzes.questions', 'quizzes.attempts'])->findOrFail($id);

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

        return view('student.courses.show', compact('course', 'isEnrolled'));
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

    public function myCourses()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // 1. Ambil kursus yang sudah disetujui (Approved)
        $enrolledCourses = \App\Models\CourseEnrollment::with(['course.teacher', 'course.modules.materials'])
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->get();

        // 2. Ambil kursus yang masih menunggu (Pending)
        $pendingEnrollments = \App\Models\CourseEnrollment::with('course.teacher')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        // 3. Hitung progress persen untuk setiap kursus aktif
        $courseProgressData = [];
        foreach ($enrolledCourses as $enrollment) {
            $course = $enrollment->course;
            $materialIds = \App\Models\Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))->pluck('id');
            
            $total = $materialIds->count();
            $completed = \App\Models\MaterialProgress::where('user_id', $user->id)
                ->whereIn('material_id', $materialIds)
                ->where('is_completed', true)
                ->count();

            $courseProgressData[] = [
                'id' => $course->id,
                'percent' => $total > 0 ? round($completed / $total * 100) : 0,
            ];
        }

        // 4. Ambil data kursus yang BELUM diikuti (Untuk Eksplorasi)
        $enrolledAndPendingCourseIds = \App\Models\CourseEnrollment::where('user_id', $user->id)->pluck('course_id');
        $availableCourses = \App\Models\Course::whereNotIn('id', $enrolledAndPendingCourseIds)
            ->with(['teacher', 'modules'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 5. Arahkan ke file view my-courses
        return view('student.my-courses', compact('enrolledCourses', 'pendingEnrollments', 'courseProgressData', 'availableCourses'));
    }
}