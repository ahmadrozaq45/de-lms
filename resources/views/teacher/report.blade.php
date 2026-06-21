<x-app-layout>
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div style="margin-bottom:28px;">
        <h1 style="font-size:22px; font-weight:800; color:#1e293b; margin:0 0 2px;">Laporan Mengajar</h1>
        <p style="font-size:13px; color:#64748b; margin:0;">Ringkasan performa seluruh kelas Anda · {{ now()->format('d M Y') }}</p>
    </div>

    {{-- Totals --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:14px; margin-bottom:28px;">
        @foreach([['Kursus',$totals['courses'],'#3b5bdb'],['Total Siswa',$totals['students'],'#16a34a'],['Submission',$totals['submissions'],'#d97706'],['Belum Dinilai',$totals['pending'],'#dc2626']] as [$l,$v,$c])
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px;">
            <div style="font-size:28px; font-weight:800; color:{{ $c }};">{{ $v }}</div>
            <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">{{ $l }}</div>
        </div>
        @endforeach
    </div>

    {{-- Per-course table --}}
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
        <div style="padding:18px 22px; border-bottom:1px solid #f1f5f9;">
            <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0;">Performa Per Kursus</h3>
        </div>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                    <th style="padding:11px 20px; text-align:left; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Kursus</th>
                    <th style="padding:11px 14px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Siswa</th>
                    <th style="padding:11px 14px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Submission</th>
                    <th style="padding:11px 14px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Pending</th>
                    <th style="padding:11px 14px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Quiz Attempt</th>
                    <th style="padding:11px 14px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Avg Skor</th>
                    <th style="padding:11px 20px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Pass Rate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courseStats as $stat)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:14px 20px;">
                        <div style="font-size:14px; font-weight:600; color:#1e293b;">{{ $stat['course']->title }}</div>
                        <div style="font-size:11px; color:#94a3b8;">{{ $stat['course']->modules->count() }} modul</div>
                    </td>
                    <td style="padding:14px; text-align:center; font-size:14px; font-weight:600; color:#3b5bdb;">{{ $stat['enrolled'] }}</td>
                    <td style="padding:14px; text-align:center; font-size:14px; color:#475569;">{{ $stat['submissions'] }}</td>
                    <td style="padding:14px; text-align:center;">
                        @if($stat['pending'] > 0)
                            <span style="background:#fef3c7; color:#b45309; font-size:12px; font-weight:700; padding:3px 10px; border-radius:100px;">{{ $stat['pending'] }}</span>
                        @else
                            <span style="color:#94a3b8; font-size:13px;">—</span>
                        @endif
                    </td>
                    <td style="padding:14px; text-align:center; font-size:14px; color:#475569;">{{ $stat['quiz_count'] }}</td>
                    <td style="padding:14px; text-align:center; font-size:14px; font-weight:700; color:{{ $stat['avg_score'] ? ($stat['avg_score'] >= 70 ? '#16a34a' : '#dc2626') : '#94a3b8' }};">
                        {{ $stat['avg_score'] ? $stat['avg_score'] : '—' }}
                    </td>
                    <td style="padding:14px 20px; text-align:center;">
                        @if($stat['quiz_count'] > 0)
                            @php $rate = round($stat['pass_count'] / $stat['quiz_count'] * 100); @endphp
                            <div style="font-size:13px; font-weight:700; color:{{ $rate >= 70 ? '#16a34a' : '#dc2626' }}; margin-bottom:4px;">{{ $rate }}%</div>
                            <div style="height:6px; background:#f1f5f9; border-radius:100px; overflow:hidden;">
                                <div style="height:100%; width:{{ $rate }}%; background:{{ $rate >= 70 ? '#22c55e' : '#ef4444' }}; border-radius:100px;"></div>
                            </div>
                        @else
                            <span style="color:#94a3b8; font-size:13px;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; padding:48px; color:#94a3b8;">Belum ada kursus.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- AI Summarize per Siswa --}}
    <div style="margin-top:28px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
            <div>
                <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 2px;">AI Summarize Siswa</h3>
                <p style="font-size:12px; color:#94a3b8; margin:0;">Ringkasan aktivitas belajar tiap siswa, dihasilkan AI berdasarkan materi, quiz, dan tugas.</p>
            </div>
        </div>

        @forelse($studentsByCourse as $group)
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:16px;">
            <div style="padding:14px 20px; border-bottom:1px solid #f1f5f9; background:#f8fafc;">
                <h4 style="font-size:13px; font-weight:700; color:#1e293b; margin:0;">{{ $group['course']->title }}</h4>
            </div>
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Siswa</th>
                        <th style="padding:10px 14px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Progress</th>
                        <th style="padding:10px 14px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Avg Quiz</th>
                        <th style="padding:10px 14px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Status AI</th>
                        <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Ringkasan AI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['students'] as $row)
                    @php $student = $row['student']; $ai = $row['ai']; @endphp
                    <tr style="border-bottom:1px solid #f8fafc;">
                        <td style="padding:13px 20px;">
                            <div style="font-size:13px; font-weight:600; color:#1e293b;">{{ $student->name }}</div>
                            <div style="font-size:11px; color:#94a3b8;">{{ $student->email }}</div>
                        </td>
                        <td style="padding:13px; text-align:center;">
                            <div style="display:flex; align-items:center; gap:6px; justify-content:center;">
                                <div style="width:50px; height:7px; background:#f1f5f9; border-radius:99px; overflow:hidden;">
                                    <div style="height:100%; width:{{ $row['percent'] }}%; background:#3b5bdb;"></div>
                                </div>
                                <span style="font-size:12px; font-weight:600; color:#475569;">{{ $row['percent'] }}%</span>
                            </div>
                        </td>
                        <td style="padding:13px; text-align:center; font-size:13px; font-weight:600; color:{{ $row['avg_quiz'] !== null ? ($row['avg_quiz'] >= 70 ? '#16a34a' : '#dc2626') : '#94a3b8' }};">
                            {{ $row['avg_quiz'] !== null ? $row['avg_quiz'] : '—' }}
                        </td>
                        <td style="padding:13px; text-align:center;">
                            @if($ai)
                                @php
                                    $statusColors = [
                                        'at_risk' => ['#fef3c7', '#92400e'], 'needs_improvement' => ['#ffedd5', '#9a3412'],
                                        'on_track' => ['#dcfce7', '#166534'], 'excellent' => ['#dbeafe', '#1e40af'],
                                        'completed' => ['#dbeafe', '#1e40af'],
                                    ];
                                    [$bg, $fg] = $statusColors[$ai->status_prediction] ?? ['#f1f5f9', '#64748b'];
                                @endphp
                                <span style="background:{{ $bg }}; color:{{ $fg }}; font-size:11px; font-weight:700; padding:2px 9px; border-radius:6px; white-space:nowrap;">
                                    {{ ucfirst(str_replace('_',' ', $ai->status_prediction)) }}
                                </span>
                            @else
                                <span style="color:#cbd5e1; font-size:12px;">–</span>
                            @endif
                        </td>
                        <td style="padding:13px 20px; max-width:260px;">
                            <div style="display:flex; flex-direction:column; gap:6px; align-items:flex-start;">
                                @if($ai?->ai_summary)
                                    <div style="font-size:12px; color:#475569; line-height:1.5;
                                                display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"
                                         title="{{ $ai->ai_summary }}">
                                        {{ $ai->ai_summary }}
                                    </div>
                                @else
                                    <span style="font-size:12px; color:#94a3b8; font-style:italic;">Belum di-generate</span>
                                @endif
                                <button type="button"
                                        style="display:inline-flex; align-items:center; gap:4px; background:#eff6ff; color:#3b5bdb;
                                               border:1px solid #bfdbfe; font-size:11px; font-weight:700; padding:4px 10px;
                                               border-radius:7px; cursor:pointer; white-space:nowrap;"
                                        data-student-id="{{ $student->id }}"
                                        data-course-id="{{ $group['course']->id }}"
                                        onclick="generateAiSummary(this)">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path d="M21 12a9 9 0 11-6.219-8.56"/>
                                    </svg>
                                    {{ $ai ? 'Generate Ulang' : 'Generate' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @empty
        <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; padding:40px; text-align:center; color:#94a3b8;">
            Belum ada siswa aktif di kursus Anda.
        </div>
        @endforelse
    </div>

</div>

<script>
async function generateAiSummary(btn) {
    const studentId = btn.dataset.studentId;
    const courseId  = btn.dataset.courseId;
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = 'Memproses...';

    try {
        const response = await fetch('/web/ai/generate-for-student', {
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

        window.location.reload();
    } catch (err) {
        alert('Gagal generate AI summary: ' + err.message + '\n\nPastikan API key provider sudah diisi di Pengaturan, dan provider pilihan Anda sudah disimpan di tab Preferensi AI.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}
</script>
</x-app-layout>