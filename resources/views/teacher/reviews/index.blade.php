<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if(isset($filterCourse) && $filterCourse)
        <a href="{{ route('teacher.courses.students', $filterCourse) }}"
           class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Daftar Siswa
        </a>
        @else
        <a href="{{ route('teacher.dashboard') }}"
           class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Dashboard
        </a>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div style="margin-bottom:24px;">
            <h1 style="font-size:24px; font-weight:800; color:#1e293b; margin:0 0 4px 0;">Review Submissions</h1>
            <p style="font-size:14px; color:#64748b; margin:0;">
                @if(isset($filterStudent) && $filterStudent && $submissions->first())
                    Menampilkan tugas: <strong style="color:#1e293b;">{{ $submissions->first()->student->name ?? '' }}</strong>
                    &nbsp;·&nbsp; {{ $submissions->count() }} pengumpulan
                @else
                    Review dan validasi hasil AI scoring
                @endif
            </p>
        </div>

        <div style="display:grid; grid-template-columns:340px 1fr; gap:24px; align-items:start;">

            {{-- Left: Submission List --}}
            <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.03);">
                <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; background:#f8fafc;">
                    <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0;">Semua Submissions</h3>
                </div>
                <div style="max-height:600px; overflow-y:auto;">
                    @forelse($submissions as $submission)
                        <a href="{{ route('teacher.reviews.index', ['submission' => $submission->id]) }}"
                           style="display:block; padding:16px 20px; border-bottom:1px solid #f1f5f9; text-decoration:none; transition:background 0.15s;
                                  {{ isset($selected) && $selected->id === $submission->id ? 'background:#eff6ff; border-left:4px solid #3b5bdb;' : 'border-left:4px solid transparent;' }}">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                                <span style="font-size:14px; font-weight:700; color:#1e293b;">{{ $submission->student->name ?? '-' }}</span>
                                <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:6px;
                                    background:{{ $submission->status === 'graded' ? '#dcfce7' : ($submission->status === 'reviewed' ? '#dbeafe' : '#fef9c3') }};
                                    color:{{ $submission->status === 'graded' ? '#16a34a' : ($submission->status === 'reviewed' ? '#1d4ed8' : '#a16207') }};">
                                    {{ $submission->status }}
                                </span>
                            </div>
                            <div style="font-size:12px; color:#94a3b8; margin-bottom:4px;">
                                Assignment: {{ $submission->assignment->title ?? 'a'.$submission->assignment_id }}
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <span style="font-size:12px; color:#94a3b8;">{{ $submission->created_at->format('Y-m-d H:i') }}</span>
                                @if($submission->score)
                                    <span style="font-size:13px; font-weight:700; color:#3b5bdb;">{{ $submission->score }}/100</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div style="padding:40px; text-align:center; color:#94a3b8; font-size:14px; font-style:italic;">
                            Belum ada submission.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Right: Detail & Review --}}
            <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.03);">
                @if($selected)
                    <div style="padding:28px 32px;">
                        <div style="margin-bottom:24px;">
                            <h2 style="font-size:20px; font-weight:800; color:#1e293b; margin:0 0 4px 0;">{{ $selected->student->name ?? '-' }}</h2>
                            <p style="font-size:13px; color:#64748b; margin:0;">Submitted: {{ $selected->created_at->format('Y-m-d H:i') }}</p>
                        </div>

                        @if($selected->ai_accuracy || $selected->ai_completeness || $selected->ai_relevance || $selected->ai_confidence)
                        <div style="margin-bottom:24px; padding:20px; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">
                            <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 16px 0;">AI Analysis</h3>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                                @if($selected->ai_accuracy)
                                <div style="background:white; border-radius:10px; padding:14px 18px; border:1px solid #e2e8f0;">
                                    <div style="font-size:12px; color:#64748b; margin-bottom:4px;">Ketepatan</div>
                                    <div style="font-size:24px; font-weight:800; color:#1e293b;">{{ $selected->ai_accuracy }}%</div>
                                </div>
                                @endif
                                @if($selected->ai_completeness)
                                <div style="background:white; border-radius:10px; padding:14px 18px; border:1px solid #e2e8f0;">
                                    <div style="font-size:12px; color:#64748b; margin-bottom:4px;">Kelengkapan</div>
                                    <div style="font-size:24px; font-weight:800; color:#1e293b;">{{ $selected->ai_completeness }}%</div>
                                </div>
                                @endif
                                @if($selected->ai_relevance)
                                <div style="background:white; border-radius:10px; padding:14px 18px; border:1px solid #e2e8f0;">
                                    <div style="font-size:12px; color:#64748b; margin-bottom:4px;">Relevansi</div>
                                    <div style="font-size:24px; font-weight:800; color:#1e293b;">{{ $selected->ai_relevance }}%</div>
                                </div>
                                @endif
                                @if($selected->ai_confidence)
                                <div style="background:white; border-radius:10px; padding:14px 18px; border:1px solid #e2e8f0;">
                                    <div style="font-size:12px; color:#64748b; margin-bottom:4px;">Confidence</div>
                                    <div style="font-size:24px; font-weight:800; color:#1e293b;">{{ $selected->ai_confidence }}%</div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div style="margin-bottom:24px;">
                            <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 12px 0;">Jawaban:</h3>
                            <div style="border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px; background:#fafafa;">
                                @if($selected->answer)
                                    <div style="margin-bottom:8px;">
                                        <span style="font-size:11px; font-weight:700; background:#eff6ff; color:#3b82f6; padding:2px 8px; border-radius:4px;">TEXT</span>
                                    </div>
                                    <p style="font-size:14px; color:#374151; line-height:1.7; margin:0;">{{ $selected->answer }}</p>
                                @elseif($selected->file_path)
                                    <div style="margin-bottom:8px;">
                                        <span style="font-size:11px; font-weight:700; background:#f5f3ff; color:#7c3aed; padding:2px 8px; border-radius:4px;">FILE</span>
                                    </div>
                                    <a href="{{ Storage::url($selected->file_path) }}" target="_blank"
                                       style="display:inline-flex; align-items:center; gap:6px; font-size:14px; color:#3b5bdb; font-weight:600; text-decoration:none;">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Download File Jawaban
                                    </a>
                                @else
                                    <p style="color:#94a3b8; font-style:italic; font-size:14px; margin:0;">Tidak ada jawaban tersedia.</p>
                                @endif
                            </div>
                        </div>

                        @if($selected->score || $selected->ai_feedback)
                        <div style="margin-bottom:24px;">
                            <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0 0 12px 0;">AI Score & Feedback:</h3>
                            <div style="border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px; background:#fafafe;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                    <span style="font-size:13px; color:#64748b; font-weight:600;">Score:</span>
                                    <span style="font-size:24px; font-weight:800; color:#3b5bdb;">{{ $selected->score ?? '–' }}/100</span>
                                </div>
                                @if($selected->ai_feedback)
                                    <p style="font-size:14px; color:#475569; line-height:1.6; margin:0; padding-top:8px; border-top:1px solid #e2e8f0;">{{ $selected->ai_feedback }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        <form action="{{ route('teacher.reviews.update', $selected->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                                <svg width="16" height="16" fill="none" stroke="#374151" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span style="font-size:15px; font-weight:700; color:#1e293b;">Edit Score & Feedback</span>
                            </div>

                            <div style="margin-bottom:16px;">
                                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Custom Score</label>
                                <input type="number" name="score" min="0" max="100"
                                       value="{{ old('score', $selected->score) }}"
                                       style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; font-size:14px; color:#1e293b; outline:none; box-sizing:border-box;"
                                       required>
                                @error('score')<p style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
                            </div>

                            <div style="margin-bottom:24px;">
                                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Custom Feedback</label>
                                <textarea name="feedback" rows="4"
                                          style="width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; font-size:14px; color:#1e293b; outline:none; resize:vertical; box-sizing:border-box;"
                                          >{{ old('feedback', $selected->teacher_feedback) }}</textarea>
                            </div>

                            <div style="display:flex; gap:12px;">
                                <button type="submit" name="action" value="approve"
                                        style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#16a34a; color:white; border:none; border-radius:10px; padding:14px 20px; font-size:14px; font-weight:700; cursor:pointer;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                    Setujui AI Score
                                </button>
                                <button type="submit" name="action" value="send"
                                        style="flex:1; display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#3b5bdb; color:white; border:none; border-radius:10px; padding:14px 20px; font-size:14px; font-weight:700; cursor:pointer;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    Kirim Review
                                </button>
                            </div>
                        </form>

                    </div>
                @else
                    <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:80px 40px; text-align:center;">
                        <div style="width:60px; height:60px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:16px;">
                            <svg width="28" height="28" fill="none" stroke="#94a3b8" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </div>
                        <p style="color:#94a3b8; font-size:15px; font-weight:500; margin:0;">Pilih submission untuk review</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>