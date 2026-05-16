<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Tambah modul ke kursus. Digunakan web & API.
     * POST /teacher/courses/{courseId}/modules
     * POST /api/courses/{courseId}/modules
     */
    public function store(Request $request, int $courseId)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['course_id'] = $courseId;
        $module = Module::create($validated);

        if ($request->expectsJson()) {
            return response()->json($module, 201);
        }

        return redirect()->back()->with('success', 'Modul berhasil ditambahkan!');
    }

    public function destroy(Request $request, int $id)
    {
        $module = Module::findOrFail($id);
        $module->delete();

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->back()->with('success', 'Modul berhasil dihapus!');
    }
}