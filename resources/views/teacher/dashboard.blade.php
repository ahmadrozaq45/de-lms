<x-app-layout>
<style>
.stat-card { background:white; border-radius:12px; padding:24px; border:1px solid #f0f0f0; }
.stat-icon { width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; margin-bottom:12px; }
.course-card { background:white; border-radius:12px; overflow:hidden; border:1px solid #f0f0f0; }
.review-item { background:white; border:1px solid #f0f0f0; border-radius:10px; padding:16px 20px; display:flex; align-items:center; justify-content:space-between; }
</style>

<div>
    <div style="margin-bottom:24px;">
        <h1 style="font-size:24px; font-weight:700; color:#111827; margin:0 0 4px 0;">Dashboard Guru</h1>
        <p style="font-size:14px; color:#6b7280; margin:0;">Kelola course dan review hasil siswa</p>
    </div>

   <!-- 
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px;">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div style="font-size:28px; font-weight:700; color:#111827; line-height:1;">{{ $courses->count() }}</div>
            <div style="font-size:13px; color:#6b7280; margin-top:4px;">Course Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div style="font-size:28px; font-weight:700; color:#111827; line-height:1;">{{ $totalStudents }}</div>
            <div style="font-size:13px; color:#6b7280; margin-top:4px;">Total Siswa</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdf4ff;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div style="font-size:28px; font-weight:700; color:#111827; line-height:1;">{{ $totalAssignments }}</div>
            <div style="font-size:13px; color:#6b7280; margin-top:4px;">Assignment</div>
        </div>
    </div> -->

    <!-- Pending Reviews -->
    @if($pendingSubmissions->isNotEmpty())
    <div style="background:white; border-radius:12px; border:1px solid #f0f0f0; padding:20px; margin-bottom:28px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:16px; font-weight:600; color:#111827; margin:0;">Perlu Review</h2>
            <a href="{{ route('teacher.reviews.index') }}" style="font-size:13px; color:#3b5bdb; text-decoration:none;">Lihat Semua →</a>
        </div>
        @foreach($pendingSubmissions->take(3) as $sub)
        <div class="review-item" style="margin-bottom:10px;">
            <div>
                <div style="font-size:14px; font-weight:600; color:#111827;">{{ $sub->student->name }}</div>
                <div style="font-size:12px; color:#6b7280; margin-top:2px;">Assignment ID: {{ $sub->assignment_id }}</div>
                <div style="font-size:12px; color:#9ca3af; margin-top:2px;">Submitted: {{ $sub->created_at->format('Y-m-d H:i') }}</div>
            </div>
            <a href="{{ route('teacher.reviews.index', ['submission' => $sub->id]) }}"
               style="display:inline-flex; align-items:center; gap:6px; background:#3b5bdb; color:white; font-size:13px; font-weight:500; padding:8px 16px; border-radius:8px; text-decoration:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                Review
            </a>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Courses -->
    <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0 0 16px 0;">Course Saya</h2>
    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:20px;">
        @forelse ($courses as $course)
        <div class="course-card">
            <div style="height:150px; background:linear-gradient(135deg,#1a2b4a,#2d4a8a); display:flex; align-items:center; justify-content:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><polyline points="8 21 12 17 16 21"/></svg>
            </div>
            <div style="padding:16px;">
                <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0 0 6px 0;">{{ $course->title }}</h3>
                <p style="font-size:13px; color:#6b7280; margin:0 0 12px 0;">{{ Str::limit($course->description, 80) }}</p>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:12px; color:#9ca3af;">{{ $course->modules->count() }} modul</span>
                    <a href="{{ route('teacher.courses.show', $course->id) }}" style="font-size:13px; color:#3b5bdb; text-decoration:none; font-weight:500;">Kelola →</a>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:span 2; text-align:center; padding:40px; color:#9ca3af;">
            Belum ada kursus. <a href="{{ route('teacher.courses.create') }}" style="color:#3b5bdb;">Buat sekarang</a>
        </div>
        @endforelse
    </div>
</div>
</x-app-layout>
