<x-app-layout>
<style>
.tab-btn { padding:12px 20px; font-size:14px; font-weight:500; cursor:pointer; border:none; background:none; color:#6b7280; border-bottom:2px solid transparent; transition:all 0.15s; }
.tab-btn.active { color:#3b5bdb; border-bottom-color:#3b5bdb; }
.tab-content { display:none; } .tab-content.active { display:block; }
.assignment-card { background:white; border:1px solid #f0f0f0; border-radius:10px; padding:20px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:flex-start; }
</style>

<div>
    <a href="{{ route('student.dashboard') }}" style="display:inline-flex; align-items:center; gap:6px; color:#6b7280; font-size:14px; text-decoration:none; margin-bottom:20px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali ke Dashboard
    </a>

    <!-- Hero Banner -->
    <div style="background:white; border-radius:12px; overflow:hidden; border:1px solid #f0f0f0; margin-bottom:20px;">
        <div style="height:200px; background:linear-gradient(135deg,#1a2b4a,#2d4a8a); display:flex; align-items:center; justify-content:center;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><polyline points="8 21 12 17 16 21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        </div>
        <div style="padding:24px;">
            <h1 style="font-size:22px; font-weight:700; color:#111827; margin:0 0 6px 0;">{{ $course->title }}</h1>
            <p style="font-size:14px; color:#6b7280; margin:0 0 14px 0;">{{ $course->description }}</p>
            <div style="display:flex; gap:20px; font-size:13px; color:#6b7280;">
                <span>Instruktur: <strong style="color:#374151;">{{ $course->teacher->name ?? '-' }}</strong></span>
                <span>{{ $course->modules->count() }} modul</span>
                <span>Progress: <strong style="color:#3b5bdb;">65%</strong></span>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div style="background:white; border-radius:12px; border:1px solid #f0f0f0; overflow:hidden;">
        <div style="display:flex; border-bottom:1px solid #f0f0f0; padding:0 20px;">
            <button class="tab-btn active" onclick="switchTab(event,'materi')">Materi</button>
            <button class="tab-btn" onclick="switchTab(event,'diskusi')">Diskusi</button>
            <button class="tab-btn" onclick="switchTab(event,'tugas')">Tugas</button>
            <button class="tab-btn" onclick="switchTab(event,'ujian')">Ujian</button>
        </div>

        <!-- Materi Tab -->
        <div id="tab-materi" class="tab-content active" style="padding:20px;">
            @forelse ($course->modules as $module)
            <div style="margin-bottom:20px;">
                <h3 style="font-size:15px; font-weight:600; color:#374151; margin-bottom:10px;">{{ $module->title }}</h3>
                @foreach ($module->materials as $material)
                <div style="display:flex; align-items:center; gap:12px; padding:14px; border:1px solid #f0f0f0; border-radius:8px; margin-bottom:8px;">
                    <div style="width:32px; height:32px; background:#eff6ff; border-radius:6px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:14px; font-weight:500; color:#111827;">{{ $material->title }}</div>
                        <div style="font-size:12px; color:#9ca3af;">{{ Str::limit($material->content ?? '', 80) }}</div>
                    </div>
                    <div style="margin-left:auto;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>
                @endforeach
            </div>
            @empty
            <p style="color:#9ca3af; font-size:14px; text-align:center; padding:20px 0;">Belum ada materi tersedia.</p>
            @endforelse
        </div>

        <!-- Diskusi Tab -->
        <div id="tab-diskusi" class="tab-content" style="padding:20px;">
            <p style="color:#9ca3af; font-size:14px;">Forum diskusi akan segera hadir.</p>
        </div>

        <!-- Tugas Tab -->
        <div id="tab-tugas" class="tab-content" style="padding:20px;">
            @forelse ($course->assignments ?? [] as $assignment)
            <div class="assignment-card">
                <div style="flex:1;">
                    <h4 style="font-size:15px; font-weight:600; color:#111827; margin:0 0 6px 0;">{{ $assignment->title }}</h4>
                    <p style="font-size:13px; color:#6b7280; margin:0 0 8px 0;">{{ $assignment->description }}</p>
                    <span style="font-size:12px; color:#374151;">Max Score: {{ $assignment->max_score ?? 100 }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:12px; flex-shrink:0; margin-left:16px;">
                    @if(isset($assignment->due_date))
                    <span style="background:#fffbeb; color:#92400e; font-size:11px; font-weight:500; padding:4px 10px; border-radius:6px; border:1px solid #fcd34d;">Due: {{ $assignment->due_date }}</span>
                    @endif
                    <a href="#" style="display:inline-flex; align-items:center; gap:6px; background:#3b5bdb; color:white; font-size:13px; font-weight:500; padding:8px 16px; border-radius:8px; text-decoration:none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Kerjakan
                    </a>
                </div>
            </div>
            @empty
            <p style="color:#9ca3af; font-size:14px;">Belum ada tugas tersedia.</p>
            @endforelse
        </div>

        <!-- Ujian Tab -->
        <div id="tab-ujian" class="tab-content" style="padding:20px;">
            <p style="color:#9ca3af; font-size:14px;">Belum ada ujian tersedia.</p>
        </div>
    </div>

    @if(!$isEnrolled)
    <div style="position:fixed; bottom:24px; right:24px;">
        <form action="{{ route('student.enroll') }}" method="POST">
            @csrf
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <button type="submit" style="background:#3b5bdb; color:white; border:none; border-radius:10px; padding:14px 28px; font-size:14px; font-weight:600; cursor:pointer; box-shadow:0 4px 15px rgba(59,91,219,0.4);">
                + Daftar ke Kursus Ini
            </button>
        </form>
    </div>
    @endif
</div>

<script>
function switchTab(e, tabId) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    e.target.classList.add('active');
    document.getElementById('tab-'+tabId).classList.add('active');
}
</script>
</x-app-layout>
