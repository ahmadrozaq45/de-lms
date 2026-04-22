<x-app-layout>
        <div class="max-w-7xl mx-auto px-6 py-8">
            <!-- Page Title -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Dashboard Guru</h2>
                <p class="text-gray-500 mt-1">Kelola course dan review hasil siswa</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $courses->count() }}</p>
                    <p class="text-sm text-gray-500">Course Aktif</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</p>
                    <p class="text-sm text-gray-500">Total Siswa</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalAssignments }}</p>
                    <p class="text-sm text-gray-500">Assignment</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingReviews ?? 1 }}</p>
                    <p class="text-sm text-gray-500">Pending Review</p>
                </div>
            </div>

            <!-- Perlu Review Section -->
            @if(isset($pendingSubmissions) && $pendingSubmissions->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Perlu Review</h3>
                    <a href="{{ route('teacher.reviews.index') }}" class="text-blue-600 text-sm hover:underline">Lihat Semua →</a>
                </div>
                @foreach($pendingSubmissions as $submission)
                <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $submission->student->name }}</p>
                        <p class="text-sm text-gray-500">Assignment ID: {{ $submission->assignment_id }}</p>
                        <p class="text-sm text-gray-400">Submitted: {{ $submission->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <a href="{{ route('teacher.reviews.index', ['submission' => $submission->id]) }}"
                       class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Review
                    </a>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Course Saya -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Course Saya</h3>
                <div class="grid grid-cols-2 gap-6">
                    @forelse($courses as $course)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="h-40 bg-gradient-to-br from-blue-400 to-blue-600 relative overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-white opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900">{{ $course->title }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ Str::limit($course->description, 80) }}</p>
                            <div class="flex items-center justify-between mt-3">
                                <span class="text-sm text-gray-400">{{ $course->modules->count() }} modul</span>
                                <a href="{{ route('teacher.courses.show', $course->id) }}" class="text-blue-600 text-sm hover:underline">Kelola →</a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 text-center py-12 text-gray-400">
                        <p>Belum ada course. <a href="{{ route('teacher.courses.index') }}" class="text-blue-600 hover:underline">Buat course pertama Anda</a>.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>