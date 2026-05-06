<x-app-layout>
<style>
    .stat-card { background:white; border-radius:16px; padding:24px; border:1px solid #f1f5f9; shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    .stat-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:16px; }
    
    .course-card { background:white; border-radius:20px; overflow:hidden; border:1px solid #f1f5f9; transition: all 0.3s ease; display: block; text-decoration: none; }
    .course-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border-color: #e2e8f0; }
    
    .progress-bar-bg { height:8px; background:#f1f5f9; border-radius:10px; overflow:hidden; margin-top:8px; }
    .progress-bar-fill { height:100%; background: #3b5bdb; border-radius:10px; transition: width 1s ease-in-out; }
    
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-10">
        <h1 style="font-size:32px; font-weight:800; color:#1e293b; margin:0 0 6px 0;">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p style="color:#64748b; font-size:16px; margin:0;">Yuk lanjutkan pembelajaran Anda hari ini</p>
    </div>

    <!-- Stats Grid -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:24px; margin-bottom:40px;">
        <!-- Course Aktif -->
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div style="font-size:32px; font-weight:800; color:#1e293b; line-height:1;">{{ $enrolledCourses->count() }}</div>
            <div style="font-size:14px; font-weight:600; color:#94a3b8; margin-top:8px; text-transform: uppercase; tracking-wider">Course Aktif</div>
        </div>

        <!-- Rata-rata Nilai -->
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffbeb;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
            </div>
            <div style="font-size:32px; font-weight:800; color:#1e293b; line-height:1;">{{ round($avgGrade ?? 0) }}%</div>
            <div style="font-size:14px; font-weight:600; color:#94a3b8; margin-top:8px; text-transform: uppercase;">Rata-rata Nilai</div>
        </div>

        <!-- Waktu Belajar -->
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div style="font-size:32px; font-weight:800; color:#1e293b; line-height:1;">{{ $totalStudyTime ?? '24h' }}</div>
            <div style="font-size:14px; font-weight:600; color:#94a3b8; margin-top:8px; text-transform: uppercase;">Waktu Belajar</div>
        </div>

        <!-- Progress Total -->
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdf4ff;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
            <div style="font-size:32px; font-weight:800; color:#1e293b; line-height:1;">{{ $overallProgress ?? 0 }}%</div>
            <div style="font-size:14px; font-weight:600; color:#94a3b8; margin-top:8px; text-transform: uppercase;">Progress Total</div>
        </div>
    </div>

    <!-- My Courses Section -->
    <div class="flex justify-between items-center mb-6">
        <h2 style="font-size:24px; font-weight:800; color:#1e293b; margin:0;">Course Saya</h2>
        <a href="#" style="color:#3b5bdb; font-weight:600; font-size:14px; text-decoration:none;">Lihat Semua</a>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:32px;">
        @forelse ($enrolledCourses as $enrollment)
            @php $course = $enrollment->course; @endphp
            <a href="{{ route('student.courses.show', $course->id) }}" class="course-card group">
                <!-- Course Thumbnail -->
                <div style="height:200px; position:relative; overflow:hidden;">
                    <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600" 
                         style="width:100%; height:100%; object-fit:cover;" 
                         alt="{{ $course->title }}"
                         class="group-hover:scale-110 transition-transform duration-500">
                    <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.4), transparent);"></div>
                </div>

                <!-- Course Info -->
                <div style="padding:24px;">
                    <h3 style="font-size:18px; font-weight:700; color:#1e293b; margin:0 0 8px 0;">{{ $course->title }}</h3>
                    <p class="line-clamp-2" style="font-size:14px; color:#64748b; margin:0 0 20px 0; line-height:1.6;">
                        {{ $course->description }}
                    </p>

                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:13px; color:#94a3b8; margin-bottom:16px; font-weight:500;">
                        <span style="display:flex; align-items:center; gap:6px;">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            {{ $course->teacher->name ?? 'Instruktur' }}
                        </span>
                        <span>{{ $course->modules->count() }} modul</span>
                    </div>

                    <!-- Progress Section -->
                    <div style="border-top:1px solid #f1f5f9; padding-top:16px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                            <span style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Progress</span>
                            <span style="font-size:12px; font-weight:800; color:#3b5bdb;">{{ $course->user_progress ?? 65 }}%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $course->user_progress ?? 65 }}%;"></div>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div style="grid-column:1/-1; background:#fffbeb; border:1px solid #fef3c7; border-radius:16px; padding:48px; text-align:center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="1.5" style="margin:0 auto 16px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <h3 style="font-size:18px; font-weight:700; color:#92400e; margin:0 0 4px 0;">Belum Ada Kursus</h3>
                <p style="color:#b45309; font-size:14px; margin:0;">Anda belum terdaftar di kursus manapun. Yuk cari kursus menarik!</p>
            </div>
        @endforelse
    </div>
</div>
</x-app-layout>