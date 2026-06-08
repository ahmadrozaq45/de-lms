<x-app-layout>
<style>
/* ── Base ── */
.ad-wrap   { max-width:1280px; margin:0 auto; padding:28px 24px; }
.ad-card   { background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px 24px; }

/* ── Stat cards ── */
.stat-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin-bottom:24px; }
.stat-card { background:white; border:1px solid #e2e8f0; border-radius:14px; padding:20px 22px;
             display:flex; align-items:center; gap:14px; }
.stat-icon { width:44px; height:44px; border-radius:11px; display:flex; align-items:center;
             justify-content:center; flex-shrink:0; }
.stat-val  { font-size:28px; font-weight:800; color:#1e293b; line-height:1; }
.stat-lbl  { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;
             letter-spacing:.5px; margin-top:3px; }

/* ── Section grid ── */
.row2      { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
.row3      { display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:16px; }
@media(max-width:900px){ .row2,.row3{ grid-template-columns:1fr; } }

/* ── Table inside card ── */
.mini-table { width:100%; border-collapse:collapse; }
.mini-table th { padding:10px 14px; font-size:11px; font-weight:700; color:#94a3b8;
                 text-transform:uppercase; text-align:left; border-bottom:1px solid #f1f5f9; }
.mini-table td { padding:11px 14px; font-size:13px; color:#475569;
                 border-bottom:1px solid #f8fafc; vertical-align:middle; }
.mini-table tr:last-child td { border-bottom:none; }
.mini-table tr:hover td { background:#f8fafc; }

/* ── Bar chart ── */
.bar-row   { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.bar-label { font-size:12px; color:#64748b; width:130px; flex-shrink:0;
             white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.bar-track { flex:1; height:10px; background:#f1f5f9; border-radius:99px; overflow:hidden; }
.bar-fill  { height:100%; border-radius:99px; }
.bar-count { font-size:12px; font-weight:700; color:#475569; width:28px; text-align:right; }

/* ── Avatar ── */
.avatar { width:32px; height:32px; border-radius:50%;
          background:linear-gradient(135deg,#3b5bdb,#6366f1);
          display:flex; align-items:center; justify-content:center;
          color:white; font-size:12px; font-weight:700; flex-shrink:0; }

.sec-title { font-size:14px; font-weight:700; color:#1e293b; margin:0 0 16px; }
.sec-sub   { font-size:12px; color:#94a3b8; font-weight:500; }
</style>

<div class="ad-wrap">

    {{-- Header --}}
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:22px;">
        <div>
            <h1 style="font-size:21px; font-weight:800; color:#1e293b; margin:0 0 2px;">Analytic Dashboard</h1>
            <p style="font-size:13px; color:#94a3b8; margin:0;">Ringkasan keseluruhan platform — {{ now()->format('d M Y') }}</p>
        </div>
        <a href="{{ route('admin.report') }}"
           style="display:inline-flex; align-items:center; gap:6px; background:#3b5bdb; color:white;
                  padding:9px 18px; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <rect x="5" y="2" width="14" height="20" rx="2"/><path d="M9 7h6M9 11h6M9 15h4"/>
            </svg>
            Lihat Report Lengkap
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7;">
                <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div><div class="stat-val">{{ number_format($stats['total_students']) }}</div><div class="stat-lbl">Siswa</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;">
                <svg width="20" height="20" fill="none" stroke="#1d4ed8" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div><div class="stat-val">{{ number_format($stats['total_teachers']) }}</div><div class="stat-lbl">Guru</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;">
                <svg width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                </svg>
            </div>
            <div><div class="stat-val">{{ number_format($stats['total_courses']) }}</div><div class="stat-lbl">Kursus</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7;">
                <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <path d="M9 12h6M9 16h4"/>
                </svg>
            </div>
            <div><div class="stat-val">{{ number_format($stats['total_quizzes']) }}</div><div class="stat-lbl">Quiz</div></div>
        </div>
    </div>

    {{-- Row: Growth Chart + Recent Users --}}
    <div class="row2">
        <div class="ad-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <p class="sec-title" style="margin:0;">Pertumbuhan User <span class="sec-sub">(6 bulan terakhir)</span></p>
                <div style="display:flex; gap:12px; font-size:11px; font-weight:600;">
                    <span style="display:flex; align-items:center; gap:4px; color:#3b5bdb;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#3b5bdb;display:inline-block;"></span>Siswa
                    </span>
                    <span style="display:flex; align-items:center; gap:4px; color:#f59e0b;">
                        <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>Guru
                    </span>
                </div>
            </div>
            <div style="position:relative; height:180px; width:100%;"><canvas id="growthChart"></canvas></div>
        </div>

        <div class="ad-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <p class="sec-title" style="margin:0;">User Terbaru</p>
                <a href="{{ route('admin.users.index') }}" style="font-size:12px; color:#3b5bdb; text-decoration:none; font-weight:600;">Lihat semua →</a>
            </div>
            <table class="mini-table">
                @forelse($recentUsers as $u)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:9px;">
                            <div class="avatar">{{ strtoupper(substr($u->name,0,1)) }}</div>
                            <div>
                                <div style="font-weight:600; color:#1e293b; font-size:13px;">{{ $u->name }}</div>
                                <div style="font-size:11px; color:#94a3b8;">{{ $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <span style="font-size:11px; font-weight:700; padding:3px 8px; border-radius:99px;
                            background:{{ $u->role==='admin' ? '#fee2e2' : ($u->role==='teacher' ? '#dbeafe' : '#dcfce7') }};
                            color:{{ $u->role==='admin' ? '#991b1b' : ($u->role==='teacher' ? '#1e40af' : '#166534') }};">
                            {{ ucfirst($u->role) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="2" style="text-align:center; color:#94a3b8; padding:24px;">Belum ada user.</td></tr>
                @endforelse
            </table>
        </div>
    </div>

    {{-- Row: Top Courses Bar + Top Guru --}}
    <div class="row3">
        <div class="ad-card">
            <p class="sec-title">Top Kursus <span class="sec-sub">berdasarkan jumlah siswa aktif</span></p>
            @php $maxEnroll = $enrollmentStats->max('enrollments_count') ?: 1; @endphp
            @forelse($enrollmentStats as $course)
            @php $pct = $maxEnroll > 0 ? round($course->enrollments_count / $maxEnroll * 100) : 0; @endphp
            <div class="bar-row">
                <div class="bar-label" title="{{ $course->title }}">{{ $course->title }}</div>
                <div class="bar-track">
                    <div class="bar-fill" style="width:{{ $pct }}%; background:#3b5bdb;"></div>
                </div>
                <div class="bar-count">{{ $course->enrollments_count }}</div>
            </div>
            @empty
            <p style="text-align:center; color:#94a3b8; padding:24px 0; margin:0;">Belum ada kursus.</p>
            @endforelse
        </div>

        <div class="ad-card">
            <p class="sec-title">Top Guru <span class="sec-sub">kursus terbanyak</span></p>
            @php
                $topTeachers = \App\Models\User::where('role','teacher')
                    ->withCount('teacherCourses')
                    ->orderByDesc('teacher_courses_count')
                    ->limit(5)->get();
            @endphp
            <table class="mini-table">
                @forelse($topTeachers as $t)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:9px;">
                            <div class="avatar" style="background:linear-gradient(135deg,#f59e0b,#f97316);">
                                {{ strtoupper(substr($t->name,0,1)) }}
                            </div>
                            <span style="font-weight:600; color:#1e293b; font-size:13px;">{{ $t->name }}</span>
                        </div>
                    </td>
                    <td style="text-align:right; font-weight:700; color:#3b5bdb; font-size:13px;">
                        {{ $t->teacher_courses_count }} kursus
                    </td>
                </tr>
                @empty
                <tr><td colspan="2" style="text-align:center; color:#94a3b8; padding:24px;">Belum ada guru.</td></tr>
                @endforelse
            </table>
        </div>
    </div>

    {{-- Recent Quiz Failures --}}
    <div class="ad-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <p class="sec-title" style="margin:0;">
                Siswa Gagal Quiz
                <span class="sec-sub"> — 30 hari terakhir</span>
            </p>
            <span style="font-size:12px; font-weight:700; background:#fee2e2; color:#dc2626; padding:4px 10px; border-radius:99px;">
                {{ $recentFailures->count() }} kejadian
            </span>
        </div>
        @if($recentFailures->isEmpty())
        <p style="text-align:center; color:#94a3b8; padding:28px 0; margin:0;">
            Tidak ada kegagalan quiz dalam 30 hari terakhir.
        </p>
        @else
        <div style="overflow-x:auto;">
        <table class="mini-table">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Quiz</th>
                    <th style="text-align:center;">Skor</th>
                    <th style="text-align:center;">Min. Lulus</th>
                    <th style="text-align:right;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentFailures as $f)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div class="avatar" style="background:linear-gradient(135deg,#f43f5e,#ec4899);">
                                {{ strtoupper(substr($f->user->name ?? '?', 0, 1)) }}
                            </div>
                            <span style="font-weight:600; color:#1e293b;">{{ $f->user->name ?? '–' }}</span>
                        </div>
                    </td>
                    <td>{{ $f->quiz->title ?? '–' }}</td>
                    <td style="text-align:center;"><span class="score-fail">{{ $f->score }}</span></td>
                    <td style="text-align:center; font-size:13px; color:#64748b;">{{ $f->quiz->passing_score ?? '–' }}</td>
                    <td style="text-align:right; font-size:12px; color:#94a3b8;">{{ $f->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    const labels   = @json($userGrowth->pluck('label'));
    const students = @json($userGrowth->pluck('students'));
    const teachers = @json($userGrowth->pluck('teachers'));

    const canvas = document.getElementById('growthChart');

    // Destroy instance lama jika ada, supaya tidak menumpuk
    const existing = Chart.getChart(canvas);
    if (existing) existing.destroy();

    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Siswa',
                    data: students,
                    borderColor: '#3b5bdb',
                    backgroundColor: 'rgba(59,91,219,.08)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b5bdb',
                    tension: 0.35,
                    fill: true,
                },
                {
                    label: 'Guru',
                    data: teachers,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,.08)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#f59e0b',
                    tension: 0.35,
                    fill: true,
                },
            ],
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
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { size: 11 },
                        color: '#94a3b8',
                        stepSize: 1,
                        callback: v => Number.isInteger(v) ? v : null,
                    },
                    grid: { color: '#f1f5f9' },
                },
            },
        },
    });
})();
</script>
</x-app-layout>