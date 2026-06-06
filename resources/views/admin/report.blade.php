<x-app-layout>
<style>
.stat { background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px 22px; }
.stat-num { font-size:30px; font-weight:800; color:#1e293b; }
.stat-label { font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:3px; }
.section-title { font-size:15px; font-weight:700; color:#1e293b; margin:0 0 14px; }
</style>
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div style="margin-bottom:28px;">
        <h1 style="font-size:22px; font-weight:800; color:#1e293b; margin:0 0 2px;">Laporan Platform</h1>
        <p style="font-size:13px; color:#64748b; margin:0;">Ringkasan data seluruh platform · {{ now()->format('d M Y') }}</p>
    </div>

    {{-- Platform stats --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:14px; margin-bottom:28px;">
        <div class="stat"><div class="stat-num">{{ $totalUsers }}</div><div class="stat-label">Total User</div></div>
        <div class="stat"><div class="stat-num" style="color:#3b5bdb;">{{ $totalTeachers }}</div><div class="stat-label">Guru</div></div>
        <div class="stat"><div class="stat-num" style="color:#16a34a;">{{ $totalStudents }}</div><div class="stat-label">Siswa</div></div>
        <div class="stat"><div class="stat-num" style="color:#d97706;">{{ $totalCourses }}</div><div class="stat-label">Kursus</div></div>
        <div class="stat"><div class="stat-num">{{ $totalEnrollments }}</div><div class="stat-label">Enrollment</div></div>
        <div class="stat"><div class="stat-num">{{ $totalSubmissions }}</div><div class="stat-label">Submission</div></div>
        <div class="stat"><div class="stat-num">{{ $totalQuizAttempts }}</div><div class="stat-label">Quiz Attempt</div></div>
        <div class="stat"><div class="stat-num" style="color:#6366f1;">{{ round($avgQuizScore) }}</div><div class="stat-label">Avg Skor Quiz</div></div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:28px;">
        {{-- Top Courses --}}
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px;">
            <h3 class="section-title">Top 5 Kursus (Siswa Terbanyak)</h3>
            @forelse($topCourses as $i => $course)
            <div style="display:flex; align-items:center; gap:12px; padding:10px 0; {{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }}">
                <div style="width:26px; height:26px; border-radius:50%; background:{{ ['#eff6ff','#f0fdf4','#fffbeb','#fdf4ff','#fff1f2'][$i] }}; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:{{ ['#3b5bdb','#16a34a','#d97706','#9333ea','#e11d48'][$i] }}; flex-shrink:0;">{{ $i+1 }}</div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:600; color:#1e293b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $course->title }}</div>
                    <div style="font-size:11px; color:#94a3b8;">{{ $course->teacher->name ?? '-' }}</div>
                </div>
                <div style="font-size:13px; font-weight:700; color:#3b5bdb; white-space:nowrap;">{{ $course->enrollments_count }} siswa</div>
            </div>
            @empty<p style="color:#94a3b8; font-size:13px;">Belum ada data.</p>@endforelse
        </div>

        {{-- Top Teachers --}}
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px;">
            <h3 class="section-title">Top 5 Guru (Kursus Terbanyak)</h3>
            @forelse($topTeachers as $i => $teacher)
            <div style="display:flex; align-items:center; gap:12px; padding:10px 0; {{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }}">
                <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#3b5bdb,#6366f1); display:flex; align-items:center; justify-content:center; color:white; font-size:12px; font-weight:700; flex-shrink:0;">{{ strtoupper(substr($teacher->name,0,1)) }}</div>
                <div style="flex:1;">
                    <div style="font-size:13px; font-weight:600; color:#1e293b;">{{ $teacher->name }}</div>
                    <div style="font-size:11px; color:#94a3b8;">{{ $teacher->email }}</div>
                </div>
                <div style="font-size:13px; font-weight:700; color:#3b5bdb;">{{ $teacher->teacher_courses_count }} kursus</div>
            </div>
            @empty<p style="color:#94a3b8; font-size:13px;">Belum ada data.</p>@endforelse
        </div>
    </div>

    {{-- Submission stats --}}
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:22px;">
        <h3 class="section-title">Status Submission</h3>
        <div style="display:flex; gap:24px; flex-wrap:wrap;">
            @foreach([['Pending',$submissionStats['pending'],'#fef3c7','#b45309'],['Graded',$submissionStats['graded'],'#dcfce7','#16a34a'],['Reviewed',$submissionStats['reviewed'],'#dbeafe','#1d4ed8']] as [$l,$v,$bg,$c])
            <div style="background:{{ $bg }}; border-radius:10px; padding:14px 22px; text-align:center;">
                <div style="font-size:26px; font-weight:800; color:{{ $c }};">{{ $v }}</div>
                <div style="font-size:12px; font-weight:600; color:{{ $c }};">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
</x-app-layout>
