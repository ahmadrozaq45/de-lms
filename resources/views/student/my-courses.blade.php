<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Course') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl shadow-sm flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-bold">Gagal bergabung!</span>
                    </div>
                    <ul class="list-disc list-inside text-sm ml-7">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── 1. GABUNG KELAS DENGAN KODE (MANUAL) ── --}}
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Punya Kode Kelas Pribadi?</h3>
                    <p class="text-sm text-gray-500">Masukkan kode yang diberikan oleh gurumu untuk bergabung.</p>
                </div>
                <form action="{{ route('student.enroll') }}" method="POST" class="flex items-center gap-3 w-full md:w-auto">
                    @csrf
                    <input type="text" name="course_code" placeholder="Contoh: KLS-XYZ123" class="w-full md:w-64 border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-xl shadow-sm text-sm px-4 py-2.5" required>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-colors shadow-sm whitespace-nowrap">
                        Gabung
                    </button>
                </form>
            </div>

            {{-- ── 2. KURSUS SAYA (YANG SUDAH DISETUJUI) ── --}}
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Course Saya</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($enrolledCourses as $enrollment)
                        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col overflow-hidden relative">
                            <div class="absolute top-4 right-4 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full border border-green-200">
                                Aktif
                            </div>
                            
                            <div class="p-6 flex-1 mt-4">
                                <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1">
                                    {{ $enrollment->course->title }}
                                </h3>
                                <p class="text-xs text-gray-500 mb-4">Pengajar: <span class="font-semibold text-gray-700">{{ $enrollment->course->teacher->name ?? 'Pengajar' }}</span></p>
                                
                                @php
                                    $progressPercent = 0;
                                    foreach($courseProgressData as $cp) {
                                        if($cp['id'] == $enrollment->course_id) {
                                            $progressPercent = $cp['percent'];
                                            break;
                                        }
                                    }
                                @endphp

                                <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden mb-2">
                                    <div class="bg-blue-600 h-full transition-all" style="width: {{ $progressPercent }}%"></div>
                                </div>
                                <div class="text-xs text-right font-bold text-blue-600">{{ $progressPercent }}% Selesai</div>
                            </div>

                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                <a href="{{ route('student.courses.show', $enrollment->course_id) }}" class="block w-full text-center bg-white border border-gray-300 hover:border-blue-500 hover:text-blue-600 text-gray-700 text-sm font-bold py-2 rounded-lg transition-colors shadow-sm">
                                    Lanjut Belajar &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-gray-50/50 rounded-2xl border border-dashed border-gray-200 p-12 text-center w-full">
                            <div class="mx-auto bg-white rounded-2xl flex items-center justify-center mb-4 shadow-sm border border-gray-100" style="width: 56px; height: 56px;">
                                <svg width="28" height="28" class="text-gray-800" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                </svg>
                            </div>
                            <h4 class="text-gray-900 font-bold mb-1">Belum Ada Kursus</h4>
                            <p class="text-sm text-gray-500">Anda belum terdaftar di kursus manapun. Gabung dengan memasukkan kode atau pilih dari daftar di bawah!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ── 3. MENUNGGU PERSETUJUAN (PENDING) ── --}}
            @if($pendingEnrollments->count() > 0)
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Menunggu Persetujuan Guru</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pendingEnrollments as $pending)
                            <div class="bg-gray-50 rounded-2xl border border-gray-200 shadow-sm flex flex-col overflow-hidden relative opacity-75">
                                <div class="absolute top-4 right-4 bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200">
                                    Pending
                                </div>
                                <div class="p-6 flex-1 mt-4">
                                    <h3 class="text-base font-bold text-gray-900 mb-1 line-clamp-1">{{ $pending->course->title }}</h3>
                                    <p class="text-xs text-gray-500">Menunggu dikonfirmasi oleh {{ $pending->course->teacher->name ?? 'Pengajar' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── 4. EKSPLORASI KURSUS TERSEDIA ── --}}
            <div class="pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Eksplorasi Kursus</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($availableCourses as $course)
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                            
                            <div class="p-6 flex-1">
                                <div class="rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4" style="width: 48px; height: 48px;">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1">
                                    {{ $course->title }}
                                </h3>
                                <p class="text-xs text-gray-400 mb-3">
                                    Pengajar: <strong class="text-gray-600">{{ $course->teacher->name ?? 'Pengajar' }}</strong>
                                </p>
                                <p class="text-sm text-gray-500 mb-4 h-10 line-clamp-2">
                                    {{ $course->description ?? 'Tidak ada deskripsi yang disediakan.' }}
                                </p>
                                <div class="flex items-center gap-3 text-xs border-t border-gray-100 pt-4 mt-2">
                                    <span class="bg-blue-50 text-blue-600 font-bold px-2.5 py-1 rounded-md">
                                        {{ $course->modules->count() }} Modul
                                    </span>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                <form action="{{ route('student.enroll') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="course_code" value="{{ $course->code ?? $course->course_code }}">
                                    
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                                        Gabung Kelas
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center w-full">
                            <p class="text-sm text-gray-400">Belum ada kursus baru yang tersedia saat ini. Kamu sudah mendaftar di semua kelas!</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>