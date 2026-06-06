<x-app-layout>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div style="margin-bottom:28px;">
        <h1 style="font-size:22px; font-weight:800; color:#1e293b; margin:0 0 2px;">Laporan Belajar Saya</h1>
        <p style="font-size:13px; color:#64748b; margin:0;">Progres dan performa kamu di semua kursus · {{ now()->format('d M Y') }}</p>
    </div>

    {{-- Summary --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:14px; margin-bottom:28px;">
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px;">
            <div style="font-size:28px; font-weight:800; color:#3b5bdb;">{{ $courseReports->count() }}</div>
            <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">Kursus Diikuti</div>
        </div>
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px;">
            <div style="font-size:28px; font-weight:800; color:#6366f1;">{{ $overallProgress }}%</div>
            <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">Progres Rata-rata</div>
        </div>
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px;">
            <div style="font-size:28px; font-weight:800; color:{{ ($avgQuiz ?? 0) >= 70 ? '#16a34a' : '#d97706' }};">{{ $avgQuiz ? round($avgQuiz) : '—' }}</div>
            <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">Avg Skor Quiz</div>
        </div>
    </div>

    {{-- Per course --}}
    @forelse($courseReports as $r)
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; margin-bottom:16px; overflow:hidden;">
        <div style="padding:18px 22px; border-bottom:1px solid #f8fafc; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
                <div style="font-size:15px; font-weight:700; color:#1e293b;">{{ $r['course']->title }}</div>
                <div style="font-size:12px; color:#94a3b8;">Guru: {{ $r['course']->teacher->name ?? '-' }} · Bergabung {{ $r['joined_at']->format('d M Y') }}</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:20px; font-weight:800; color:#6366f1;">{{ $r['progress'] }}%</div>
                <div style="font-size:11px; color:#94a3b8;">{{ $r['completed'] }}/{{ $r['total_mat'] }} materi</div>
            </div>
        </div>

        {{-- Progress bar --}}
        <div style="padding:0 22px 4px;">
            <div style="height:5px; background:#f1f5f9; border-radius:100px; overflow:hidden; margin:12px 0;">
                <div style="height:100%; width:{{ $r['progress'] }}%; background:{{ $r['progress'] >= 80 ? '#22c55e' : ($r['progress'] >= 40 ? '#3b5bdb' : '#f59e0b') }}; border-radius:100px; transition:width 0.5s;"></div>
            </div>
        </div>

        <div style="padding:14px 22px 18px; display:flex; flex-wrap:wrap; gap:20px;">
            {{-- Submissions --}}
            <div>
                <div style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:8px;">Pengumpulan Tugas ({{ $r['submissions']->count() }})</div>
                @forelse($r['submissions'] as $sub)
                <div style="display:inline-flex; align-items:center; gap:6px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:5px 10px; font-size:12px; margin:2px;">
                    <span style="font-weight:600; color:#1e293b;">{{ $sub->assignment->title ?? 'Tugas' }}</span>
                    <span style="padding:1px 7px; border-radius:4px; font-weight:700; background:{{ $sub->status==='graded' ? '#dcfce7' : '#fef9c3' }}; color:{{ $sub->status==='graded' ? '#16a34a' : '#a16207' }}; font-size:11px;">{{ $sub->status }}</span>
                    @if($sub->score)<span style="font-weight:800; color:#3b5bdb;">{{ $sub->score }}</span>@endif
                </div>
                @empty<span style="font-size:13px; color:#94a3b8;">Belum ada pengumpulan.</span>@endforelse
            </div>

            {{-- Quiz attempts --}}
            @if($r['quiz_attempts']->count() > 0)
            <div>
                <div style="font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:8px;">Hasil Quiz ({{ $r['quiz_attempts']->count() }} attempt · Avg: {{ $r['avg_quiz'] }})</div>
                @foreach($r['quiz_attempts'] as $att)
                <div style="display:inline-flex; align-items:center; gap:6px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:5px 10px; font-size:12px; margin:2px;">
                    <span style="font-weight:600; color:#1e293b;">{{ $att->quiz->title ?? 'Quiz' }}</span>
                    <span style="font-weight:800; color:{{ $att->is_passed ? '#16a34a' : '#dc2626' }};">{{ $att->score }}</span>
                    <span style="font-size:10px; color:{{ $att->is_passed ? '#16a34a' : '#dc2626' }};">{{ $att->is_passed ? '✓ Lulus' : '✗ Tidak' }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @empty
    <div style="background:white; border:1px dashed #e2e8f0; border-radius:14px; padding:60px; text-align:center;">
        <p style="font-size:15px; font-weight:600; color:#64748b; margin:0;">Kamu belum terdaftar di kursus manapun.</p>
    </div>
    @endforelse
</div>
</x-app-layout>
