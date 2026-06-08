<x-app-layout>
<style>
/* ── Base ── */
.td-wrap   { max-width:1280px; margin:0 auto; padding:28px 24px; }
.td-card   { background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px 24px; }

/* ── Stat Cards ── */
.stat-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin-bottom:24px; }
.stat-card { background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px 22px;
             display:flex; align-items:center; gap:14px; }
.stat-icon { width:44px; height:44px; border-radius:11px; display:flex; align-items:center;
             justify-content:center; flex-shrink:0; }
.stat-val  { font-size:28px; font-weight:800; color:#1e293b; line-height:1; }
.stat-lbl  { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;
             letter-spacing:.5px; margin-top:3px; }

/* ── Layout grids ── */
.row2      { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
.row3      { display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:16px; }
.row-full  { margin-bottom:16px; }
@media(max-width:900px){ .row2,.row3{ grid-template-columns:1fr; } }

/* ── Mini Table ── */
.mini-table { width:100%; border-collapse:collapse; }
.mini-table th { padding:10px 14px; font-size:11px; font-weight:700; color:#94a3b8;
                 text-transform:uppercase; text-align:left; border-bottom:1px solid #f1f5f9; }
.mini-table td { padding:11px 14px; font-size:13px; color:#475569;
                 border-bottom:1px solid #f8fafc; vertical-align:middle; }
.mini-table tr:last-child td { border-bottom:none; }
.mini-table tr:hover td { background:#f8fafc; }

/* ── Avatar ── */
.avatar { width:32px; height:32px; border-radius:50%;
          background:linear-gradient(135deg,#3b5bdb,#6366f1);
          display:flex; align-items:center; justify-content:center;
          color:white; font-size:12px; font-weight:700; flex-shrink:0; }

/* ── Progress bar row ── */
.bar-row   { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.bar-label { font-size:12px; color:#64748b; width:130px; flex-shrink:0;
             white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.bar-track { flex:1; height:10px; background:#f1f5f9; border-radius:99px; overflow:hidden; }
.bar-fill  { height:100%; border-radius:99px; }
.bar-count { font-size:12px; font-weight:700; color:#475569; width:36px; text-align:right; }

/* ── Badge ── */
.badge-pass   { display:inline-block; padding:3px 8px; border-radius:99px; font-size:11px;
                font-weight:700; background:#dcfce7; color:#166534; }
.badge-fail   { display:inline-block; padding:3px 8px; border-radius:99px; font-size:11px;
                font-weight:700; background:#fee2e2; color:#991b1b; }
.badge-warn   { display:inline-block; padding:3px 8px; border-radius:99px; font-size:11px;
                font-weight:700; background:#fef3c7; color:#92400e; }

/* ── AI Insight card ── */
.ai-card     { background:linear-gradient(135deg,#eff6ff,#f5f3ff); border:1px solid #c7d2fe;
               border-radius:14px; padding:20px 24px; }
.ai-badge    { display:inline-flex; align-items:center; gap:5px; background:#3b5bdb;
               color:white; font-size:11px; font-weight:700; padding:4px 10px;
               border-radius:99px; margin-bottom:12px; }

/* ── Activity dots ── */
.dot-active  { width:8px; height:8px; border-radius:50%; background:#22c55e; flex-shrink:0; }
.dot-inactive{ width:8px; height:8px; border-radius:50%; background:#f59e0b; flex-shrink:0; }

/* ── Shared ── */
.sec-title { font-size:14px; font-weight:700; color:#1e293b; margin:0 0 16px; }
.sec-sub   { font-size:12px; color:#94a3b8; font-weight:500; }
</style>

<div class="td-wrap">

    {{-- ══════════════════════════════════════
         HEADER
    ══════════════════════════════════════ --}}
    <div style="display:flex; justify-content:space-between; align-items:center;
                flex-wrap:wrap; gap:10px; margin-bottom:22px;">
        <div>
            <h1 style="font-size:21px; font-weight:800; color:#1e293b; margin:0 0 2px;">
                Dashboard Guru
            </h1>
            <p style="font-size:13px; color:#94a3b8; margin:0;">
                Selamat datang, <strong style="color:#475569;">{{ Auth::user()->name }}</strong>
                — {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('teacher.report') }}"
               style="display:inline-flex; align-items:center; gap:6px; background:#3b5bdb;
                      color:white; padding:9px 18px; border-radius:10px; font-size:13px;
                      font-weight:600; text-decoration:none;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
                     viewBox="0 0 24 24">
                    <rect x="5" y="2" width="14" height="20" rx="2"/>
                    <path d="M9 7h6M9 11h6M9 15h4"/>
                </svg>
                Lihat Report
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         STAT CARDS  (4 kartu)
    ══════════════════════════════════════ --}}
    <div class="stat-grid">

        {{-- Jumlah Course --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;">
                <svg width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="2"
                     viewBox="0 0 24 24">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <path d="M8 21h8M12 17v4"/>
                </svg>
            </div>
            <div>
                <div class="stat-val">{{ $stats['total_courses'] }}</div>
                <div class="stat-lbl">Course Aktif</div>
            </div>
        </div>

        {{-- Jumlah Siswa --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7;">
                <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div>
                <div class="stat-val">{{ $stats['total_students'] }}</div>
                <div class="stat-lbl">Siswa Diajar</div>
            </div>
        </div>

        {{-- Nilai Rata-rata Kelas --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <path d="M9 12h6M9 16h4"/>
                </svg>
            </div>
            <div>
                <div class="stat-val">{{ number_format($stats['avg_class_score'], 1) }}</div>
                <div class="stat-lbl">Rata-rata Nilai</div>
            </div>
        </div>

        {{-- Pending Submissions --}}
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;">
                <svg width="20" height="20" fill="none" stroke="#dc2626" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path d="M15 17H20L18.59 15.59A2 2 0 0118 14V11A6 6 0 006 11V14A2 2 0 015.41 15.59L4 17H9M12 21A2 2 0 0014 19H10A2 2 0 0012 21z"/>
                </svg>
            </div>
            <div>
                <div class="stat-val">{{ $pendingSubmissions->count() }}</div>
                <div class="stat-lbl">Tugas Pending</div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════
         ROW 1: Progress Kelas (chart) + Pending Approvals
    ══════════════════════════════════════ --}}
    <div class="row3">

        {{-- Progress Kelas – Grafik --}}
        <div class="td-card">
            <p class="sec-title">
                Progress Kelas
                <span class="sec-sub">rata-rata penyelesaian materi per course</span>
            </p>
            <div style="position:relative; height:210px; width:100%;">
                <canvas id="classProgressChart"></canvas>
            </div>
        </div>

        {{-- Pending Approvals --}}
        <div class="td-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <p class="sec-title" style="margin:0;">
                    Permohonan Masuk
                    @if($pendingApprovals->count())
                    <span style="font-size:11px; font-weight:700; background:#fef3c7; color:#92400e;
                                 padding:2px 8px; border-radius:99px; margin-left:6px;">
                        {{ $pendingApprovals->count() }}
                    </span>
                    @endif
                </p>
                @if($pendingApprovals->count())
                <a href="{{ route('teacher.courses.index') }}"
                   style="font-size:12px; color:#3b5bdb; text-decoration:none; font-weight:600;">
                    Lihat semua →
                </a>
                @endif
            </div>
            @forelse($pendingApprovals->take(5) as $enrollment)
            <div style="display:flex; align-items:center; justify-content:space-between;
                        padding:9px 0; border-bottom:1px solid #f1f5f9;">
                <div style="display:flex; align-items:center; gap:9px;">
                    <div class="avatar" style="background:linear-gradient(135deg,#f59e0b,#f97316);">
                        {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:600; color:#1e293b; font-size:13px;">
                            {{ $enrollment->user->name }}
                        </div>
                        <div style="font-size:11px; color:#94a3b8;">
                            {{ Str::limit($enrollment->course->title, 28) }}
                        </div>
                    </div>
                </div>
                <form action="{{ route('teacher.enrollments.approve', $enrollment->id) }}"
                      method="POST" style="display:inline;">
                    @csrf @method('PATCH')
                    <button type="submit"
                            style="background:#3b5bdb; color:white; border:none; padding:5px 12px;
                                   border-radius:7px; font-size:11px; font-weight:700; cursor:pointer;">
                        Setujui
                    </button>
                </form>
            </div>
            @empty
            <p style="text-align:center; color:#94a3b8; padding:24px 0; margin:0; font-size:13px;">
                Tidak ada permohonan masuk.
            </p>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════
         ROW 2: Student Paling Aktif + Student Kurang Aktif
    ══════════════════════════════════════ --}}
    <div class="row2">

        {{-- Siswa Paling Aktif --}}
        <div class="td-card">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
                <div style="width:8px; height:8px; border-radius:50%; background:#22c55e;"></div>
                <p class="sec-title" style="margin:0;">
                    Siswa Paling Aktif
                    <span class="sec-sub"> — berdasarkan materi selesai</span>
                </p>
            </div>
            <table class="mini-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Siswa</th>
                        <th style="text-align:center;">Materi Selesai</th>
                        <th style="text-align:center;">Rata-rata Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mostActiveStudents as $i => $student)
                    <tr>
                        <td style="font-weight:700; color:#94a3b8; font-size:12px;">
                            {{ $i + 1 }}
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:9px;">
                                <div class="avatar">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:#1e293b; font-size:13px;">
                                        {{ $student->name }}
                                    </div>
                                    <div style="font-size:11px; color:#94a3b8;">
                                        {{ $student->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge-pass">{{ $student->completed_count }}</span>
                        </td>
                        <td style="text-align:center; font-weight:700; font-size:13px; color:#1e293b;">
                            {{ $student->avg_score !== null ? number_format($student->avg_score, 1) : '–' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:#94a3b8; padding:24px;">
                            Belum ada data aktivitas siswa.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Siswa Kurang Aktif --}}
        <div class="td-card">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
                <div style="width:8px; height:8px; border-radius:50%; background:#f59e0b;"></div>
                <p class="sec-title" style="margin:0;">
                    Siswa Kurang Aktif
                    <span class="sec-sub"> — perlu dorongan</span>
                </p>
            </div>
            <table class="mini-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th style="text-align:center;">Progress</th>
                        <th style="text-align:center;">Terakhir Aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leastActiveStudents as $student)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:9px;">
                                <div class="avatar"
                                     style="background:linear-gradient(135deg,#f43f5e,#ec4899);">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:#1e293b; font-size:13px;">
                                        {{ $student->name }}
                                    </div>
                                    <div style="font-size:11px; color:#94a3b8;">
                                        {{ Str::limit($student->email, 26) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            @php $pct = $student->progress_percent ?? 0; @endphp
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div style="flex:1; height:7px; background:#f1f5f9;
                                            border-radius:99px; overflow:hidden;">
                                    <div style="width:{{ $pct }}%; height:100%; border-radius:99px;
                                                background:{{ $pct < 30 ? '#f43f5e' : '#f59e0b' }};"></div>
                                </div>
                                <span style="font-size:11px; font-weight:700; color:#64748b; width:30px;">
                                    {{ $pct }}%
                                </span>
                            </div>
                        </td>
                        <td style="text-align:center; font-size:12px; color:#94a3b8;">
                            {{ $student->last_active ? \Carbon\Carbon::parse($student->last_active)->diffForHumans() : 'Belum pernah' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center; color:#94a3b8; padding:24px;">
                            Semua siswa aktif 🎉
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         ROW 3: AI Insights (2 kartu)
    ══════════════════════════════════════ --}}
    <div class="row2">

        {{-- Materi yang Perlu Diulang (AI Insight) --}}
        {{-- Karena tidak ada kolom analysis_type, gunakan status_prediction = 'needs_improvement' --}}
        <div class="ai-card">
            <div class="ai-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a10 10 0 110 20A10 10 0 0112 2zm0 2a8 8 0 100 16A8 8 0 0012 4zm-1 5h2v5h-2V9zm0 6h2v2h-2v-2z"/>
                </svg>
                AI Insight
            </div>
            <p class="sec-title" style="margin:0 0 14px;">
                Rekomendasi untuk Siswa
                <span class="sec-sub"> — perlu peningkatan</span>
            </p>
            @php
                $aiMaterialInsights = \App\Models\AiAnalysis::where('status_prediction', 'needs_improvement')
                    ->whereHas('course', fn($q) => $q->where('teacher_id', Auth::id()))
                    ->with(['course', 'user'])
                    ->latest()
                    ->limit(5)
                    ->get();
            @endphp
            @forelse($aiMaterialInsights as $insight)
            <div style="background:white; border:1px solid #c7d2fe; border-radius:10px;
                        padding:12px 14px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
                    <div style="min-width:0;">
                        <div style="font-weight:700; color:#1e293b; font-size:13px; margin-bottom:4px;">
                            {{ $insight->user->name ?? '–' }}
                        </div>
                        <div style="font-size:11px; color:#94a3b8;">
                            {{ Str::limit($insight->course->title ?? '–', 36) }}
                        </div>
                    </div>
                    <span class="badge-warn" style="flex-shrink:0;">Perlu Peningkatan</span>
                </div>
                @if($insight->recommendation)
                <p style="font-size:12px; color:#64748b; margin:8px 0 0; line-height:1.5;">
                    {{ Str::limit($insight->recommendation, 120) }}
                </p>
                @endif
            </div>
            @empty
            <div style="text-align:center; padding:28px 0; color:#94a3b8;">
                <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.5"
                     viewBox="0 0 24 24" style="margin:0 auto 8px; display:block;">
                    <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3M5.636 5.636l.707.707M12 21v-1"/>
                    <circle cx="12" cy="12" r="4"/>
                </svg>
                <p style="margin:0; font-size:13px;">Belum ada analisis AI.</p>
                <p style="margin:4px 0 0; font-size:11px;">
                    Jalankan analisis dari halaman Report untuk menghasilkan insight.
                </p>
            </div>
            @endforelse
        </div>

        {{-- Siswa yang Perlu Perhatian (AI Insight) --}}
        <div class="ai-card">
            <div class="ai-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a10 10 0 110 20A10 10 0 0112 2zm0 2a8 8 0 100 16A8 8 0 0012 4zm-1 5h2v5h-2V9zm0 6h2v2h-2v-2z"/>
                </svg>
                AI Insight
            </div>
            <p class="sec-title" style="margin:0 0 14px;">
                Siswa yang Perlu Perhatian
            </p>
            @php
                $atRiskStudents = \App\Models\AiAnalysis::where('status_prediction', 'at_risk')
                    ->whereHas('course', fn($q) => $q->where('teacher_id', Auth::id()))
                    ->with(['user', 'course'])
                    ->latest()
                    ->limit(5)
                    ->get();
            @endphp
            @forelse($atRiskStudents as $analysis)
            <div style="background:white; border:1px solid #c7d2fe; border-radius:10px;
                        padding:12px 14px; margin-bottom:10px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="avatar"
                         style="background:linear-gradient(135deg,#f43f5e,#ec4899); flex-shrink:0;">
                        {{ strtoupper(substr($analysis->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:700; color:#1e293b; font-size:13px;">
                            {{ $analysis->user->name ?? '–' }}
                        </div>
                        <div style="font-size:11px; color:#94a3b8;">
                            {{ Str::limit($analysis->course->title ?? '–', 32) }}
                        </div>
                    </div>
                    <span class="badge-fail">Berisiko</span>
                </div>
                @if($analysis->recommendation)
                <p style="font-size:12px; color:#64748b; margin:8px 0 0; line-height:1.5;">
                    {{ Str::limit($analysis->recommendation, 120) }}
                </p>
                @endif
            </div>
            @empty
            <div style="text-align:center; padding:28px 0; color:#94a3b8;">
                <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.5"
                     viewBox="0 0 24 24" style="margin:0 auto 8px; display:block;">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
                <p style="margin:0; font-size:13px;">Tidak ada siswa berisiko saat ini.</p>
                <p style="margin:4px 0 0; font-size:11px;">Semua siswa dalam kondisi baik.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════
         ROW 4: Pending Submissions Table
    ══════════════════════════════════════ --}}
    <div class="row-full">
        <div class="td-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <p class="sec-title" style="margin:0;">
                    Tugas Menunggu Penilaian
                    <span class="sec-sub"> — belum dinilai</span>
                </p>
                @if($pendingSubmissions->count())
                <span style="font-size:12px; font-weight:700; background:#fee2e2; color:#dc2626;
                             padding:4px 10px; border-radius:99px;">
                    {{ $pendingSubmissions->count() }} tugas
                </span>
                @endif
            </div>
            @if($pendingSubmissions->isEmpty())
            <p style="text-align:center; color:#94a3b8; padding:28px 0; margin:0; font-size:13px;">
                Tidak ada tugas yang menunggu penilaian.
            </p>
            @else
            <div style="overflow-x:auto;">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Judul Tugas</th>
                            <th>Course</th>
                            <th style="text-align:center;">Dikumpulkan</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingSubmissions as $sub)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div class="avatar">
                                        {{ strtoupper(substr($sub->student->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span style="font-weight:600; color:#1e293b;">
                                        {{ $sub->student->name ?? '–' }}
                                    </span>
                                </div>
                            </td>
                            <td>{{ $sub->assignment->title ?? '–' }}</td>
                            <td style="font-size:12px; color:#64748b;">
                                {{ Str::limit($sub->assignment->course->title ?? '–', 30) }}
                            </td>
                            <td style="text-align:center; font-size:12px; color:#94a3b8;">
                                {{ $sub->created_at->format('d M Y, H:i') }}
                            </td>
                            <td style="text-align:center;">
                                <a href="{{ route('teacher.submissions.show', $sub->id) }}"
                                   style="background:#3b5bdb; color:white; padding:5px 12px;
                                          border-radius:7px; font-size:11px; font-weight:700;
                                          text-decoration:none; display:inline-block;">
                                    Nilai
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    @php
        $chartLabels  = $courses->pluck('title')->map(fn($t) => Str::limit($t, 20));
        $chartValues  = [];
        foreach ($courses as $course) {
            $courseIds     = collect([$course->id]);
            $allMatIds     = \App\Models\Material::whereHas('module',
                                fn($q) => $q->where('course_id', $course->id))->pluck('id');
            $enrolled      = \App\Models\CourseEnrollment::where('course_id', $course->id)
                                ->where('status', 'approved')->count();
            $possible      = $allMatIds->count() * ($enrolled ?: 1);
            $completed     = \App\Models\MaterialProgress::whereIn('material_id', $allMatIds)
                                ->where('is_completed', true)->count();
            $chartValues[] = $possible > 0 ? round($completed / $possible * 100) : 0;
        }
    @endphp

    const labels = @json($chartLabels);
    const values = @json($chartValues);

    const canvas   = document.getElementById('classProgressChart');
    const existing = Chart.getChart(canvas);
    if (existing) existing.destroy();

    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Progress (%)',
                data: values,
                backgroundColor: values.map(v =>
                    v >= 70 ? 'rgba(34,197,94,.75)'
                  : v >= 40 ? 'rgba(245,158,11,.75)'
                  :           'rgba(244,63,94,.75)'
                ),
                borderRadius: 8,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#94a3b8',
                    bodyColor: '#f8fafc',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => ` Progress: ${ctx.parsed.y}%`
                    }
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8', maxRotation: 30 },
                },
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        font: { size: 11 },
                        color: '#94a3b8',
                        callback: v => v + '%',
                    },
                    grid: { color: '#f1f5f9' },
                },
            },
        },
    });
})();
</script>
</x-app-layout>