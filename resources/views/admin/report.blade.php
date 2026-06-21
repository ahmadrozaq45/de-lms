<x-app-layout>
<style>
.rp-wrap      { max-width:1280px; margin:0 auto; padding:28px 24px; }
.rp-card      { background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px 24px; margin-bottom:16px; }

/* Stat row */
.stat-mini    { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:12px; margin-bottom:20px; }
.stat-box     { background:#f8fafc; border:1px solid #e2e8f0; border-radius:11px; padding:14px 18px; }
.stat-box-val { font-size:24px; font-weight:800; color:#1e293b; }
.stat-box-lbl { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-top:2px; }

/* Filter bar */
.filter-bar   { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; }
.filter-bar select, .filter-bar input[type="text"], .filter-bar input[type="date"] {
    padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:9px;
    font-size:13px; outline:none; background:white; color:#1e293b; height:38px; }
.filter-bar select:focus,
.filter-bar input[type="text"]:focus,
.filter-bar input[type="date"]:focus { border-color:#3b5bdb; }

/* Filter group */
.filter-group       { display:flex; flex-direction:column; gap:5px; }
.filter-group-label { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; padding-left:2px; }

/* Input dengan icon search */
.input-icon-wrap { position:relative; }
.input-icon-wrap svg { position:absolute; left:10px; top:50%; transform:translateY(-50%); pointer-events:none; }
.input-icon-wrap input { padding-left:30px; }

/* Separator antar group filter */
.filter-separator {
    width:1px; background:#e2e8f0; align-self:stretch; margin:0 2px;
}

/* Table */
.rp-table     { width:100%; border-collapse:collapse; font-size:13px; }
.rp-table th  { padding:10px 14px; font-size:11px; font-weight:700; color:#94a3b8;
                text-transform:uppercase; text-align:left; border-bottom:1px solid #f1f5f9;
                background:#f8fafc; white-space:nowrap; }
.rp-table td  { padding:11px 14px; color:#475569; border-bottom:1px solid #f8fafc; vertical-align:middle; }
.rp-table tr:last-child td { border-bottom:none; }
.rp-table tr:hover td      { background:#f8fafc; }

/* Progress bar */
.prog-wrap { display:flex; align-items:center; gap:8px; }
.prog-bar  { flex:1; height:8px; background:#f1f5f9; border-radius:99px; overflow:hidden; min-width:60px; }
.prog-fill { height:100%; border-radius:99px; background:#3b5bdb; }
.prog-lbl  { font-size:12px; font-weight:700; color:#475569; width:34px; text-align:right; }

/* Score badge */
.badge-pass { background:#dcfce7; color:#16a34a; font-weight:700; border-radius:6px; padding:2px 7px; font-size:11px; }
.badge-fail { background:#fee2e2; color:#dc2626; font-weight:700; border-radius:6px; padding:2px 7px; font-size:11px; }
.badge-na   { background:#f1f5f9; color:#94a3b8; font-weight:600; border-radius:6px; padding:2px 7px; font-size:11px; }

/* AI status badge */
.ai-at_risk          { background:#fef3c7; color:#92400e; }
.ai-needs_improvement{ background:#ffedd5; color:#9a3412; }
.ai-on_track         { background:#dcfce7; color:#166534; }
.ai-excellent        { background:#dbeafe; color:#1e40af; }
.ai-completed        { background:#dbeafe; color:#1e40af; }
.ai-badge            { font-size:11px; font-weight:700; padding:2px 8px; border-radius:6px; white-space:nowrap; }

/* AI summary text */
.ai-summary-text {
    font-size:12px; color:#475569; line-height:1.6;
    display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;
}
.ai-summary-empty { font-size:12px; color:#94a3b8; font-style:italic; }
.btn-generate-ai {
    display:inline-flex; align-items:center; gap:4px;
    background:#eff6ff; color:#3b5bdb; border:1px solid #bfdbfe;
    font-size:11px; font-weight:700; padding:4px 10px; border-radius:7px;
    cursor:pointer; white-space:nowrap;
}
.btn-generate-ai:hover { background:#dbeafe; }
.btn-generate-ai:disabled { opacity:.5; cursor:not-allowed; }

/* Course group header */
.course-header {
    background:linear-gradient(135deg,#eff6ff,#f5f3ff);
    border:1px solid #c7d2fe; border-radius:10px;
    padding:12px 18px; margin:20px 0 10px;
    display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;
}
.course-header h3 { font-size:14px; font-weight:700; color:#1e293b; margin:0; }
.course-meta      { font-size:12px; color:#6366f1; font-weight:600; }

.btn-export {
    display:inline-flex; align-items:center; gap:6px;
    background:#16a34a; color:white; padding:9px 18px;
    border-radius:10px; font-size:13px; font-weight:600;
    text-decoration:none; border:none; cursor:pointer; }
.btn-export:hover { background:#15803d; }

.sec-title { font-size:14px; font-weight:700; color:#1e293b; margin:0 0 4px; }
.sec-sub   { font-size:12px; color:#94a3b8; }

/* Submission stats */
.sub-chips { display:flex; gap:8px; flex-wrap:wrap; }
.sub-chip  { font-size:12px; font-weight:700; padding:3px 10px; border-radius:99px; }

/* Active filter chip */
.active-filter-chip {
    display:inline-flex; align-items:center; gap:5px;
    background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8;
    font-size:11px; font-weight:700; padding:3px 9px; border-radius:99px;
}
/* Print-friendly (Export PDF) */
@media print {
    .btn-export, .filter-bar, #filterInfo, .btn-generate-ai { display:none !important; }
    .rp-wrap { max-width:100%; padding:0; }
    .rp-card { break-inside:avoid; border:1px solid #cbd5e1; }
    .course-group { break-inside:avoid; }
    body { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
}
</style>

<div class="rp-wrap">

    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:22px;">
        <div>
            <h1 style="font-size:21px; font-weight:800; color:#1e293b; margin:0 0 2px;">Report Admin</h1>
            <p style="font-size:13px; color:#94a3b8; margin:0;">Data seluruh platform — {{ now()->format('d M Y') }}</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn-export" style="background:#dc2626;" onclick="exportToPdf()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/>
                </svg>
                Export PDF
            </button>
            <button class="btn-export" onclick="exportToExcel()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M12 2v13M6 9l6 6 6-6"/><path d="M4 20h16"/>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="stat-mini">
        @foreach([
            ['Total User',        $totalUsers,          '#3b5bdb'],
            ['Siswa',             $totalStudents,        '#16a34a'],
            ['Guru',              $totalTeachers,        '#1d4ed8'],
            ['Kursus',            $totalCourses,         '#7c3aed'],
            ['Enrollment Aktif',  $totalEnrollments,     '#0891b2'],
            ['Submission',        $totalSubmissions,     '#d97706'],
            ['Quiz Attempts',     $totalQuizAttempts,    '#dc2626'],
            ['Rata-rata Nilai',   round($avgQuizScore,1),'#059669'],
        ] as [$lbl, $val, $clr])
        <div class="stat-box">
            <div class="stat-box-val" style="color:{{ $clr }};">{{ $val }}</div>
            <div class="stat-box-lbl">{{ $lbl }}</div>
        </div>
        @endforeach
    </div>

    {{-- Submission Status Chips --}}
    <div class="rp-card" style="margin-bottom:16px;">
        <p class="sec-title" style="margin-bottom:10px;">Status Submission</p>
        <div class="sub-chips">
            <span class="sub-chip" style="background:#fef3c7; color:#92400e;">
                Pending: {{ $submissionStats['pending'] }}
            </span>
            <span class="sub-chip" style="background:#dbeafe; color:#1e40af;">
                Dinilai: {{ $submissionStats['graded'] }}
            </span>
            <span class="sub-chip" style="background:#dcfce7; color:#166534;">
                Diulas: {{ $submissionStats['reviewed'] }}
            </span>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rp-card" style="padding:16px 20px; margin-bottom:16px;">
        <p class="sec-title" style="margin-bottom:14px;">Filter Data</p>
        <form method="GET" action="{{ route('admin.report') }}" id="filterForm">
            <div class="filter-bar">

                {{-- Filter Nama Siswa --}}
                <div class="filter-group">
                    <span class="filter-group-label">Nama Siswa</span>
                    <div class="input-icon-wrap">
                        <svg width="13" height="13" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="filter_siswa"
                               placeholder="Cari nama siswa..."
                               value="{{ $filterSiswa }}"
                               style="min-width:190px;"
                               oninput="debounceSubmit()">
                    </div>
                </div>

                {{-- Filter Nama Guru --}}
                <div class="filter-group">
                    <span class="filter-group-label">Nama Guru</span>
                    <div class="input-icon-wrap">
                        <svg width="13" height="13" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="filter_guru"
                               placeholder="Cari nama guru..."
                               value="{{ $filterGuru }}"
                               style="min-width:190px;"
                               oninput="debounceSubmit()">
                    </div>
                </div>

                {{-- Separator --}}
                <div class="filter-separator"></div>

                {{-- Filter Tanggal Kursus Dibuat: Dari --}}
                <div class="filter-group">
                    <span class="filter-group-label">Kursus Dibuat: Dari</span>
                    <input type="date" name="filter_date_from"
                           value="{{ $filterDateFrom }}"
                           max="{{ $filterDateTo ?: date('Y-m-d') }}"
                           onchange="document.getElementById('filterForm').submit()">
                </div>

                {{-- Filter Tanggal Kursus Dibuat: Sampai --}}
                <div class="filter-group">
                    <span class="filter-group-label">Sampai</span>
                    <input type="date" name="filter_date_to"
                           value="{{ $filterDateTo }}"
                           min="{{ $filterDateFrom ?: '' }}"
                           max="{{ date('Y-m-d') }}"
                           onchange="document.getElementById('filterForm').submit()">
                </div>

                {{-- Filter AI Summarize (client-side) --}}
                <div class="filter-group">
                    <span class="filter-group-label">AI Summarize</span>
                    <select id="filterAi" onchange="filterClientSide()">
                        <option value="">Semua Status</option>
                        <option value="excellent">Excellent</option>
                        <option value="on_track">On Track</option>
                        <option value="needs_improvement">Needs Improvement</option>
                        <option value="at_risk">At Risk</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                {{-- Tombol Reset --}}
                <div class="filter-group">
                    <span class="filter-group-label" style="visibility:hidden;">–</span>
                    <a href="{{ route('admin.report') }}"
                       style="display:inline-flex; align-items:center; height:38px; padding:0 14px;
                              border:1px solid #e2e8f0; border-radius:9px; font-size:13px;
                              color:#64748b; background:white; cursor:pointer; text-decoration:none;">
                        Reset
                    </a>
                </div>

            </div>
        </form>

        {{-- Chip filter aktif --}}
        @if($filterSiswa || $filterGuru || $filterDateFrom || $filterDateTo)
        <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            <span style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Filter aktif:</span>
            @if($filterSiswa)
                <span class="active-filter-chip">Siswa: {{ $filterSiswa }}</span>
            @endif
            @if($filterGuru)
                <span class="active-filter-chip">Guru: {{ $filterGuru }}</span>
            @endif
            @if($filterDateFrom || $filterDateTo)
                <span class="active-filter-chip">
                    Tanggal:
                    {{ $filterDateFrom ? \Carbon\Carbon::parse($filterDateFrom)->format('d M Y') : '…' }}
                    –
                    {{ $filterDateTo ? \Carbon\Carbon::parse($filterDateTo)->format('d M Y') : 'sekarang' }}
                </span>
            @endif
            <span style="font-size:12px; color:#64748b;">
                — menampilkan <strong style="color:#3b5bdb;">{{ $allCourses->count() }}</strong> kursus
            </span>
        </div>
        @endif

        {{-- Info filter client-side AI --}}
        <div id="filterInfo" style="margin-top:10px; font-size:12px; color:#94a3b8; display:none;">
            Menampilkan <span id="filterCount" style="font-weight:700; color:#3b5bdb;"></span> dari
            <span id="filterTotal" style="font-weight:700; color:#475569;"></span> siswa (filter AI Summarize aktif)
        </div>
    </div>

    {{-- Per-Course Detail Table --}}
    @forelse($allCourses as $course)
    @php
        $enrolledQuery = \App\Models\CourseEnrollment::where('course_id', $course->id)
            ->where('status','approved')
            ->with('user');

        if ($filterSiswa !== '') {
            $enrolledQuery->whereHas('user', fn($q) => $q->where('name', 'like', "%{$filterSiswa}%"));
        }

        $enrolled = $enrolledQuery->get();

        if ($filterSiswa !== '' && $enrolled->isEmpty()) continue;
    @endphp
    <div class="course-group" data-course-id="{{ $course->id }}" data-teacher="{{ strtolower($course->teacher->name ?? '') }}">
        <div class="course-header">
            <div>
                <h3>{{ $course->title }}</h3>
                <span class="course-meta">Guru: {{ $course->teacher->name ?? '–' }}</span>
                <span style="font-size:11px; color:#94a3b8; margin-left:8px;">
                    Dibuat {{ $course->created_at->format('d M Y') }}
                </span>
            </div>
            <div style="font-size:12px; color:#64748b; font-weight:600;">
                <span class="visible-count">{{ $enrolled->count() }}</span> siswa aktif
            </div>
        </div>

        <div class="rp-card" style="padding:0; overflow:hidden;">
            <div style="overflow-x:auto;">
            <table class="rp-table" id="table-course-{{ $course->id }}">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th style="text-align:center;">Progress Materi</th>
                        <th style="text-align:center;">Rata-rata Quiz</th>
                        <th style="text-align:center;">Quiz Gagal</th>
                        <th style="text-align:center; width:110px;">Status AI</th>
                        <th style="min-width:200px;">Ringkasan AI</th>
                        <th style="min-width:200px;">Rekomendasi AI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrolled as $enrollment)
                    @php
                        $student = $enrollment->user;
                        $matIds  = \App\Models\Material::whereHas('module', fn($q) => $q->where('course_id', $course->id))->pluck('id');
                        $total   = $matIds->count();
                        $done    = \App\Models\MaterialProgress::where('user_id', $student->id)
                                     ->whereIn('material_id', $matIds)->where('is_completed', true)->count();
                        $pct     = $total > 0 ? round($done/$total*100) : 0;
                        $avgQ    = \App\Models\QuizAttempt::where('user_id', $student->id)
                                     ->whereHas('quiz', fn($q)=>$q->where('course_id', $course->id))
                                     ->whereNotNull('score')->avg('score');
                        $failQ   = \App\Models\QuizAttempt::where('user_id', $student->id)
                                     ->whereHas('quiz', fn($q)=>$q->where('course_id', $course->id))
                                     ->where('is_passed', false)->whereNotNull('score')->count();
                        $ai      = \App\Models\AiAnalysis::where('user_id', $student->id)
                                     ->where('course_id', $course->id)->latest()->first();
                    @endphp
                    <tr class="report-row"
                        data-name="{{ strtolower($student->name) }}"
                        data-course="{{ $course->id }}"
                        data-ai="{{ $ai?->status_prediction ?? '' }}">
                        <td>
                            <div style="font-weight:600; color:#1e293b;">{{ $student->name }}</div>
                            <div style="font-size:11px; color:#94a3b8;">{{ $student->email }}</div>
                        </td>
                        <td style="text-align:center;">
                            <div class="prog-wrap">
                                <div class="prog-bar"><div class="prog-fill" style="width:{{ $pct }}%;"></div></div>
                                <div class="prog-lbl">{{ $pct }}%</div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            @if($avgQ !== null)
                                <span class="{{ $avgQ >= 70 ? 'badge-pass' : 'badge-fail' }}">
                                    {{ round($avgQ, 1) }}
                                </span>
                            @else
                                <span class="badge-na">–</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($failQ > 0)
                                <span class="badge-fail">{{ $failQ }}x</span>
                            @else
                                <span class="badge-pass">0x</span>
                            @endif
                        </td>
                        <td style="text-align:center; width:110px;">
                            @if($ai)
                                <span class="ai-badge ai-{{ $ai->status_prediction }}">
                                    {{ ucfirst(str_replace('_',' ', $ai->status_prediction)) }}
                                </span>
                            @else
                                <span class="badge-na">–</span>
                            @endif
                        </td>
                        <td>
                            @if($ai?->ai_summary)
                                <div style="font-size:12px; color:#475569; line-height:1.6; max-width:260px;">
                                    {{ $ai->ai_summary }}
                                </div>
                            @else
                                <span style="font-size:12px; color:#94a3b8; font-style:italic;">Belum di-generate</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:6px; align-items:flex-start;">
                                @if($ai?->recommendation)
                                    <div style="font-size:12px; color:#475569; line-height:1.6; max-width:260px;">
                                        {{ $ai->recommendation }}
                                    </div>
                                @else
                                    <span style="font-size:12px; color:#94a3b8; font-style:italic;">Belum di-generate</span>
                                @endif
                                <button type="button" class="btn-generate-ai"
                                        data-student-id="{{ $student->id }}"
                                        data-course-id="{{ $course->id }}"
                                        onclick="generateAiSummary(this)">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path d="M21 12a9 9 0 11-6.219-8.56"/>
                                    </svg>
                                    {{ $ai ? 'Generate Ulang' : 'Generate' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row"><td colspan="7" style="text-align:center; padding:28px; color:#94a3b8;">
                        Tidak ada siswa aktif di kursus ini.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>
    @empty
    <div class="rp-card" style="text-align:center; padding:48px; color:#94a3b8;">
        @if($filterSiswa || $filterGuru || $filterDateFrom || $filterDateTo)
            <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 10px; display:block;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <div style="font-size:14px; font-weight:600; color:#94a3b8; margin-bottom:4px;">Tidak ada hasil ditemukan</div>
            <div style="font-size:12px; color:#cbd5e1;">Coba ubah filter atau <a href="{{ route('admin.report') }}" style="color:#3b5bdb;">reset</a></div>
        @else
            Belum ada kursus atau data enrollment.
        @endif
    </div>
    @endforelse

    {{-- Pesan saat semua tersaring oleh filter AI (client-side) --}}
    <div id="noResultMsg" style="display:none;">
        <div class="rp-card" style="text-align:center; padding:36px; color:#94a3b8;">
            <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 10px; display:block;">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <div style="font-size:14px; font-weight:600; color:#94a3b8; margin-bottom:4px;">Tidak ada hasil ditemukan</div>
            <div style="font-size:12px; color:#cbd5e1;">Coba ubah filter AI Summarize atau <a href="{{ route('admin.report') }}" style="color:#3b5bdb;">reset</a></div>
        </div>
    </div>

    {{-- Top Teachers & Top Courses summary --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px;">
        <div class="rp-card">
            <p class="sec-title" style="margin-bottom:14px;">Top 5 Kursus</p>
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <th style="padding:8px 0; font-size:11px; font-weight:700; color:#94a3b8; text-align:left;">Kursus</th>
                        <th style="padding:8px 0; font-size:11px; font-weight:700; color:#94a3b8; text-align:left;">Guru</th>
                        <th style="padding:8px 0; font-size:11px; font-weight:700; color:#94a3b8; text-align:right;">Siswa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCourses as $c)
                    <tr style="border-bottom:1px solid #f8fafc;">
                        <td style="padding:9px 0; color:#1e293b; font-weight:600;">{{ $c->title }}</td>
                        <td style="padding:9px 0; color:#64748b;">{{ $c->teacher->name ?? '–' }}</td>
                        <td style="padding:9px 0; text-align:right; font-weight:700; color:#3b5bdb;">{{ $c->enrollments_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="rp-card">
            <p class="sec-title" style="margin-bottom:14px;">Top 5 Guru</p>
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <th style="padding:8px 0; font-size:11px; font-weight:700; color:#94a3b8; text-align:left;">Nama</th>
                        <th style="padding:8px 0; font-size:11px; font-weight:700; color:#94a3b8; text-align:right;">Kursus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topTeachers as $t)
                    <tr style="border-bottom:1px solid #f8fafc;">
                        <td style="padding:9px 0; color:#1e293b; font-weight:600;">{{ $t->name }}</td>
                        <td style="padding:9px 0; text-align:right; font-weight:700; color:#f59e0b;">{{ $t->teacher_courses_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- SheetJS dari CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
// ── Filter client-side (hanya untuk AI Summarize) ────────────────────────────────
function filterClientSide() {
    const ai     = document.getElementById('filterAi').value;
    const groups = document.querySelectorAll('.course-group');
    const rows   = document.querySelectorAll('.report-row');
    const noMsg  = document.getElementById('noResultMsg');
    const info   = document.getElementById('filterInfo');

    let totalVisible = 0;
    const totalRows  = rows.length;

    rows.forEach(row => {
        const matchAi = !ai || row.dataset.ai === ai;
        row.style.display = matchAi ? '' : 'none';
        if (matchAi) totalVisible++;
    });

    let anyGroupVisible = false;
    groups.forEach(group => {
        const groupRows      = group.querySelectorAll('.report-row');
        const visibleInGroup = [...groupRows].filter(r => r.style.display !== 'none').length;
        group.style.display  = visibleInGroup > 0 ? '' : 'none';
        if (visibleInGroup > 0) {
            anyGroupVisible = true;
            const counter = group.querySelector('.visible-count');
            if (counter) counter.textContent = visibleInGroup;
        }
    });

    noMsg.style.display = (!anyGroupVisible && totalRows > 0) ? '' : 'none';

    if (ai) {
        info.style.display = '';
        document.getElementById('filterCount').textContent = totalVisible;
        document.getElementById('filterTotal').textContent  = totalRows;
    } else {
        info.style.display = 'none';
    }
}

// ── Debounce submit form untuk input teks ─────────────────────────────────────
let debounceTimer;
function debounceSubmit() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 500);
}

// ── Generate AI Summary per siswa ──────────────────────────────────────────────
async function generateAiSummary(btn) {
    const studentId = btn.dataset.studentId;
    const courseId  = btn.dataset.courseId;
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = 'Memproses...';

    try {
        const response = await fetch('{{ route("ai.generate-for-student") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ student_id: studentId, course_id: courseId }),
        });

        if (!response.ok) {
            throw new Error('Gagal generate AI summary (status ' + response.status + ')');
        }

        // Refresh halaman agar data ringkasan AI terbaru tampil
        window.location.reload();
    } catch (err) {
        alert('Gagal generate AI summary: ' + err.message + '\n\nPastikan API key provider aktif sudah diisi di Pengaturan > API & AI.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// ── Export PDF (via print dialog browser) ──────────────────────────────────────
function exportToPdf() {
    document.title = `Report Admin LMS - {{ now()->format('d-m-Y') }}`;
    window.print();
}

// ── Export Excel ──────────────────────────────────────────────────────────────
function exportToExcel() {
    const wb = XLSX.utils.book_new();

    // Sheet 1: Ringkasan
    const filterInfo = [
        @if($filterSiswa)   ['Filter Siswa',  '{{ $filterSiswa }}'], @endif
        @if($filterGuru)    ['Filter Guru',   '{{ $filterGuru }}'], @endif
        @if($filterDateFrom)['Tanggal Dari',  '{{ $filterDateFrom }}'], @endif
        @if($filterDateTo)  ['Tanggal Sampai','{{ $filterDateTo }}'], @endif
    ];
    const summary = [
        ['REPORT ADMIN LMS', '', '', new Date().toLocaleDateString('id-ID')],
        [],
        ['RINGKASAN PLATFORM'],
        ['Total User',           {{ $totalUsers }}],
        ['Siswa',                {{ $totalStudents }}],
        ['Guru',                 {{ $totalTeachers }}],
        ['Kursus',               {{ $totalCourses }}],
        ['Enrollment Aktif',     {{ $totalEnrollments }}],
        ['Total Submission',     {{ $totalSubmissions }}],
        ['Quiz Attempts',        {{ $totalQuizAttempts }}],
        ['Rata-rata Nilai Quiz', {{ round($avgQuizScore, 1) }}],
        [],
        ['STATUS SUBMISSION'],
        ['Pending',  {{ $submissionStats['pending'] }}],
        ['Dinilai',  {{ $submissionStats['graded'] }}],
        ['Diulas',   {{ $submissionStats['reviewed'] }}],
        [],
        ...filterInfo,
    ];
    const wsSummary = XLSX.utils.aoa_to_sheet(summary);
    wsSummary['!cols'] = [{wch:28},{wch:14}];
    XLSX.utils.book_append_sheet(wb, wsSummary, 'Ringkasan');

    // Sheet 2: Data siswa (hanya yang visible)
    const header = ['Kursus','Guru','Tgl Kursus Dibuat','Nama Siswa','Email','Progress (%)','Rata-rata Quiz','Quiz Gagal','Status AI','Analisis & Rekomendasi AI'];
    const dataRows = [];
    document.querySelectorAll('.course-group').forEach(group => {
        if (group.style.display === 'none') return;
        const headerEl    = group.querySelector('.course-header');
        const courseName  = headerEl?.querySelector('h3')?.textContent?.trim() ?? '';
        const teacherName = headerEl?.querySelector('.course-meta')?.textContent?.replace('Guru: ','')?.trim() ?? '';
        const courseDate  = headerEl?.querySelector('span[style*="94a3b8"]')?.textContent?.trim()?.replace('Dibuat ','') ?? '';
        group.querySelectorAll('.report-row').forEach(row => {
            if (row.style.display === 'none') return;
            const tds      = row.querySelectorAll('td');
            const name     = tds[0]?.querySelector('div')?.textContent?.trim() ?? '';
            const email    = tds[0]?.querySelectorAll('div')[1]?.textContent?.trim() ?? '';
            const progress = tds[1]?.querySelector('.prog-lbl')?.textContent?.trim() ?? '';
            const quiz     = tds[2]?.querySelector('span')?.textContent?.trim() ?? '–';
            const failedQ  = tds[3]?.querySelector('span')?.textContent?.trim() ?? '0x';
            const aiStatus = tds[4]?.querySelector('span')?.textContent?.trim() ?? '–';
            const aiSummary= tds[5]?.querySelector('.ai-summary-text')?.textContent?.trim()
                           ?? tds[5]?.querySelector('.ai-summary-empty')?.textContent?.trim() ?? '–';
            const aiRec    = tds[6]?.querySelector('div')?.textContent?.trim() ?? '–';
            dataRows.push([courseName, teacherName, courseDate, name, email, progress, quiz, failedQ, aiStatus, aiSummary, aiRec]);
        });
    });

    const wsData = XLSX.utils.aoa_to_sheet([header, ...dataRows]);
    wsData['!cols'] = [
        {wch:24},{wch:18},{wch:16},{wch:22},{wch:28},
        {wch:14},{wch:16},{wch:12},{wch:14},{wch:55},{wch:50}
    ];
    XLSX.utils.book_append_sheet(wb, wsData, 'Data Siswa');

    // Sheet 3: Top Courses
    const tcHeader = ['Kursus','Guru','Jumlah Siswa'];
    const tcRows   = @json($topCourses->map(fn($c) => [$c->title, $c->teacher->name ?? '–', $c->enrollments_count]));
    const wsTc = XLSX.utils.aoa_to_sheet([tcHeader, ...tcRows]);
    wsTc['!cols'] = [{wch:28},{wch:22},{wch:14}];
    XLSX.utils.book_append_sheet(wb, wsTc, 'Top Kursus');

    const date = new Date().toISOString().slice(0,10);
    XLSX.writeFile(wb, `report-admin-${date}.xlsx`);
}
</script>
</x-app-layout>