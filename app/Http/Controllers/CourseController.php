<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Daftar kursus milik guru yang login.
     * GET /teacher/courses
     */
    public function index()
    {
        $courses = Course::with('modules')->where('teacher_id', Auth::id())->latest()->get();
        return view('teacher.courses.index', compact('courses'));
    }

    /**
     * Form pembuatan kursus baru.
     * GET /teacher/courses/create
     */
    public function create()
    {
        return view('teacher.courses.create');
    }

    /**
     * Simpan kursus baru. Digunakan web & API.
     * POST /teacher/courses  |  POST /api/courses
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
            return response()->json($course, 201);
        }

        return redirect()->route('teacher.courses.show', $course->id)
                         ->with('success', 'Kursus berhasil dibuat!');
    }

    /**
     * Detail kursus.
     * - Guru: tampilkan modul & materi miliknya (GET /teacher/courses/{id})
     * - Siswa: tampilkan modul, materi, dan status enroll (GET /student/courses/{id})
     */
    public function show(int $id)
    {
        $user = Auth::user();

        if ($user->role === 'teacher') {
            $course = Course::with(['modules.materials'])
                ->where('teacher_id', $user->id)
                ->findOrFail($id);

            return view('teacher.courses.show', compact('course'));
        }

        // Student
        $course     = Course::with('modules.materials')->findOrFail($id);
        $isEnrolled = CourseEnrollment::where('user_id', $user->id)
                            ->where('course_id', $id)
                            ->exists();

        return view('student.course-detail', compact('course', 'isEnrolled'));
    }

    /**
     * Form edit kursus (guru).
     * GET /teacher/courses/{id}/edit
     */
    public function edit(int $id)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($id);
        return view('teacher.courses.edit', compact('course'));
    }

    /**
     * Update kursus (guru).
     * PUT/PATCH /teacher/courses/{id}
     */
    public function update(Request $request, int $id)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($id);
        $course->update($request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ]));

        return redirect()->route('teacher.courses.index')
                         ->with('success', 'Kursus berhasil diperbarui!');
    }

    /**
     * Hapus kursus (guru).
     * DELETE /teacher/courses/{id}
     */
    public function destroy(int $id)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($id);
        $course->delete();

        return redirect()->route('teacher.courses.index')
                         ->with('success', 'Kursus berhasil dihapus!');
    }
}
