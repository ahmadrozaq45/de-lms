<?php

namespace App\Http\Controllers;

use App\Models\{Course, Module, Material};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseContentController extends Controller
{
    // Menampilkan dashboard guru dengan daftar kursusnya
    public function index()
    {
        $courses = Course::where('teacher_id', Auth::id())->get();
        return view('teacher.dashboard', compact('courses'));
    }

    // Menyimpan Kursus (Hybrid)
    public function storeCourse(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $validated['teacher_id'] = Auth::id();
        $course = Course::create($validated);

        if ($request->expectsJson()) {
            return response()->json($course, 201);
        }

        return redirect()->back()->with('success', 'Kursus berhasil dibuat!');
    }

    // Menambah Modul (Hybrid)
    public function addModule(Request $request, $courseId) {
        $validated = $request->validate(['title' => 'required', 'order' => 'integer']);
        $validated['course_id'] = $courseId;
        
        $module = Module::create($validated);

        if ($request->expectsJson()) {
            return response()->json($module, 201);
        }

        return redirect()->back();
    }

    // Menambah Materi (Hybrid)
    public function addMaterial(Request $request, $moduleId) {
        $validated = $request->validate([
            'title' => 'required',
            'type' => 'required|in:text,video,pdf',
            'content' => 'nullable',
            'file_path' => 'nullable'
        ]);
        $validated['module_id'] = $moduleId;
        $material = Material::create($validated);

        if ($request->expectsJson()) {
            return response()->json($material, 201);
        }

        return redirect()->back();
    }
}
