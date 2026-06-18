<x-app-layout>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <a href="{{ route('student.courses.show', $assignment->course_id) }}"
           class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Assignment Info --}}
        <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; padding:32px; margin-bottom:24px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.03);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
                <h1 style="font-size:26px; font-weight:800; color:#1e293b; margin:0;">Tugas: {{ $assignment->title }}</h1>
                <span style="background:{{ \Carbon\Carbon::parse($assignment->due_date)->isPast() ? '#fee2e2' : '#fffbeb' }}; color:{{ \Carbon\Carbon::parse($assignment->due_date)->isPast() ? '#dc2626' : '#d97706' }}; font-size:13px; font-weight:700; padding:6px 16px; border-radius:8px; border:1px solid {{ \Carbon\Carbon::parse($assignment->due_date)->isPast() ? '#fecaca' : '#fef3c7' }}; white-space:nowrap;">
                    Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y, H:i') }}
                </span>
            </div>

            <p style="font-size:15px; color:#475569; line-height:1.7; margin:0 0 20px 0;">
                {{ $assignment->instructions }}
            </p>

            @if($assignment->module)
            <div style="font-size:13px; color:#94a3b8; background:#f8fafc; padding:10px 16px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
                <svg width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Modul: <strong style="color:#475569;">{{ $assignment->module->title }}</strong>
            </div>
            @endif

            @if($assignment->max_score)
            <div style="font-size:13px; color:#94a3b8; background:#f8fafc; padding:10px 16px; border-radius:8px; display:inline-flex; align-items:center; gap:6px; margin-left:8px;">
                <svg width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                Skor Maksimal: <strong style="color:#475569;">{{ $assignment->max_score }}</strong>
            </div>
            @endif

            {{-- Rubrik Penilaian --}}
            @if($assignment->rubric ?? false)
            <div style="margin-top:20px; padding:20px; background:#f0f4ff; border-radius:12px; border:1px solid #e0e7ff;">
                <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 12px 0;">Rubrik Penilaian:</h3>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    @foreach(explode("\n", $assignment->rubric) as $item)
                        @if(trim($item))
                        <div style="display:flex; align-items:center; gap:8px; font-size:14px; color:#374151;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ trim($item) }}
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Check if already submitted --}}
        @if($submission)
            <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; padding:32px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.03);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
                    <div style="width:44px; height:44px; background:#dcfce7; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                        <svg width="22" height="22" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <h2 style="font-size:18px; font-weight:700; color:#1e293b; margin:0;">Tugas Sudah Dikumpulkan</h2>
                        <p style="font-size:13px; color:#64748b; margin:0;">Dikumpulkan {{ $submission->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <span style="margin-left:auto; background:{{ $submission->status === 'graded' ? '#dcfce7' : ($submission->status === 'reviewed' ? '#dbeafe' : '#fef9c3') }}; color:{{ $submission->status === 'graded' ? '#16a34a' : ($submission->status === 'reviewed' ? '#1d4ed8' : '#d97706') }}; font-size:12px; font-weight:700; padding:5px 14px; border-radius:8px;">
                        {{ ucfirst($submission->status) }}
                    </span>
                </div>

                @if($submission->score)
                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:20px; margin-bottom:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                        <div>
                            <div style="font-size:13px; color:#16a34a; font-weight:600; margin-bottom:4px;">Nilai Anda</div>
                            <div style="font-size:36px; font-weight:800; color:#15803d;">{{ $submission->score }}<span style="font-size:16px; color:#22c55e;">/{{ $assignment->max_score ?? 100 }}</span></div>
                        </div>
                        @if($submission->ai_accuracy || $submission->ai_completeness)
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            @if($submission->ai_accuracy)
                            <div style="background:white; border-radius:8px; padding:10px 16px; text-align:center; border:1px solid #bbf7d0;">
                                <div style="font-size:11px; color:#64748b; margin-bottom:2px;">Ketepatan</div>
                                <div style="font-size:18px; font-weight:700; color:#1e293b;">{{ $submission->ai_accuracy }}%</div>
                            </div>
                            @endif
                            @if($submission->ai_completeness)
                            <div style="background:white; border-radius:8px; padding:10px 16px; text-align:center; border:1px solid #bbf7d0;">
                                <div style="font-size:11px; color:#64748b; margin-bottom:2px;">Kelengkapan</div>
                                <div style="font-size:18px; font-weight:700; color:#1e293b;">{{ $submission->ai_completeness }}%</div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($submission->ai_feedback || $submission->teacher_feedback)
                <div style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                    @if($submission->ai_feedback)
                    <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0;">
                        <div style="font-size:12px; font-weight:700; color:#6366f1; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                            <svg width="14" height="14" fill="#6366f1" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm1 15h-2v-6h2zm0-8h-2V7h2z"/></svg>
                            Feedback AI
                        </div>
                        <p style="font-size:14px; color:#374151; margin:0; line-height:1.6;">{{ $submission->ai_feedback }}</p>
                    </div>
                    @endif
                    @if($submission->teacher_feedback)
                    <div style="padding:16px 20px;">
                        <div style="font-size:12px; font-weight:700; color:#0369a1; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                            <svg width="14" height="14" fill="none" stroke="#0369a1" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            Feedback Guru
                        </div>
                        <p style="font-size:14px; color:#374151; margin:0; line-height:1.6;">{{ $submission->teacher_feedback }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

        @else
        {{-- Submit Form --}}
        <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; padding:32px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.03);">
            <h2 style="font-size:18px; font-weight:700; color:#1e293b; margin:0 0 24px 0;">Upload Jawaban Anda</h2>

            <form action="{{ route('student.assignments.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Hanya muncul jika guru memilih TEXT --}}
                @if($assignment->submission_type == 'text')
                    <div style="margin-bottom:20px;">
                        <label style="display:block; font-size:14px; font-weight:700; color:#374151; margin-bottom:8px;">Jawaban Text <span style="color:#ef4444;">*</span></label>
                        <textarea name="answer" rows="8" style="width:100%; border:1px solid #e2e8f0; border-radius:12px; padding:16px; font-size:15px; color:#1e293b; resize:vertical; outline:none; transition:border-color 0.2s; box-sizing:border-box;" placeholder="Tulis jawaban Anda di sini..." onfocus="this.style.borderColor='#3b5bdb'" onblur="this.style.borderColor='#e2e8f0'">{{ old('answer') }}</textarea>
                    </div>
                @endif

                {{-- Hanya muncul jika guru memilih FILE --}}
                @if($assignment->submission_type == 'file')
                    <div style="margin-bottom:20px;">
                        <label style="display:block; font-size:14px; font-weight:700; color:#374151; margin-bottom:8px;">Lampiran File <span style="color:#ef4444;">*</span></label>
                        <div style="border:2px dashed #e2e8f0; border-radius:12px; padding:28px; text-align:center; background:#f8fafc; transition:border-color 0.2s;" 
                             ondragover="this.style.borderColor='#3b5bdb'" ondragleave="this.style.borderColor='#e2e8f0'">
                            <svg width="36" height="36" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 10px; display:block;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <p style="color:#64748b; font-size:14px; margin:0 0 12px 0;">Upload file pendukung (PDF, DOC, ZIP, gambar, video, dll)</p>
                            <input type="file" name="file_path" style="font-size:14px; color:#64748b;">
                        </div>
                    </div>
                @endif

                {{-- AI Info Box --}}
                <div style="margin-top:20px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:20px;">
                    <h4 style="font-size:14px; font-weight:700; color:#1d4ed8; margin:0 0 10px 0;">Proses AI Setelah Submit:</h4>
                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#374151;">
                            <svg width="16" height="16" fill="none" stroke="#3b82f6" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Sistem akan mengkonversi input Anda ke format text
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#374151;">
                            <svg width="16" height="16" fill="none" stroke="#3b82f6" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            AI akan mengevaluasi jawaban berdasarkan rubrik
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#374151;">
                            <svg width="16" height="16" fill="none" stroke="#3b82f6" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Generate nilai dan feedback otomatis
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; font-size:13px; color:#374151;">
                            <svg width="16" height="16" fill="none" stroke="#3b82f6" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Hasil dapat direview oleh guru
                        </div>
                    </div>
                </div>

                <button type="submit" style="display:flex; align-items:center; justify-content:center; gap:8px; width:100%; margin-top:20px; background:#3b5bdb; color:white; border:none; border-radius:12px; padding:16px; font-size:16px; font-weight:700; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#2d45ba'" onmouseout="this.style.background='#3b5bdb'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Submit Jawaban
                </button>
            </form>
        </div>
        @endif

    </div>


</x-app-layout>