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
</div>
</x-app-layout>
