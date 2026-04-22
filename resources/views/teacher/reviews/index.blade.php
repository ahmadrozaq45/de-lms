<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">LMS</span>
                    </div>
                    <div>
                        <h1 class="font-semibold text-gray-900">Learning Management System</h1>
                        <p class="text-xs text-gray-500">Guru Dashboard</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ Auth::user()->name }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </a>

            <h2 class="text-2xl font-bold text-gray-900">Review Submissions</h2>
            <p class="text-gray-500 mt-1 mb-8">Review dan validasi hasil AI scoring</p>

            <div class="grid grid-cols-5 gap-6">
                <!-- Submission List -->
                <div class="col-span-2">
                    <h3 class="font-semibold text-gray-700 mb-3">Semua Submissions</h3>
                    <div class="space-y-3">
                        @forelse($submissions as $submission)
                        <a href="?submission={{ $submission->id }}"
                           class="block border rounded-xl p-4 bg-white transition hover:border-blue-400
                                  {{ isset($selected) && $selected->id == $submission->id ? 'border-blue-500 ring-1 ring-blue-500' : 'border-gray-200' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-medium text-gray-900">{{ $submission->student->name }}</span>
                                @php
                                    $statusColor = match($submission->status) {
                                        'graded' => 'bg-green-100 text-green-700',
                                        'reviewed' => 'bg-gray-100 text-gray-600',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $statusColor }}">
                                    {{ $submission->status }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">Assignment: {{ $submission->assignment_id }}</p>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($submission->created_at)->format('Y-m-d H:i') }}</span>
                                @if($submission->score)
                                <span class="text-sm font-semibold text-blue-600">{{ $submission->score }}/100</span>
                                @endif
                            </div>
                        </a>
                        @empty
                        <p class="text-gray-400 text-sm">Belum ada submission.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Detail Panel -->
                <div class="col-span-3">
                    @if(isset($selected))
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-semibold text-gray-900 text-lg">{{ $selected->student->name }}</h3>
                        <p class="text-sm text-gray-500 mb-6">Submitted: {{ \Carbon\Carbon::parse($selected->created_at)->format('Y-m-d H:i') }}</p>

                        <!-- AI Analysis -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-800 mb-3">AI Analysis</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-sm text-gray-500">Ketepatan</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $selected->ai_accuracy ?? 90 }}%</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-sm text-gray-500">Kelengkapan</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $selected->ai_completeness ?? 80 }}%</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-sm text-gray-500">Relevansi</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $selected->ai_relevance ?? 85 }}%</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-sm text-gray-500">Confidence</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $selected->ai_confidence ?? 88 }}%</p>
                                </div>
                            </div>
                        </div>

                        <!-- Jawaban -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-800 mb-3">Jawaban:</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <span class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded mb-2 uppercase font-medium">
                                    {{ $selected->type ?? 'TEXT' }}
                                </span>
                                <p class="text-sm text-gray-700">{{ $selected->answer }}</p>
                            </div>
                        </div>

                        <!-- AI Score & Feedback -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-800 mb-3">AI Score & Feedback:</h4>
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Score:</span>
                                    <span class="text-2xl font-bold text-blue-600">{{ $selected->score ?? 85 }}/100</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $selected->ai_feedback }}</p>
                            </div>
                        </div>

                        <!-- Edit Score & Feedback -->
                        <form method="POST" action="{{ route('teacher.reviews.update', $selected->id) }}">
                            @csrf
                            @method('PATCH')

                            <h4 class="flex items-center gap-2 font-semibold text-gray-800 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Score & Feedback
                            </h4>

                            <div class="mb-4">
                                <label class="block text-sm text-gray-600 mb-1">Custom Score</label>
                                <input type="number" name="score" value="{{ $selected->score ?? 85 }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                                       min="0" max="100">
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm text-gray-600 mb-1">Custom Feedback</label>
                                <textarea name="feedback" rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 resize-y">{{ $selected->teacher_feedback ?? $selected->ai_feedback }}</textarea>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" name="action" value="approve"
                                        class="flex-1 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Setujui AI Score
                                </button>
                                <button type="submit" name="action" value="send"
                                        class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Kirim Review
                                </button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="bg-white rounded-xl border border-gray-200 h-64 flex flex-col items-center justify-center text-gray-400">
                        <div class="w-16 h-16 rounded-full border-2 border-gray-300 flex items-center justify-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <p class="text-sm">Pilih submission untuk review</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>