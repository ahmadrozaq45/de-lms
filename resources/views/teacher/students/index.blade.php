<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <a href="{{ route('teacher.courses.show', $course->id) }}"
           class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke {{ $course->title }}
        </a>

        {{-- Header --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; margin-bottom:28px;">
            <div>
                <h1 style="font-size:24px; font-weight:800; color:#1e293b; margin:0 0 4px 0;">Kelola Siswa</h1>
                <p style="font-size:14px; color:#64748b; margin:0;">
                    Course: <strong style="color:#3b5bdb;">{{ $course->title }}</strong>
                    &nbsp;·&nbsp; Course ID: <strong style="background:#eff6ff; color:#3b5bdb; padding:2px 8px; border-radius:6px; font-size:13px;">{{ $course->course_code }}</strong>
                </p>
            </div>
            {{-- Share Course ID Box --}}
            <div style="background:#f0f4ff; border:1px solid #c7d2fe; border-radius:12px; padding:14px 20px; display:flex; align-items:center; gap:16px;">
                <div>
                    <div style="font-size:11px; font-weight:700; color:#6366f1; text-transform:uppercase; margin-bottom:2px;">Bagikan ke Siswa</div>
                    <div style="font-size:22px; font-weight:800; color:#3730a3; letter-spacing:4px;">{{ $course->course_code }}</div>
                </div>
                <button onclick="copyCourseCode('{{ $course->course_code }}')"
                        id="copy-btn"
                        style="display:flex; align-items:center; gap:6px; background:#6366f1; color:white; border:none; border-radius:8px; padding:8px 14px; font-size:12px; font-weight:700; cursor:pointer; transition:background 0.2s; white-space:nowrap;"
                        onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Salin Kode
                </button>
            </div>
        </div>

        {{-- Stats row --}}
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px; margin-bottom:28px;">
            <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px;">
                <div style="font-size:28px; font-weight:800; color:#1e293b;">{{ $students->count() }}</div>
                <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">Total Siswa</div>
            </div>
            <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px;">
                <div style="font-size:28px; font-weight:800; color:#f59e0b;">{{ $pendingCount }}</div>
                <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">Tugas Pending</div>
            </div>
            <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px;">
                <div style="font-size:28px; font-weight:800; color:#22c55e;">{{ $gradedCount }}</div>
                <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">Sudah Dinilai</div>
            </div>
        </div>

        {{-- Student List --}}
        @forelse($students as $student)
            @php
                $submissions = $student->submissions->where('course_id', $course->id);
                $pending = $submissions->where('status', 'pending')->count();
                $graded  = $submissions->where('status', 'graded')->count();
                $reviewed = $submissions->where('status', 'reviewed')->count();
                $total = $submissions->count();
                $avgScore = $submissions->whereNotNull('score')->avg('score');
            @endphp
            <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; margin-bottom:16px; overflow:hidden; transition:box-shadow 0.2s;"
                 onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">

                {{-- Student Header --}}
                <div style="padding:20px 24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                    <div style="display:flex; align-items:center; gap:14px;">
                        <div style="width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg, #3b5bdb, #6366f1); display:flex; align-items:center; justify-content:center; color:white; font-size:16px; font-weight:800; flex-shrink:0;">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:16px; font-weight:700; color:#1e293b;">{{ $student->name }}</div>
                            <div style="font-size:12px; color:#94a3b8;">{{ $student->email }}</div>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        @if($total > 0)
                            <span style="font-size:12px; font-weight:600; background:#f1f5f9; color:#475569; padding:5px 12px; border-radius:8px;">
                                {{ $total }} pengumpulan
                            </span>
                        @endif
                        @if($pending > 0)
                            <span style="font-size:12px; font-weight:700; background:#fef9c3; color:#a16207; padding:5px 12px; border-radius:8px;">
                                {{ $pending }} pending
                            </span>
                        @endif
                        @if($avgScore)
                            <span style="font-size:13px; font-weight:800; color:#3b5bdb;">
                                Avg: {{ round($avgScore) }}/100
                            </span>
                        @endif
                        <a href="{{ route('teacher.reviews.index', ['student' => $student->id, 'course' => $course->id]) }}"
                           style="display:inline-flex; align-items:center; gap:6px; background:#3b5bdb; color:white; font-size:13px; font-weight:700; padding:8px 16px; border-radius:8px; text-decoration:none; transition:background 0.2s;"
                           onmouseover="this.style.background='#2d45ba'" onmouseout="this.style.background='#3b5bdb'">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Review Tugas
                        </a>
                    </div>
                </div>

                {{-- Submission Summary --}}
                @if($submissions->count() > 0)
                <div style="padding:0 24px 18px; display:flex; flex-wrap:wrap; gap:10px;">
                    @foreach($submissions->take(5) as $sub)
                        <div style="display:flex; align-items:center; gap:8px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:7px 12px; font-size:12px;">
                            <span style="font-weight:600; color:#1e293b; max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                {{ $sub->assignment->title ?? 'Tugas' }}
                            </span>
                            <span style="padding:1px 7px; border-radius:4px; font-weight:700;
                                background:{{ $sub->status === 'graded' ? '#dcfce7' : ($sub->status === 'reviewed' ? '#dbeafe' : '#fef9c3') }};
                                color:{{ $sub->status === 'graded' ? '#16a34a' : ($sub->status === 'reviewed' ? '#1d4ed8' : '#a16207') }};">
                                {{ $sub->status }}
                            </span>
                            @if($sub->score)
                                <span style="font-weight:800; color:#3b5bdb;">{{ $sub->score }}</span>
                            @endif
                        </div>
                    @endforeach
                    @if($submissions->count() > 5)
                        <div style="background:#f1f5f9; border-radius:8px; padding:7px 12px; font-size:12px; color:#64748b; font-weight:600;">
                            +{{ $submissions->count() - 5 }} lainnya
                        </div>
                    @endif
                </div>
                @else
                <div style="padding:0 24px 16px;">
                    <span style="font-size:13px; color:#94a3b8; font-style:italic;">Belum ada pengumpulan tugas.</span>
                </div>
                @endif
            </div>
        @empty
            <div style="background:white; border:1px dashed #e2e8f0; border-radius:16px; padding:60px; text-align:center;">
                <svg width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 16px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <p style="font-size:16px; font-weight:600; color:#64748b; margin:0 0 6px 0;">Belum Ada Siswa</p>
                <p style="font-size:13px; color:#94a3b8; margin:0;">Bagikan Course ID <strong style="color:#3b5bdb;">{{ $course->id }}</strong> kepada siswa agar mereka bisa bergabung.</p>
            </div>
        @endforelse

    </div>

    <script>
        function copyCourseCode(courseCode) {
            navigator.clipboard.writeText(courseCode).then(() => {
                const btn = document.getElementById('copy-btn');
                btn.textContent = '✓ Tersalin!';
                btn.style.background = '#16a34a';
                setTimeout(() => {
                    btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Salin Kode';
                    btn.style.background = '#6366f1';
                }, 2000);
            });
        }
    </script>
</x-app-layout>