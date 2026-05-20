<x-app-layout>
<style>
    .stat-card { background:white; border-radius:14px; padding:24px; border:1px solid #e2e8f0; text-align:center; }
    .badge-pass { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; font-size:11px; font-weight:700; padding:2px 10px; border-radius:10px; }
    .badge-fail { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; font-size:11px; font-weight:700; padding:2px 10px; border-radius:10px; }
    .badge-ongoing { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:11px; font-weight:700; padding:2px 10px; border-radius:10px; }
</style>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <a href="{{ route('teacher.quizzes.show', $quiz->id) }}"
       class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke {{ $quiz->title }}
    </a>

    <div style="margin-bottom:24px;">
        <h1 style="font-size:26px; font-weight:800; color:#1e293b; margin:0 0 4px 0;">Hasil Quiz</h1>
        <p style="font-size:14px; color:#64748b; margin:0;">{{ $quiz->title }} — {{ $quiz->course->title }}</p>
    </div>

    {{-- Statistik --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:16px; margin-bottom:28px;">
        <div class="stat-card">
            <div style="font-size:34px; font-weight:800; color:#1e293b;">{{ $stats['total'] }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Total Attempt</div>
        </div>
        <div class="stat-card">
            <div style="font-size:34px; font-weight:800; color:#3b5bdb;">{{ $stats['completed'] }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Selesai</div>
        </div>
        <div class="stat-card">
            <div style="font-size:34px; font-weight:800; color:#16a34a;">{{ $stats['passed'] }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Lulus</div>
        </div>
        <div class="stat-card">
            <div style="font-size:34px; font-weight:800; color:#d97706;">{{ $stats['avg_score'] }}%</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Rata-rata Skor</div>
        </div>
        <div class="stat-card">
            <div style="font-size:34px; font-weight:800; color:#475569;">{{ $quiz->passing_score }}%</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Nilai Lulus</div>
        </div>
    </div>

    {{-- Tabel Hasil --}}
    <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.03);">
        <div style="padding:20px 28px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">Rincian per Siswa</h2>
            <span style="font-size:13px; color:#94a3b8;">{{ $quiz->attempts->count() }} entri</span>
        </div>

        @if($quiz->attempts->isEmpty())
            <div style="padding:64px; text-align:center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 16px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                <p style="color:#64748b; font-weight:500; margin:0;">Belum ada siswa yang mengerjakan quiz ini.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px;">
                            <th style="padding:14px 24px; text-align:left;">#</th>
                            <th style="padding:14px 24px; text-align:left;">Nama Siswa</th>
                            <th style="padding:14px 24px; text-align:center;">Skor</th>
                            <th style="padding:14px 24px; text-align:center;">Status</th>
                            <th style="padding:14px 24px; text-align:left;">Waktu Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quiz->attempts as $i => $attempt)
                        <tr style="border-top:1px solid #f1f5f9; transition:background .15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding:16px 24px; font-size:13px; color:#94a3b8; font-weight:600;">{{ $i + 1 }}</td>
                            <td style="padding:16px 24px;">
                                <div style="font-size:15px; font-weight:600; color:#1e293b;">{{ $attempt->user->name ?? '-' }}</div>
                                <div style="font-size:12px; color:#94a3b8;">{{ $attempt->user->email ?? '' }}</div>
                            </td>
                            <td style="padding:16px 24px; text-align:center;">
                                @if($attempt->completed_at)
                                    <span style="font-size:20px; font-weight:800; color:{{ $attempt->is_passed ? '#16a34a' : '#dc2626' }};">
                                        {{ $attempt->score }}%
                                    </span>
                                @else
                                    <span style="font-size:13px; color:#94a3b8;">—</span>
                                @endif
                            </td>
                            <td style="padding:16px 24px; text-align:center;">
                                @if(!$attempt->completed_at)
                                    <span class="badge-ongoing">Sedang Dikerjakan</span>
                                @elseif($attempt->is_passed)
                                    <span class="badge-pass">✓ Lulus</span>
                                @else
                                    <span class="badge-fail">✗ Belum Lulus</span>
                                @endif
                            </td>
                            <td style="padding:16px 24px; font-size:13px; color:#64748b;">
                                {{ $attempt->completed_at ? $attempt->completed_at->format('d M Y, H:i') : '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
</x-app-layout>