<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:text,file',
            'description' => 'nullable|string|max:500',
            'content'     => 'nullable|string',
            'file_path'   => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mkv|max:51200',
        ]);

        if ($request->hasFile('file_path')) {
            // Simpan file ke storage/app/public/materials
            // Pastikan sudah jalankan: php artisan storage:link
            $path = $request->file('file_path')->store('materials', 'public');
            $validated['file_path'] = $path;
        }

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

        // Keamanan: cek apakah siswa sudah enroll di kelas tempat materi ini berada
        $isEnrolled = CourseEnrollment::where('user_id', Auth::id())
            ->where('course_id', $material->module->course_id)
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.courses.show', $material->module->course_id)
                             ->with('error', 'Kamu harus daftar kelas ini dulu sebelum bisa membaca materinya!');
        }

        return view('student.materials.read', compact('material'));
    }

    /**
     * Update materi.
     * PUT /teacher/materials/{id}
     */
    public function update(Request $request, int $id)
    {
        $material = Material::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:text,file',
            'description' => 'nullable|string|max:500',
            'content'     => 'nullable|string',
            'file_path'   => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,mp4,mkv|max:51200',
        ]);

        if ($request->hasFile('file_path')) {
            // Hapus file lama dulu jika ada
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            // Simpan file baru
            $path = $request->file('file_path')->store('materials', 'public');
            $validated['file_path'] = $path;
        } else {
            // Jangan overwrite file_path lama kalau tidak ada file baru yang dikirim
            unset($validated['file_path']);
        }

        $material->update($validated);

        if ($request->expectsJson()) {
            return response()->json($material);
        }

        return redirect()->back()->with('success', 'Materi berhasil diperbarui!');
    }

    /**
     * Hapus materi beserta file fisiknya.
     * DELETE /teacher/materials/{id}
     */
    public function destroy(Request $request, int $id)
    {
        $material = Material::findOrFail($id);

        // Hapus file fisik dari storage jika ada
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->back()->with('success', 'Materi berhasil dihapus!');
    }

    /**
     * Download file materi.
     * GET /teacher/materials/{id}/download  (guru)
     * GET /student/materials/{id}/download  (siswa)
     */
    public function download(int $id)
    {
        $material = Material::with('module.course')->findOrFail($id);

        // Kalau diakses siswa, pastikan sudah enroll
        if (Auth::user()->role === 'student') {
            $isEnrolled = CourseEnrollment::where('user_id', Auth::id())
                ->where('course_id', $material->module->course_id)
                ->exists();

            if (!$isEnrolled) {
                return redirect()->back()->with('error', 'Kamu belum terdaftar di kelas ini.');
            }
        }

        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan atau sudah dihapus.');
        }

        // Download dengan nama file asli (bukan hash)
        $filename = $material->title . '.' . pathinfo($material->file_path, PATHINFO_EXTENSION);

        return Storage::disk('public')->download($material->file_path, $filename);
    }
}