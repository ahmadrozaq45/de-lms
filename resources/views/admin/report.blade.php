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
.filter-bar   { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:18px; align-items:center; }
.filter-bar select, .filter-bar input {
    padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:9px;
    font-size:13px; outline:none; background:white; color:#1e293b; }
.filter-bar select:focus, .filter-bar input:focus { border-color:#3b5bdb; }

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
.ai-at_risk   { background:#fef3c7; color:#92400e; }
.ai-on_track  { background:#dcfce7; color:#166534; }
.ai-completed { background:#dbeafe; color:#1e40af; }
.ai-badge     { font-size:11px; font-weight:700; padding:2px 8px; border-radius:6px; }

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
</style>

<div class="rp-wrap">

    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:22px;">
        <div>
            <h1 style="font-size:21px; font-weight:800; color:#1e293b; margin:0 0 2px;">Report Admin</h1>
            <p style="font-size:13px; color:#94a3b8; margin:0;">Data seluruh platform — {{ now()->format('d M Y') }}</p>
        </div>
        <button class="btn-export" onclick="exportToExcel()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M12 2v13M6 9l6 6 6-6"/><path d="M4 20h16"/>
            </svg>
            Export Excel
        </button>
    </div>

    {{-- Summary Stats --}}
    <div class="stat-mini">
        @foreach([
            ['Total User',        $totalUsers,         '#3b5bdb'],
            ['Siswa',             $totalStudents,       '#16a34a'],
            ['Guru',              $totalTeachers,       '#1d4ed8'],
            ['Kursus',            $totalCourses,        '#7c3aed'],
            ['Enrollment Aktif',  $totalEnrollments,    '#0891b2'],
            ['Submission',        $totalSubmissions,    '#d97706'],
            ['Quiz Attempts',     $totalQuizAttempts,   '#dc2626'],
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
    <div class="rp-card" style="padding:14px 20px; margin-bottom:16px;">
        <div class="filter-bar">
            <div style="position:relative;">
                <svg width="13" height="13" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"
                     style="position:absolute; left:10px; top:50%; transform:translateY(-50%);">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="filterName" placeholder="Cari nama siswa/guru..."
                       style="padding-left:30px; min-width:220px;" oninput="filterTable()">
            </div>
            <select id="filterCourse" onchange="filterTable()">
                <option value="">Semua Kursus</option>
                @foreach($topCourses as $c)
                <option value="{{ $c->id }}">{{ $c->title }}</option>
                @endforeach
            </select>
            <select id="filterAi" onchange="filterTable()">
                <option value="">Semua Status AI</option>
                <option value="on_track">On Track</option>
                <option value="at_risk">At Risk</option>
                <option value="completed">Completed</option>
            </select>
            <button onclick="resetFilter()"
                    style="padding:8px 14px; border:1px solid #e2e8f0; border-radius:9px;
                           font-size:13px; color:#64748b; background:white; cursor:pointer;">
                Reset
            </button>
        </div>
    </div>

    {{-- Per-Course Detail Table --}}
    @forelse($topCourses as $course)
    @php
        $enrolled = \App\Models\CourseEnrollment::where('course_id', $course->id)
            ->where('status','approved')->with('user')->get();
    @endphp
    <div class="course-group" data-course-id="{{ $course->id }}">
        <div class="course-header">
            <div>
                <h3>{{ $course->title }}</h3>
                <span class="course-meta">Guru: {{ $course->teacher->name ?? '–' }}</span>
            </div>
            <div style="font-size:12px; color:#64748b; font-weight:600;">
                {{ $enrolled->count() }} siswa aktif
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
                        <th style="text-align:center;">Status AI</th>
                        <th>Rekomendasi AI</th>
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
                        <td style="text-align:center;">
                            @if($ai)
                                <span class="ai-badge ai-{{ $ai->status_prediction }}">
                                    {{ ucfirst(str_replace('_',' ', $ai->status_prediction)) }}
                                </span>
                            @else
                                <span class="badge-na">–</span>
                            @endif
                        </td>
                        <td style="max-width:220px;">
                            @if($ai?->recommendation)
                                <div style="font-size:12px; color:#475569; line-height:1.5;
                                            display:-webkit-box; -webkit-line-clamp:2;
                                            -webkit-box-orient:vertical; overflow:hidden;"
                                     title="{{ $ai->recommendation }}">
                                    {{ $ai->recommendation }}
                                </div>
                            @else
                                <span style="font-size:12px; color:#94a3b8;">Belum ada analisis AI</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center; padding:28px; color:#94a3b8;">
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
        Belum ada kursus atau data enrollment.
    </div>
    @endforelse

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
// ── Filter ───────────────────────────────────────
function filterTable() {
    const name   = document.getElementById('filterName').value.toLowerCase();
    const course = document.getElementById('filterCourse').value;
    const ai     = document.getElementById('filterAi').value;
    const rows   = document.querySelectorAll('.report-row');

    rows.forEach(row => {
        const matchName   = !name   || row.dataset.name.includes(name);
        const matchCourse = !course || row.dataset.course === course;
        const matchAi     = !ai     || row.dataset.ai === ai;
        row.style.display = (matchName && matchCourse && matchAi) ? '' : 'none';
    });
}

function resetFilter() {
    document.getElementById('filterName').value   = '';
    document.getElementById('filterCourse').value = '';
    document.getElementById('filterAi').value     = '';
    filterTable();
}

// ── Export Excel ─────────────────────────────────
function exportToExcel() {
    const wb = XLSX.utils.book_new();

    // Sheet 1: Ringkasan
    const summary = [
        ['REPORT ADMIN LMS', '', '', new Date().toLocaleDateString('id-ID')],
        [],
        ['RINGKASAN PLATFORM'],
        ['Total User',       {{ $totalUsers }}],
        ['Siswa',            {{ $totalStudents }}],
        ['Guru',             {{ $totalTeachers }}],
        ['Kursus',           {{ $totalCourses }}],
        ['Enrollment Aktif', {{ $totalEnrollments }}],
        ['Total Submission', {{ $totalSubmissions }}],
        ['Quiz Attempts',    {{ $totalQuizAttempts }}],
        ['Rata-rata Nilai Quiz', {{ round($avgQuizScore, 1) }}],
        [],
        ['STATUS SUBMISSION'],
        ['Pending',  {{ $submissionStats['pending'] }}],
        ['Dinilai',  {{ $submissionStats['graded'] }}],
        ['Diulas',   {{ $submissionStats['reviewed'] }}],
    ];
    const wsSummary = XLSX.utils.aoa_to_sheet(summary);
    wsSummary['!cols'] = [{wch:28},{wch:14}];
    XLSX.utils.book_append_sheet(wb, wsSummary, 'Ringkasan');

    // Sheet 2: Data siswa per kursus
    const header = ['Kursus','Guru','Nama Siswa','Email','Progress (%)','Rata-rata Quiz','Quiz Gagal','Status AI','Rekomendasi AI'];
    const rows = [];
    document.querySelectorAll('.report-row').forEach(row => {
        const tds = row.querySelectorAll('td');
        const courseName  = row.closest('.course-group')?.querySelector('h3')?.textContent?.trim() ?? '';
        const teacherName = row.closest('.course-group')?.querySelector('.course-meta')?.textContent?.replace('Guru: ','')?.trim() ?? '';
        const name        = tds[0]?.querySelector('div')?.textContent?.trim() ?? '';
        const email       = tds[0]?.querySelectorAll('div')[1]?.textContent?.trim() ?? '';
        const progress    = tds[1]?.querySelector('.prog-lbl')?.textContent?.trim() ?? '';
        const quiz        = tds[2]?.querySelector('span')?.textContent?.trim() ?? '–';
        const failedQ     = tds[3]?.querySelector('span')?.textContent?.trim() ?? '0x';
        const aiStatus    = tds[4]?.querySelector('span')?.textContent?.trim() ?? '–';
        const aiRec       = tds[5]?.querySelector('div')?.textContent?.trim() ?? '–';
        rows.push([courseName, teacherName, name, email, progress, quiz, failedQ, aiStatus, aiRec]);
    });

    const wsData = XLSX.utils.aoa_to_sheet([header, ...rows]);
    wsData['!cols'] = [
        {wch:24},{wch:18},{wch:22},{wch:28},
        {wch:14},{wch:16},{wch:12},{wch:14},{wch:50}
    ];
    // Style header row (bold)
    ['A1','B1','C1','D1','E1','F1','G1','H1','I1'].forEach(ref => {
        if (wsData[ref]) wsData[ref].s = { font: { bold: true } };
    });
    XLSX.utils.book_append_sheet(wb, wsData, 'Data Siswa');

    // Sheet 3: Top Courses
    const tcHeader = ['Kursus','Guru','Jumlah Siswa'];
    const tcRows   = @json($topCourses->map(fn($c) => [$c->title, $c->teacher->name ?? '–', $c->enrollments_count]));
    const wsTc = XLSX.utils.aoa_to_sheet([tcHeader, ...tcRows]);
    wsTc['!cols'] = [{wch:28},{wch:22},{wch:14}];
    XLSX.utils.book_append_sheet(wb, wsTc, 'Top Kursus');

    // Download
    const date = new Date().toISOString().slice(0,10);
    XLSX.writeFile(wb, `report-admin-${date}.xlsx`);
}
</script>
</x-app-layout>