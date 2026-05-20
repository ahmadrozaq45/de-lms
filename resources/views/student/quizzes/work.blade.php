<x-app-layout>
<style>
    .q-card { background:white; border-radius:14px; border:1px solid #e2e8f0; padding:28px 32px; margin-bottom:20px; transition:box-shadow .2s; }
    .q-card:hover { box-shadow:0 4px 12px rgba(0,0,0,0.06); }
    .q-num { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#eff3ff; color:#3b5bdb; font-size:13px; font-weight:800; border-radius:8px; flex-shrink:0; }
    .opt-label { display:flex; align-items:center; gap:14px; padding:14px 18px; border:2px solid #e2e8f0; border-radius:12px; cursor:pointer; transition:all .2s; margin-bottom:8px; }
    .opt-label:hover { border-color:#3b5bdb; background:#f5f7ff; }
    input[type=radio]:checked + .opt-label { border-color:#3b5bdb; background:#eff3ff; }
    .opt-circle { width:20px; height:20px; border:2px solid #cbd5e1; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; transition:all .2s; }
    input[type=radio]:checked + .opt-label .opt-circle { border-color:#3b5bdb; background:#3b5bdb; }
    input[type=radio]:checked + .opt-label .opt-circle::after { content:''; width:8px; height:8px; background:white; border-radius:50%; }
    input[type=radio] { display:none; }
    #timer-bar { height:6px; background:#3b5bdb; border-radius:3px; transition:width 1s linear; }
    #timer-bar.warning { background:#f59e0b; }
    #timer-bar.danger { background:#ef4444; }
</style>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Timer Sticky --}}
    <div id="timer-sticky"
         style="position:sticky; top:0; z-index:40; background:white; border-radius:16px; border:1px solid #e2e8f0; padding:16px 24px; margin-bottom:24px; box-shadow:0 4px 12px rgba(0,0,0,0.08); display:flex; align-items:center; justify-content:space-between; gap:16px;">
        <div>
            <div style="font-size:12px; font-weight:600; color:#64748b; margin-bottom:2px;">{{ $quiz->title }}</div>
            <div style="font-size:11px; color:#94a3b8;">{{ $questions->count() }} soal</div>
        </div>
        <div style="text-align:center;">
            <div id="timer-display" style="font-size:28px; font-weight:800; color:#1e293b; font-variant-numeric:tabular-nums; letter-spacing:2px;">
                {{ str_pad($quiz->time_limit, 2, '0', STR_PAD_LEFT) }}:00
            </div>
            <div style="font-size:11px; color:#94a3b8; margin-top:2px;">sisa waktu</div>
        </div>
        <div id="progress-info" style="text-align:right; font-size:13px; color:#64748b; font-weight:600;">
            <span id="answered-count">0</span>/{{ $questions->count() }} dijawab
        </div>
    </div>

    {{-- Timer Bar --}}
    <div style="background:#f1f5f9; border-radius:3px; margin-bottom:28px; overflow:hidden;">
        <div id="timer-bar" style="width:100%;"></div>
    </div>

    <form id="quiz-form"
          action="{{ route('student.quizzes.submit', $attempt->id) }}"
          method="POST"
          onsubmit="return confirmSubmit()">
        @csrf

        @foreach($questions as $i => $q)
            <div class="q-card">
                <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:20px;">
                    <span class="q-num">{{ $i + 1 }}</span>
                    <p style="font-size:16px; font-weight:600; color:#1e293b; margin:0; line-height:1.6; padding-top:4px;">
                        {{ $q['question_text'] }}
                    </p>
                </div>
                <div>
                    @foreach($q['options'] as $opt)
                        <div>
                            <input type="radio" name="answers[{{ $q['id'] }}]"
                                   id="q{{ $q['id'] }}_{{ $loop->index }}"
                                   value="{{ $opt }}"
                                   onchange="updateProgress()">
                            <label class="opt-label" for="q{{ $q['id'] }}_{{ $loop->index }}">
                                <span class="opt-circle"></span>
                                <span style="font-size:15px; color:#374151;">{{ $opt }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div style="background:white; border-radius:14px; border:1px solid #e2e8f0; padding:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
            <div style="font-size:13px; color:#64748b;">
                Pastikan semua soal sudah dijawab sebelum submit.
            </div>
            <button type="submit"
                    style="display:inline-flex; align-items:center; gap:10px; background:#3b5bdb; color:white; border:none; border-radius:12px; padding:14px 32px; font-size:16px; font-weight:800; cursor:pointer; transition:background .2s;"
                    onmouseover="this.style.background='#2d45ba'" onmouseout="this.style.background='#3b5bdb'">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                Kumpulkan Jawaban
            </button>
        </div>

    </form>
</div>

<script>
const TOTAL_SECONDS = {{ $quiz->time_limit * 60 }};
const TOTAL_QUESTIONS = {{ $questions->count() }};
let remaining = TOTAL_SECONDS;
let autoSubmitted = false;

function formatTime(s) {
    const m = Math.floor(s / 60);
    const ss = s % 60;
    return String(m).padStart(2,'0') + ':' + String(ss).padStart(2,'0');
}

function updateTimer() {
    document.getElementById('timer-display').textContent = formatTime(remaining);
    const pct = (remaining / TOTAL_SECONDS) * 100;
    const bar = document.getElementById('timer-bar');
    bar.style.width = pct + '%';
    bar.className = '';
    if (pct <= 10) bar.classList.add('danger');
    else if (pct <= 25) bar.classList.add('warning');

    if (remaining <= 60) {
        document.getElementById('timer-display').style.color = '#ef4444';
    } else if (remaining <= 300) {
        document.getElementById('timer-display').style.color = '#d97706';
    }

    if (remaining <= 0 && !autoSubmitted) {
        autoSubmitted = true;
        alert('Waktu habis! Jawaban akan dikumpulkan otomatis.');
        document.getElementById('quiz-form').submit();
        return;
    }
    remaining--;
    setTimeout(updateTimer, 1000);
}

function updateProgress() {
    let answered = 0;
    @foreach($questions as $q)
        if (document.querySelector('input[name="answers[{{ $q['id'] }}]"]:checked')) answered++;
    @endforeach
    document.getElementById('answered-count').textContent = answered;
}

function confirmSubmit() {
    const answered = parseInt(document.getElementById('answered-count').textContent);
    const unanswered = TOTAL_QUESTIONS - answered;
    if (unanswered > 0) {
        return confirm(`Masih ada ${unanswered} soal yang belum dijawab. Yakin ingin mengumpulkan?`);
    }
    return confirm('Yakin ingin mengumpulkan semua jawaban?');
}

// Cegah back/refresh tidak sengaja
window.addEventListener('beforeunload', e => {
    if (!autoSubmitted) {
        e.preventDefault();
        e.returnValue = '';
    }
});

updateTimer();
updateProgress();
</script>
</x-app-layout>