<x-app-layout>
<style>
    .form-label  { display:block; font-size:14px; font-weight:700; color:#374151; margin-bottom:8px; }
    .form-input  { width:100%; border:1px solid #e2e8f0; border-radius:12px; padding:12px 16px; font-size:15px; color:#1e293b; outline:none; transition:border-color .2s; box-sizing:border-box; }
    .form-input:focus { border-color:#3b5bdb; box-shadow:0 0 0 3px rgba(59,91,219,.1); }
    .form-hint   { font-size:12px; color:#94a3b8; margin-top:6px; }
    .btn-primary { display:inline-flex; align-items:center; gap:8px; background:#3b5bdb; color:white; font-size:15px; font-weight:700; padding:12px 28px; border-radius:12px; border:none; cursor:pointer; transition:background .2s; }
    .btn-primary:hover { background:#2d45ba; }
    .btn-secondary { display:inline-flex; align-items:center; gap:8px; background:#f1f5f9; color:#475569; font-size:15px; font-weight:600; padding:12px 28px; border-radius:12px; border:none; cursor:pointer; text-decoration:none; }
    .btn-secondary:hover { background:#e2e8f0; }
</style>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <a href="{{ route('teacher.courses.show', $course->id) }}"
       class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke {{ $course->title }}
    </a>

    <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.03);">
        <div style="background:linear-gradient(135deg,#3b5bdb,#6366f1); padding:28px 32px;">
            <h1 style="font-size:24px; font-weight:800; color:white; margin:0 0 4px 0;">Buat Quiz Baru</h1>
            <p style="font-size:14px; color:rgba(255,255,255,.8); margin:0;">Untuk kursus: <strong>{{ $course->title }}</strong></p>
        </div>

        <div style="padding:32px;">
            @if($errors->any())
                <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:16px; margin-bottom:24px;">
                    <ul style="list-style:disc; padding-left:20px; margin:0; font-size:14px; color:#dc2626;">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('teacher.courses.quizzes.store', $course->id) }}" method="POST">
                @csrf

                <div style="display:flex; flex-direction:column; gap:24px;">

                    <div>
                        <label class="form-label">Judul Quiz <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="title" class="form-input" value="{{ old('title') }}"
                               placeholder="Contoh: Quiz Bab 1 — Dasar Pemrograman" required>
                    </div>

                    <div>
                        <label class="form-label">Deskripsi <span style="font-weight:400; color:#94a3b8;">(opsional)</span></label>
                        <textarea name="description" class="form-input" rows="3"
                                  placeholder="Keterangan singkat tentang quiz ini...">{{ old('description') }}</textarea>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                        <div>
                            <label class="form-label">Batas Waktu (menit) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="time_limit" class="form-input"
                                   value="{{ old('time_limit', 30) }}" min="1" max="300" required>
                            <p class="form-hint">Siswa harus selesai dalam waktu ini.</p>
                        </div>
                        <div>
                            <label class="form-label">Nilai Lulus (%) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="passing_score" class="form-input"
                                   value="{{ old('passing_score', 60) }}" min="0" max="100" required>
                            <p class="form-hint">Minimum skor untuk dinyatakan lulus.</p>
                        </div>
                    </div>

                    <div style="background:#f0f4ff; border-radius:12px; padding:20px; border:1px solid #c7d2fe;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                            <svg width="18" height="18" fill="none" stroke="#3b5bdb" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span style="font-size:14px; font-weight:700; color:#1e40af;">Info</span>
                        </div>
                        <p style="font-size:13px; color:#3730a3; margin:0; line-height:1.6;">
                            Setelah quiz dibuat, kamu akan diarahkan ke halaman tambah soal. Soal bisa ditambah kapan saja.
                        </p>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:12px; padding-top:8px; border-top:1px solid #f1f5f9;">
                        <a href="{{ route('teacher.courses.show', $course->id) }}" class="btn-secondary">Batal</a>
                        <button type="submit" class="btn-primary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Buat Quiz
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>