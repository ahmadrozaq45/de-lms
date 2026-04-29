<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Menampilkan isi materi untuk siswa.
     * GET /student/materials/{id}
     */
    public function show(int $id)
    {
        // Ambil materi beserta relasi modul dan kelasnya
        $material = Material::with('module.course')->findOrFail($id);

        // Keamanan: Cek apakah siswa sudah enroll di kelas tempat materi ini berada
        $isEnrolled = CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $material->module->course_id)
            ->exists();

        // Jika belum daftar, lempar balik ke halaman kelas dengan pesan error
        if (!$isEnrolled) {
            return redirect()->route('student.courses.show', $material->module->course_id)
                             ->with('error', 'Wah, kamu harus daftar kelas ini dulu sebelum bisa baca materinya!');
        }

        // Tampilkan halaman baca materi
        return view('student.read-material', compact('material'));
    }
}
