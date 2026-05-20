<x-app-layout>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <a href="{{ route('student.courses.show', $quiz->course_id) }}"
       class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke {{ $quiz->course->title }}
    </a>

    {{-- Quiz Info Card --}}
    <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 10px 15px -3px rgba(0,0,0,0.05); margin-bottom:20px;">
        <div style="background:linear-gradient(135deg,#3b5bdb,#6366f1); padding:32px;">
            <span style="font-size:11px; font-weight:700; background:rgba(255,255,255,.2); color:white; padding:3px 10px; border-radius:10px; letter-spacing:.5px;">QUIZ</span>
            <h1 style="font-size:26px; font-weight:800; color:white; margin:12px 0 6px 0;">{{ $quiz->title }}</h1>
            @if($quiz->description)
                <p style="font-size:14px; color:rgba(255,255,255,.85); margin:0; line-height:1.5;">{{ $quiz->description }}</p>
            @endif
        </div>
        <div style="padding:28px 32px;">
            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:20px; margin-bottom:28px;">
                <div style="text-align:center; padding:16px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                    <div style="font-size:28px; margin-bottom:4px;">⏱</div>
                    <div style="font-size:22px; font-weight:800; color:#1e293b;">{{ $quiz->time_limit }}</div>
                    <div style="font-size:12px; color:#64748b; font-weight:600;">menit</div>
                </div>
                <div style="text-align:center; padding:16px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                    <div style="font-size:28px; margin-bottom:4px;">📋</div>
                    <div style="font-size:22px; font-weight:800; color:#1e293b;">{{ $quiz->questions->count() }}</div>
                    <div style="font-size:12px; color:#64748b; font-weight:600;">soal</div>
                </div>
                <div style="text-align:center; padding:16px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                    <div style="font-size:28px; margin-bottom:4px;">✅</div>
                    <div style="font-size:22px; font-weight:800; color:#1e293b;">{{ $quiz->passing_score }}%</div>
                    <div style="font-size:12px; color:#64748b; font-weight:600;">nilai lulus</div>
                </div>
            </div>

            {{-- Riwayat attempt terakhir --}}
            @if($lastAttempt)
                <div style="background:{{ $lastAttempt->is_passed ? '#f0fdf4' : '#fff7ed' }}; border:1px solid {{ $lastAttempt->is_passed ? '#bbf7d0' : '#fed7aa' }}; border-radius:12px; padding:20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                    <div>
                        <div style="font-size:13px; font-weight:600; color:{{ $lastAttempt->is_passed ? '#15803d' : '#b45309' }}; margin-bottom:4px;">
                            Percobaan Terakhir ({{ $attemptCount }}x)
                        </div>
                        <div style="font-size:28px; font-weight:800; color:{{ $lastAttempt->is_passed ? '#16a34a' : '#d97706' }};">
                            {{ $lastAttempt->score }}%
                            <span style="font-size:14px; font-weight:600;">— {{ $lastAttempt->is_passed ? 'LULUS ✓' : 'BELUM LULUS' }}</span>
                        </div>
                    </div>
                    <a href="{{ route('student.quizzes.result', $lastAttempt->id) }}"
                       style="display:inline-flex; align-items:center; gap:6px; background:white; color:#475569; font-size:13px; font-weight:700; padding:8px 18px; border-radius:10px; text-decoration:none; border:1px solid #e2e8f0;">
                        Lihat Detail Hasil
                    </a>
                </div>
            @endif

            {{-- Instruksi --}}
            <div style="background:#fffbeb; border:1px solid #fef3c7; border-radius:12px; padding:20px; margin-bottom:24px;">
                <h3 style="font-size:14px; font-weight:700; color:#92400e; margin:0 0 10px 0; display:flex; align-items:center; gap:6px;">
                    <svg width="16" height="16" fill="#d97706" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 15h-2v-6h2zm0-8h-2V7h2z"/></svg>
                    Sebelum Memulai
                </h3>
                <ul style="list-style:none; padding:0; margin:0; font-size:13px; color:#78350f; display:flex; flex-direction:column; gap:6px;">
                    <li style="display:flex; align-items:flex-start; gap:8px;"><span>•</span> Kamu memiliki <strong>{{ $quiz->time_limit }} menit</strong> untuk menyelesaikan quiz ini.</li>
                    <li style="display:flex; align-items:flex-start; gap:8px;"><span>•</span> Terdapat <strong>{{ $quiz->questions->count() }} soal</strong> pilihan ganda.</li>
                    <li style="display:flex; align-items:flex-start; gap:8px;"><span>•</span> Minimal nilai lulus adalah <strong>{{ $quiz->passing_score }}%</strong>.</li>
                    <li style="display:flex; align-items:flex-start; gap:8px;"><span>•</span> Jangan tutup atau refresh halaman saat mengerjakan.</li>
                </ul>
            </div>

            @if($quiz->questions->isEmpty())
                <div style="text-align:center; padding:20px; background:#fef2f2; border-radius:12px; border:1px solid #fecaca; margin-bottom:20px;">
                    <p style="font-size:14px; color:#dc2626; font-weight:600; margin:0;">Quiz ini belum memiliki soal. Hubungi instruktur.</p>
                </div>
            @else
                <form action="{{ route('student.quizzes.start', $quiz->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            style="display:flex; align-items:center; justify-content:center; gap:10px; width:100%; background:#3b5bdb; color:white; border:none; border-radius:14px; padding:18px; font-size:17px; font-weight:800; cursor:pointer; transition:background .2s; letter-spacing:.3px;"
                            onmouseover="this.style.background='#2d45ba'" onmouseout="this.style.background='#3b5bdb'">
                        <svg width="20" height="20" fill="white" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"/></svg>
                        {{ $lastAttempt ? 'Coba Lagi' : 'Mulai Quiz Sekarang' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
</x-app-layout>