<x-app-layout>
<style>
    @keyframes popIn { 0%{transform:scale(.6);opacity:0} 70%{transform:scale(1.08)} 100%{transform:scale(1);opacity:1} }
    .score-circle { animation: popIn .6s ease forwards; }
    .answer-row { display:flex; align-items:flex-start; gap:12px; padding:14px 18px; border-radius:10px; margin-bottom:8px; }
    .answer-row.correct { background:#f0fdf4; border:1px solid #bbf7d0; }
    .answer-row.incorrect { background:#fef2f2; border:1px solid #fecaca; }
    .answer-row.unanswered { background:#f8fafc; border:1px solid #e2e8f0; }
</style>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Score Hero --}}
    <div style="background:white; border-radius:20px; border:1px solid #e2e8f0; padding:40px 32px; margin-bottom:24px; text-align:center; box-shadow:0 10px 25px rgba(0,0,0,0.06);">
        <div class="score-circle" style="display:inline-flex; align-items:center; justify-content:center;
             width:140px; height:140px; border-radius:50%; margin:0 auto 24px;
             background:{{ $attempt->is_passed ? 'linear-gradient(135deg,#22c55e,#16a34a)' : 'linear-gradient(135deg,#f59e0b,#d97706)' }};
             box-shadow:0 8px 24px {{ $attempt->is_passed ? 'rgba(34,197,94,.4)' : 'rgba(245,158,11,.4)' }};">
            <div>
                <div style="font-size:42px; font-weight:900; color:white; line-height:1;">{{ $attempt->score }}</div>
                <div style="font-size:16px; font-weight:700; color:rgba(255,255,255,.85);">poin</div>
            </div>
        </div>

        <h1 style="font-size:28px; font-weight:800; color:#1e293b; margin:0 0 8px 0;">
            {{ $attempt->is_passed ? '🎉 Selamat, Kamu Lulus!' : '😤 Hampir! Coba Lagi' }}
        </h1>
        <p style="font-size:15px; color:#64748b; margin:0 0 28px 0;">
            {{ $attempt->quiz->title }}
        </p>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:32px;">
            <div style="background:#f8fafc; border-radius:12px; padding:16px; border:1px solid #e2e8f0;">
                <div style="font-size:24px; font-weight:800; color:#16a34a;">{{ $correctCount }}</div>
                <div style="font-size:12px; color:#64748b; font-weight:600;">Benar</div>
            </div>
            <div style="background:#f8fafc; border-radius:12px; padding:16px; border:1px solid #e2e8f0;">
                <div style="font-size:24px; font-weight:800; color:#dc2626;">{{ $totalQuestions - $correctCount }}</div>
                <div style="font-size:12px; color:#64748b; font-weight:600;">Salah</div>
            </div>
            <div style="background:#f8fafc; border-radius:12px; padding:16px; border:1px solid #e2e8f0;">
                <div style="font-size:24px; font-weight:800; color:#3b5bdb;">{{ $attempt->quiz->passing_score }}%</div>
                <div style="font-size:12px; color:#64748b; font-weight:600;">Min Lulus</div>
            </div>
        </div>

        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
            <a href="{{ route('student.courses.show', $attempt->quiz->course_id) }}"
               style="display:inline-flex; align-items:center; gap:8px; background:#f1f5f9; color:#475569; font-size:14px; font-weight:700; padding:12px 24px; border-radius:12px; text-decoration:none;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Kembali ke Kursus
            </a>
            <a href="{{ route('student.quizzes.show', $attempt->quiz_id) }}"
               style="display:inline-flex; align-items:center; gap:8px; background:#3b5bdb; color:white; font-size:14px; font-weight:700; padding:12px 24px; border-radius:12px; text-decoration:none;">
                <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/></svg>
                Coba Lagi
            </a>
        </div>
    </div>

    {{-- Pembahasan Jawaban --}}
    <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.03);">
        <div style="padding:20px 28px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">Pembahasan Jawaban</h2>
            <span style="font-size:13px; color:#94a3b8;">{{ $totalQuestions }} soal</span>
        </div>
        <div style="padding:24px 28px;">
            @foreach($attempt->answers as $i => $answer)
                <div class="answer-row {{ $answer->is_correct ? 'correct' : 'incorrect' }}">
                    <div style="flex-shrink:0; margin-top:2px;">
                        @if($answer->is_correct)
                            <div style="width:24px; height:24px; background:#16a34a; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <svg width="13" height="13" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                        @else
                            <div style="width:24px; height:24px; background:#dc2626; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <svg width="13" height="13" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </div>
                        @endif
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:13px; font-weight:700; color:#94a3b8; margin-bottom:4px;">Soal {{ $i + 1 }}</div>
                        <p style="font-size:14px; font-weight:600; color:#1e293b; margin:0 0 10px 0; line-height:1.5;">
                            {{ $answer->question->question_text }}
                        </p>
                        <div style="font-size:13px; display:flex; flex-direction:column; gap:4px;">
                            <div style="display:flex; align-items:center; gap:6px;">
                                <span style="color:#64748b;">Jawabanmu:</span>
                                <span style="font-weight:600; color:{{ $answer->is_correct ? '#16a34a' : '#dc2626' }};">
                                    {{ $answer->answer_text ?: '(tidak dijawab)' }}
                                </span>
                            </div>
                            
                            {{-- 🚀 BARIS JAWABAN BENAR SUDAH DIHAPUS DARI SINI AGAR RAHASIA --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
</x-app-layout>