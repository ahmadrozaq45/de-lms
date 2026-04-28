<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Kelas: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 border-l-4 border-blue-500">
                <div class="p-6 text-gray-900">
                    <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $course->title }}</h3>
                    <p class="text-gray-600 mb-4 text-lg">{{ $course->description }}</p>
                    <p class="text-sm font-medium text-gray-500 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Pengajar: {{ $course->teacher->name ?? 'Guru belum ditugaskan' }}
                    </p>

                    @if($isEnrolled)
                        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-green-700 font-semibold flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Anda sudah terdaftar di kelas ini.
                            </p>
                            <p class="text-sm text-green-600 mt-1">Silakan akses materi pembelajaran di bagian silabus di bawah.</p>
                        </div>
                    @else
                        <form action="{{ route('student.enroll') }}" method="POST">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                            
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-bold text-white uppercase tracking-widest hover:bg-green-700 transition duration-150 ease-in-out shadow-lg">
                                Daftar Kelas Ini (Enroll)
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <h4 class="text-2xl font-bold mb-4 text-gray-800">Silabus Kelas</h4>
            <div class="space-y-4">
                @forelse ($course->modules as $module)
                    <div class="bg-white rounded-lg shadow p-5">
                        <h5 class="text-xl font-bold text-gray-800 border-b pb-2 mb-3">
                            Modul: {{ $module->title ?? 'Judul Modul' }}
                        </h5>
                        
                        <ul class="list-disc pl-5 text-gray-600 space-y-2">
                            @forelse ($module->materials as $material)
                                <li>
                                    @if($isEnrolled)
                                        <a href="#" class="text-blue-600 hover:underline font-medium">
                                            {{ $material->title ?? 'Materi Belajar' }}
                                        </a>
                                    @else
                                        <span class="text-gray-500">{{ $material->title ?? 'Materi Belajar' }}</span>
                                        <span class="text-xs ml-2 px-2 py-1 bg-gray-100 text-gray-400 rounded border">Terkunci (Enroll dahulu)</span>
                                    @endif
                                </li>
                            @empty
                                <li class="text-sm italic text-gray-400 list-none">Belum ada materi di modul ini.</li>
                            @endforelse
                        </ul>
                    </div>
                @empty
                    <div class="bg-yellow-50 p-4 rounded-md text-yellow-700">
                        Belum ada modul atau materi untuk kelas ini.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>