<x-app-layout>
<style>
    .tab-btn { padding:10px 20px; font-size:14px; font-weight:600; cursor:pointer; border:none; background:none; color:#64748b; border-bottom:2px solid transparent; transition:all 0.2s; }
    .tab-btn.active { color:#3b5bdb; border-bottom-color:#3b5bdb; }
    .tab-content { display:none; }
    .tab-content.active { display:block; }
    .student-row { background:white; border:1px solid #e2e8f0; border-radius:14px; margin-bottom:12px; overflow:hidden; transition:box-shadow 0.2s; }
    .student-row:hover { box-shadow:0 4px 12px rgba(0,0,0,0.08); }
    .pending-row { background:#fffbeb; border:1px solid #fde68a; border-radius:14px; margin-bottom:10px; }
</style>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <a href="{{ route('teacher.courses.show', $course->id) }}"
       class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke {{ $course->title }}
    </a>

    {{-- Flash messages --}}
    @if(session('success'))
        <div style="margin-bottom:20px; padding:13px 18px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; color:#16a34a; font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header + Course Code --}}
    <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-start; justify-content:space-between; margin-bottom:28px;">
        <div>
            <h1 style="font-size:22px; font-weight:800; color:#1e293b; margin:0 0 4px;">Kelola Siswa</h1>
            <p style="font-size:14px; color:#64748b; margin:0;">
                Kursus: <strong style="color:#1e293b;">{{ $course->title }}</strong>
            </p>
        </div>

        {{-- Course Code Card --}}
        <div style="background:#eef2ff; border:1px solid #c7d2fe; border-radius:14px; padding:16px 22px; display:flex; align-items:center; gap:18px;">
            <div>
                <div style="font-size:11px; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:0.8px; margin-bottom:4px;">Kode Kelas</div>
                <div id="course-code-display" style="font-family:monospace; font-size:26px; font-weight:800; color:#3730a3; letter-spacing:6px;">{{ $course->course_code }}</div>
                <div style="font-size:11px; color:#818cf8; margin-top:2px;">Bagikan ke siswa agar bisa bergabung</div>
            </div>
            <button onclick="copyCourseCode('{{ $course->course_code }}')" id="copy-btn"
                    style="display:flex; flex-direction:column; align-items:center; gap:4px; background:#6366f1; color:white; border:none; border-radius:10px; padding:10px 14px; font-size:11px; font-weight:700; cursor:pointer; transition:background 0.2s; white-space:nowrap; min-width:64px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Salin
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:14px; margin-bottom:28px;">
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px;">
            <div style="font-size:26px; font-weight:800; color:#1e293b;">{{ $students->count() }}</div>
            <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">Siswa Aktif</div>
        </div>
        <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:16px 20px;">
            <div style="font-size:26px; font-weight:800; color:#b45309;">{{ $pendingEnrollments->count() }}</div>
            <div style="font-size:12px; font-weight:600; color:#92400e; text-transform:uppercase; margin-top:2px;">Menunggu Approve</div>
        </div>
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px;">
            <div style="font-size:26px; font-weight:800; color:#f59e0b;">{{ $pendingCount }}</div>
            <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">Tugas Pending</div>
        </div>
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px;">
            <div style="font-size:26px; font-weight:800; color:#22c55e;">{{ $gradedCount }}</div>
            <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">Sudah Dinilai</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
        <div style="display:flex; border-bottom:1px solid #e2e8f0; padding:0 16px; background:#f8fafc;">
            <button class="tab-btn active" onclick="switchTab(event,'approved')">
                Siswa Aktif
                <span style="background:#e0e7ff; color:#3730a3; font-size:11px; font-weight:700; padding:2px 8px; border-radius:100px; margin-left:6px;">{{ $students->count() }}</span>
            </button>
            <button class="tab-btn" onclick="switchTab(event,'pending')" id="pending-tab-btn">
                Menunggu Persetujuan
                @if($pendingEnrollments->count() > 0)
                    <span style="background:#fef3c7; color:#92400e; font-size:11px; font-weight:700; padding:2px 8px; border-radius:100px; margin-left:6px;">{{ $pendingEnrollments->count() }}</span>
                @endif
            </button>
        </div>

        {{-- TAB: Siswa Aktif --}}
        <div id="tab-approved" class="tab-content active" style="padding:24px;">
            @forelse($students as $student)
                @php
                    $submissions = $student->submissions;
                    $pending = $submissions->where('status', 'pending')->count();
                    $graded  = $submissions->where('status', 'graded')->count();
                    $avgScore = $submissions->whereNotNull('score')->avg('score');
                    $enrollment = \App\Models\CourseEnrollment::where('course_id', $course->id)
                        ->where('user_id', $student->id)->first();
                @endphp
                <div class="student-row">
                    <div style="padding:18px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#3b5bdb,#6366f1); display:flex; align-items:center; justify-content:center; color:white; font-size:15px; font-weight:800; flex-shrink:0;">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size:15px; font-weight:700; color:#1e293b;">{{ $student->name }}</div>
                                <div style="font-size:12px; color:#94a3b8;">{{ $student->email }}</div>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            @if($avgScore)
                                <span style="font-size:13px; font-weight:700; color:#3b5bdb;">Avg: {{ round($avgScore) }}/100</span>
                            @endif
                            @if($pending > 0)
                                <span style="font-size:12px; font-weight:700; background:#fef9c3; color:#a16207; padding:4px 10px; border-radius:7px;">{{ $pending }} pending</span>
                            @endif
                            <a href="{{ route('teacher.reviews.index', ['student' => $student->id, 'course' => $course->id]) }}"
                               style="display:inline-flex; align-items:center; gap:5px; background:#eff6ff; color:#3b5bdb; font-size:12px; font-weight:700; padding:7px 14px; border-radius:8px; text-decoration:none;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Review
                            </a>
                            @if($enrollment)
                            <form method="POST" action="{{ route('teacher.courses.students.destroy', [$course->id, $enrollment->id]) }}"
                                  onsubmit="return confirm('Hapus {{ $student->name }} dari kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        style="display:inline-flex; align-items:center; gap:5px; background:#fef2f2; color:#dc2626; font-size:12px; font-weight:700; padding:7px 14px; border-radius:8px; border:none; cursor:pointer;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="8" x2="23" y2="14"/><line x1="23" y1="8" x2="17" y2="14"/></svg>
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:48px 20px;">
                    <svg width="44" height="44" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 14px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <p style="font-size:15px; font-weight:600; color:#64748b; margin:0 0 6px;">Belum Ada Siswa Aktif</p>
                    <p style="font-size:13px; color:#94a3b8;">Bagikan kode <strong style="color:#3730a3;">{{ $course->course_code }}</strong> ke siswa agar mereka bisa bergabung.</p>
                </div>
            @endforelse
        </div>

        {{-- TAB: Pending --}}
        <div id="tab-pending" class="tab-content" style="padding:24px;">
            @forelse($pendingEnrollments as $enrollment)
                <div class="pending-row" style="padding:16px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,#f59e0b,#d97706); display:flex; align-items:center; justify-content:center; color:white; font-size:14px; font-weight:800; flex-shrink:0;">
                            {{ strtoupper(substr($enrollment->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:15px; font-weight:700; color:#1e293b;">{{ $enrollment->user->name ?? '-' }}</div>
                            <div style="font-size:12px; color:#92400e;">{{ $enrollment->user->email ?? '-' }}</div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:11px; color:#92400e; background:#fef3c7; padding:4px 10px; border-radius:100px; font-weight:600;">
                            Diminta {{ $enrollment->created_at->diffForHumans() }}
                        </span>
                        {{-- Approve --}}
                        <form method="POST" action="{{ route('teacher.courses.students.approve', [$course->id, $enrollment->id]) }}">
                            @csrf
                            <button type="submit"
                                    style="display:inline-flex; align-items:center; gap:5px; background:#22c55e; color:white; font-size:12px; font-weight:700; padding:8px 16px; border-radius:8px; border:none; cursor:pointer;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                Setujui
                            </button>
                        </form>
                        {{-- Tolak --}}
                        <form method="POST" action="{{ route('teacher.courses.students.destroy', [$course->id, $enrollment->id]) }}"
                              onsubmit="return confirm('Tolak permintaan dari {{ $enrollment->user->name ?? 'siswa ini' }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="display:inline-flex; align-items:center; gap:5px; background:#fef2f2; color:#dc2626; font-size:12px; font-weight:700; padding:8px 16px; border-radius:8px; border:none; cursor:pointer;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Tolak
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:48px 20px;">
                    <svg width="44" height="44" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 14px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <p style="font-size:15px; font-weight:600; color:#64748b; margin:0;">Tidak ada permintaan masuk.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

<script>
function switchTab(e, tabName) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    e.target.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

function copyCourseCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const btn = document.getElementById('copy-btn');
        btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>Tersalin!';
        btn.style.background = '#16a34a';
        setTimeout(() => {
            btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>Salin';
            btn.style.background = '#6366f1';
        }, 2000);
    });
}

// Auto-switch ke tab pending kalau ada request pending
@if($pendingEnrollments->count() > 0 && !session('success'))
    // Biarkan di tab aktif, cukup highlight badge saja
@endif
</script>
</x-app-layout>