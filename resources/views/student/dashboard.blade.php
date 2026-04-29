<x-app-layout>
<style>
.stat-card { background:white; border-radius:12px; padding:24px; border:1px solid #f0f0f0; }
.stat-icon { width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; margin-bottom:12px; }
.course-card { background:white; border-radius:12px; overflow:hidden; border:1px solid #f0f0f0; transition:box-shadow 0.2s; }
.course-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
.progress-bar { height:6px; background:#e5e7eb; border-radius:3px; overflow:hidden; margin-top:6px; }
.progress-fill { height:100%; background:#3b5bdb; border-radius:3px; }
</style>

<div>
    <h1 style="font-size:24px; font-weight:700; color:#111827; margin:0 0 4px 0;">Selamat Datang, {{ auth()->user()->name }}!</h1>
    <p style="color:#6b7280; font-size:14px; margin:0 0 28px 0;">Yuk lanjutkan pembelajaran Anda hari ini</p>

    <!-- Stats Row -->
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:32px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div style="font-size:28px; font-weight:700; color:#111827; line-height:1;">{{ $totalCourses }}</div>
            <div style="font-size:13px; color:#6b7280; margin-top:4px;">Course Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffbeb;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>
            </div>
            <div style="font-size:28px; font-weight:700; color:#111827; line-height:1;">-</div>
            <div style="font-size:13px; color:#6b7280; margin-top:4px;">Rata-rata Nilai</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div style="font-size:28px; font-weight:700; color:#111827; line-height:1;">-</div>
            <div style="font-size:13px; color:#6b7280; margin-top:4px;">Waktu Belajar</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdf4ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <div style="font-size:28px; font-weight:700; color:#111827; line-height:1;">-</div>
            <div style="font-size:13px; color:#6b7280; margin-top:4px;">Progress Total</div>
        </div>
    </div>

    <!-- Courses -->
    <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0 0 16px 0;">Course Saya</h2>
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px;">
        @forelse ($courses as $course)
        <a href="{{ route('student.courses.show', $course->id) }}" style="text-decoration:none;" class="course-card">
            <div style="height:180px; background:linear-gradient(135deg,#1e3a5f,#3b5bdb); position:relative; overflow:hidden;">
                <div style="position:absolute; inset:0; background:linear-gradient(135deg,rgba(30,58,95,0.8),rgba(59,91,219,0.6)); display:flex; align-items:center; justify-content:center;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" stroke-width="1.5"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
            </div>
            <div style="padding:16px;">
                <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0 0 6px 0;">{{ $course->title }}</h3>
                <p style="font-size:13px; color:#6b7280; margin:0 0 12px 0; line-height:1.4;">{{ Str::limit($course->description, 70) }}</p>
                <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; color:#9ca3af; margin-bottom:6px;">
                    <span>{{ $course->teacher->name ?? 'Instruktur' }}</span>
                    <span>{{ $course->modules->count() ?? 0 }} modul</span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:12px; color:#6b7280;">Progress</span>
                    <span style="font-size:12px; font-weight:600; color:#3b5bdb;"></span>
                </div>
                <div class="progress-bar">
                   <!-- <div class="progress-fill" style="width:65%;"></div> -->
                </div>
            </div>
        </a>
        @empty
        <div style="grid-column:span 3; background:#fffbeb; border:1px solid #fcd34d; border-radius:12px; padding:24px; text-align:center; color:#92400e;">
            Belum ada kursus yang tersedia saat ini.
        </div>
        @endforelse
    </div>
</div>
</x-app-layout>
