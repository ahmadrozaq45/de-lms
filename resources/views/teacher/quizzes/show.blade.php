<x-app-layout>
<style>
    .tab-btn { padding:12px 22px; font-size:14px; font-weight:600; cursor:pointer; border:none; background:none; color:#64748b; border-bottom:3px solid transparent; transition:all .2s; }
    .tab-btn.active { color:#3b5bdb; border-bottom-color:#3b5bdb; }
    .tab-content { display:none; } .tab-content.active { display:block; }
    .form-input { width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; font-size:14px; color:#1e293b; outline:none; transition:border-color .2s; box-sizing:border-box; }
    .form-input:focus { border-color:#3b5bdb; }
    .form-label { display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px; }
    .q-card { background:white; border:1px solid #e2e8f0; border-radius:12px; padding:20px 24px; margin-bottom:12px; }
    .opt-row { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
    .btn-sm { display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:700; padding:5px 12px; border-radius:8px; border:none; cursor:pointer; }
</style>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <a href="{{ route('teacher.courses.show', $quiz->course_id) }}"
       class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke {{ $quiz->course->title }}
    </a>

    @if(session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:14px 20px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
            <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            <span style="font-size:14px; font-weight:600; color:#15803d;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; padding:28px 32px; margin-bottom:24px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.03);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                    <span style="font-size:11px; font-weight:700; background:#eff3ff; color:#3b5bdb; padding:2px 10px; border-radius:4px; border:1px solid #c7d2fe;">QUIZ</span>
                    <h1 style="font-size:24px; font-weight:800; color:#1e293b; margin:0;">{{ $quiz->title }}</h1>
                </div>
                @if($quiz->description)
                    <p style="font-size:14px; color:#64748b; margin:0 0 12px 0; line-height:1.5;">{{ $quiz->description }}</p>
                @endif
                <div style="display:flex; flex-wrap:wrap; gap:16px; font-size:13px; color:#64748b;">
                    <span>⏱ {{ $quiz->time_limit }} menit</span>
                    <span>📋 {{ $quiz->questions->count() }} soal</span>
                    <span>✅ Nilai lulus: {{ $quiz->passing_score }}%</span>
                    <span>👥 {{ $quiz->attempts->count() }} attempt</span>
                </div>
            </div>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('teacher.quizzes.results', $quiz->id) }}"
                   style="display:inline-flex; align-items:center; gap:6px; background:#dcfce7; color:#16a34a; font-size:13px; font-weight:700; padding:8px 16px; border-radius:10px; text-decoration:none;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Lihat Hasil
                </a>
                <form action="{{ route('teacher.quizzes.destroy', $quiz->id) }}" method="POST"
                      onsubmit="return confirm('Hapus quiz ini? Semua soal dan hasil akan terhapus.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="display:inline-flex; align-items:center; gap:6px; background:#fee2e2; color:#dc2626; font-size:13px; font-weight:700; padding:8px 16px; border-radius:10px; border:none; cursor:pointer;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Quiz
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">
        <div style="display:flex; border-bottom:1px solid #e2e8f0; background:#f8fafc; padding:0 16px;">
            <button class="tab-btn active" onclick="switchTab(event,'soal')">Daftar Soal ({{ $quiz->questions->count() }})</button>
            <button class="tab-btn" onclick="switchTab(event,'tambah')">+ Tambah Soal</button>
        </div>

        {{-- TAB: Daftar Soal --}}
        <div id="tab-soal" class="tab-content active" style="padding:32px;">
            @forelse($quiz->questions as $i => $question)
                <div class="q-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-bottom:14px;">
                        <div>
                            <span style="font-size:12px; font-weight:700; color:#94a3b8; display:block; margin-bottom:4px;">Soal {{ $i + 1 }}</span>
                            <p style="font-size:15px; font-weight:600; color:#1e293b; margin:0; line-height:1.5;">{{ $question->question_text }}</p>
                        </div>
                        <form action="{{ route('teacher.quizzes.questions.destroy', [$quiz->id, $question->id]) }}" method="POST"
                              onsubmit="return confirm('Hapus soal ini?')" style="flex-shrink:0;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm" style="background:#fee2e2; color:#dc2626;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:6px;">
                        @foreach($question->options as $opt)
                            <div style="display:flex; align-items:center; gap:10px; padding:8px 14px; border-radius:8px;
                                        background:{{ strtolower($opt) === strtolower($question->correct_answer) ? '#f0fdf4' : '#f8fafc' }};
                                        border:1px solid {{ strtolower($opt) === strtolower($question->correct_answer) ? '#bbf7d0' : '#e2e8f0' }};">
                                @if(strtolower($opt) === strtolower($question->correct_answer))
                                    <svg width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span style="font-size:13px; font-weight:600; color:#15803d;">{{ $opt }}</span>
                                    <span style="font-size:11px; background:#dcfce7; color:#15803d; padding:1px 8px; border-radius:10px; margin-left:auto; font-weight:700;">BENAR</span>
                                @else
                                    <svg width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                    <span style="font-size:13px; color:#475569;">{{ $opt }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:48px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 16px;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p style="color:#64748b; font-weight:500; margin:0 0 8px;">Belum ada soal. Klik tab <strong>"+ Tambah Soal"</strong> untuk mulai.</p>
                </div>
            @endforelse
        </div>

        {{-- TAB: Tambah Soal --}}
        <div id="tab-tambah" class="tab-content" style="padding:32px;">
            @if($errors->any())
                <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:16px; margin-bottom:24px;">
                    <ul style="list-style:disc; padding-left:20px; margin:0; font-size:14px; color:#dc2626;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('teacher.quizzes.questions.store', $quiz->id) }}" method="POST" id="form-tambah-soal">
                @csrf
                <div style="display:flex; flex-direction:column; gap:24px;">

                    <div>
                        <label class="form-label">Teks Soal <span style="color:#ef4444;">*</span></label>
                        <textarea name="question_text" class="form-input" rows="3"
                                  placeholder="Tuliskan pertanyaan di sini..." required>{{ old('question_text') }}</textarea>
                    </div>

                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <label class="form-label" style="margin:0;">Pilihan Jawaban <span style="color:#ef4444;">*</span></label>
                            <button type="button" onclick="addOption()"
                                    style="font-size:12px; font-weight:700; color:#3b5bdb; background:#eff3ff; border:none; padding:5px 12px; border-radius:8px; cursor:pointer;">
                                + Tambah Pilihan
                            </button>
                        </div>
                        <div id="options-container" style="display:flex; flex-direction:column; gap:8px;">
                            @php $oldOpts = old('options', ['','','','']); @endphp
                            @foreach($oldOpts as $idx => $opt)
                                <div class="opt-row" id="opt-row-{{ $idx }}">
                                    <span style="font-size:13px; font-weight:700; color:#94a3b8; width:22px; flex-shrink:0;">{{ chr(65+$idx) }}.</span>
                                    <input type="text" name="options[]" class="form-input" value="{{ $opt }}"
                                           placeholder="Pilihan {{ chr(65+$idx) }}" required>
                                    @if($idx >= 2)
                                        <button type="button" onclick="removeOption('opt-row-{{ $idx }}')"
                                                style="background:#fee2e2; color:#dc2626; border:none; border-radius:8px; padding:8px; cursor:pointer; flex-shrink:0;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p style="font-size:12px; color:#94a3b8; margin-top:8px;">Minimal 2 pilihan, maksimal 6.</p>
                    </div>

                    <div>
                        <label class="form-label">Jawaban Benar <span style="color:#ef4444;">*</span></label>
                        <select name="correct_answer" id="correct-answer-select" class="form-input" required>
                            <option value="">-- Pilih jawaban benar --</option>
                            @foreach(old('options', ['','','','']) as $opt)
                                @if($opt)
                                    <option value="{{ $opt }}" @selected(old('correct_answer') === $opt)>{{ $opt }}</option>
                                @endif
                            @endforeach
                        </select>
                        <p style="font-size:12px; color:#94a3b8; margin-top:6px;">Isi pilihan jawaban dulu, lalu pilih yang benar.</p>
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:12px; padding-top:8px; border-top:1px solid #f1f5f9;">
                        <button type="reset" onclick="resetForm()"
                                style="display:inline-flex; align-items:center; gap:8px; background:#f1f5f9; color:#475569; font-size:14px; font-weight:600; padding:10px 24px; border-radius:12px; border:none; cursor:pointer;">
                            Reset
                        </button>
                        <button type="submit"
                                style="display:inline-flex; align-items:center; gap:8px; background:#3b5bdb; color:white; font-size:14px; font-weight:700; padding:10px 24px; border-radius:12px; border:none; cursor:pointer;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Simpan Soal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let optCount = {{ count(old('options', ['','','',''])) }};

function switchTab(e, tabId) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    e.target.classList.add('active');
    document.getElementById('tab-' + tabId).classList.add('active');
}

// Buka tab tambah soal otomatis kalau ada error
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.tab-btn')[1].click();
    });
@endif

function addOption() {
    const container = document.getElementById('options-container');
    if (container.children.length >= 6) { alert('Maksimal 6 pilihan.'); return; }
    const idx = optCount++;
    const letter = String.fromCharCode(65 + container.children.length);
    const div = document.createElement('div');
    div.className = 'opt-row';
    div.id = 'opt-row-' + idx;
    div.innerHTML = `
        <span style="font-size:13px;font-weight:700;color:#94a3b8;width:22px;flex-shrink:0;">${letter}.</span>
        <input type="text" name="options[]" class="form-input" placeholder="Pilihan ${letter}" required
               oninput="syncCorrectAnswer()">
        <button type="button" onclick="removeOption('opt-row-${idx}')"
                style="background:#fee2e2;color:#dc2626;border:none;border-radius:8px;padding:8px;cursor:pointer;flex-shrink:0;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>`;
    container.appendChild(div);
    syncCorrectAnswer();
}

function removeOption(rowId) {
    const el = document.getElementById(rowId);
    if (document.getElementById('options-container').children.length <= 2) { alert('Minimal 2 pilihan.'); return; }
    el.remove();
    reindexOptions();
    syncCorrectAnswer();
}

function reindexOptions() {
    const rows = document.querySelectorAll('.opt-row');
    rows.forEach((row, i) => {
        const letter = String.fromCharCode(65 + i);
        row.querySelector('span').textContent = letter + '.';
        row.querySelector('input').placeholder = 'Pilihan ' + letter;
    });
}

function syncCorrectAnswer() {
    const sel = document.getElementById('correct-answer-select');
    const prev = sel.value;
    const inputs = document.querySelectorAll('#options-container input[name="options[]"]');
    sel.innerHTML = '<option value="">-- Pilih jawaban benar --</option>';
    inputs.forEach(inp => {
        if (inp.value.trim()) {
            const opt = document.createElement('option');
            opt.value = inp.value;
            opt.textContent = inp.value;
            if (inp.value === prev) opt.selected = true;
            sel.appendChild(opt);
        }
    });
}

// Sync saat user mengetik di input pilihan
document.addEventListener('input', e => {
    if (e.target.closest('#options-container')) syncCorrectAnswer();
});

function resetForm() {
    document.getElementById('form-tambah-soal').reset();
    syncCorrectAnswer();
}
</script>
</x-app-layout>