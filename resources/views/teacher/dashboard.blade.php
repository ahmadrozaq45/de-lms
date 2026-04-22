<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Guru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Selamat Datang --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    Selamat datang, <strong>{{ auth()->user()->name }}</strong>! Anda login sebagai <strong>{{ ucfirst(auth()->user()->role) }}</strong>.
                </div>
            </div>

            {{-- Statistik --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 uppercase tracking-wide">Total Kursus</div>
                    <div class="mt-1 text-3xl font-bold text-indigo-600">{{ $courses->count() }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 uppercase tracking-wide">Total Siswa</div>
                    <div class="mt-1 text-3xl font-bold text-green-600">{{ $totalStudents }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500 uppercase tracking-wide">Pending Review</div>
                    <div class="mt-1 text-3xl font-bold text-yellow-600">{{ $pendingReviews }}</div>
                </div>
            </div>

            {{-- Pending Submissions --}}
            @if($pendingSubmissions->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tugas Menunggu Review</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tugas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingSubmissions as $submission)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $submission->student->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $submission->assignment->title ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $submission->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('teacher.reviews.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Review</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Daftar Kursus Singkat --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Kursus Saya</h3>
                        <a href="{{ route('teacher.courses.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua →</a>
                    </div>
                    @forelse($courses as $course)
                        <div class="flex justify-between items-center py-3 border-b last:border-0">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                <div class="text-xs text-gray-500">{{ $course->modules->count() }} modul</div>
                            </div>
                            <a href="{{ route('teacher.courses.show', $course->id) }}" class="text-xs text-indigo-600 hover:underline">Kelola</a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada kursus. <a href="{{ route('teacher.courses.create') }}" class="text-indigo-600 underline">Buat sekarang</a>.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>