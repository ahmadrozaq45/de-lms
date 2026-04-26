<?php

namespace App\Http\Controllers;

use App\Models\{Course, Module, Material, Submission};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseContentController extends Controller
{
    // Dashboard guru — statistik + pending review + daftar kursus
    public function dashboard()
    {
        $courses = Course::with('modules')->where('teacher_id', Auth::id())->get();

        // Hitung total siswa unik dari semua enrollment di kursus guru ini
        $courseIds      = $courses->pluck('id');
        $totalStudents  = \App\Models\CourseEnrollment::whereIn('course_id', $courseIds)
                            ->distinct('user_id')->count('user_id');
        $totalAssignments = \App\Models\Assignment::whereIn('course_id', $courseIds)->count();

        // Submission yang belum di-review (status pending)
        $pendingSubmissions = Submission::whereHas('assignment.course', function ($q) use ($courseIds) {
            $q->whereIn('id', $courseIds);
        })->where('status', 'pending')->with('student')->latest()->get();

        $pendingReviews = $pendingSubmissions->count();

        return view('teacher.dashboard', compact(
            'courses',
            'totalStudents',
            'totalAssignments',
            'pendingSubmissions',
            'pendingReviews'
        ));
    }

    public function index()
    {
        $courses = Course::with('modules')->where('teacher_id', Auth::id())->latest()->get();
        return view('teacher.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('teacher.courses.create');
    }

    // Menyimpan Kursus (Hybrid)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $validated['teacher_id'] = Auth::id();
        $course = Course::create($validated);

        if ($request->expectsJson()) {
            return response()->json($course, 201);
        }

        return redirect()->route('teacher.courses.show', $course->id)->with('success', 'Kursus berhasil dibuat!');
    }

    /**
     * Alias untuk API route: POST /api/courses
     * Route API memanggil 'storeCourse', web memanggil 'store'.
     */
    public function storeCourse(Request $request)
    {
        return $this->store($request);
    }
    
    // Daftar kursus guru
    public function manageCourses()
    {
        $courses = Course::with('modules')->where('teacher_id', Auth::id())->latest()->get();
        return view('teacher.courses.index', compact('courses'));
    }

    // Detail kursus — tambah/kelola modul & materi
    public function show($id)
    {
        $course = Course::with(['modules.materials'])
            ->where('teacher_id', Auth::id())
            ->findOrFail($id);

        return view('teacher.courses.show', compact('course'));
    }

    // Menambah Modul (Hybrid)
    public function addModule(Request $request, $courseId)
    {
        $validated = $request->validate(['title' => 'required', 'order' => 'integer']);
        $validated['course_id'] = $courseId;
        $module = Module::create($validated);

        if ($request->expectsJson()) {
            return response()->json($module, 201);
        }

        return redirect()->back()->with('success', 'Modul berhasil ditambahkan!');
    }

    // Menambah Materi (Hybrid)
    public function addMaterial(Request $request, $moduleId)
    {
        $validated = $request->validate([
            'title'     => 'required',
            'type'      => 'required|in:text,video,pdf',
            'content'   => 'nullable',
            'file_path' => 'nullable'
        ]);
        $validated['module_id'] = $moduleId;
        $material = Material::create($validated);

        if ($request->expectsJson()) {
            return response()->json($material, 201);
        }

        return redirect()->back()->with('success', 'Materi berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($id);
        return view('teacher.courses.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($id);
        $course->update($request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string'
        ]));
        return redirect()->route('teacher.courses.index')->with('success', 'Kursus berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $course = Course::where('teacher_id', Auth::id())->findOrFail($id);
        $course->delete();
        return redirect()->route('teacher.courses.index')->with('success', 'Kursus berhasil dihapus!');
    }
}