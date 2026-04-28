<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Tambah materi ke modul. Digunakan web & API.
     * POST /teacher/modules/{moduleId}/materials
     * POST /api/modules/{moduleId}/materials
     */
    public function store(Request $request, int $moduleId)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'type'      => 'required|in:text,video,pdf',
            'content'   => 'nullable|string',
            'file_path' => 'nullable|string|max:500',
        ]);

        $validated['module_id'] = $moduleId;
        $material = Material::create($validated);

        if ($request->expectsJson()) {
            return response()->json($material, 201);
        }

        return redirect()->back()->with('success', 'Materi berhasil ditambahkan!');
    }
}
