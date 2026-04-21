<?php

namespace App\Http\Controllers;

use App\Models\{Course, Module, Material};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseContentController extends Controller
{
    // Course
    public function storeCourse(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $validated['teacher_id'] = Auth::id();
        return response()->json(Course::create($validated), 201);
    }

    // Module
    public function addModule(Request $request, $courseId) {
        $validated = $request->validate(['title' => 'required', 'order' => 'integer']);
        $validated['course_id'] = $courseId;
        return response()->json(Module::create($validated), 201);
    }

    // Material
    public function addMaterial(Request $request, $moduleId) {
        $validated = $request->validate([
            'title' => 'required',
            'type' => 'required|in:text,video,pdf',
            'content' => 'nullable',
            'file_path' => 'nullable'
        ]);
        $validated['module_id'] = $moduleId;
        return response()->json(Material::create($validated), 201);
    }
}
