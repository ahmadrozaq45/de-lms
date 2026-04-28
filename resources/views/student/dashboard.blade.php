<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dasbor Belajar Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    Halo, <strong>{{ auth()->user()->name }}</strong>! Siap untuk belajar hari ini?
                </div>
            </div>

            <h3 class="text-lg font-bold mb-4">Kelas yang Tersedia</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @forelse ($courses as $course)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                        <div class="p-6 flex-grow">
                            <h4 class="text-xl font-bold mb-2">{{ $course->title ?? 'Judul Kelas' }}</h4>
                            <p class="text-gray-600 text-sm mb-4">
                                {{ Str::limit($course->description ?? 'Deskripsi kelas belum tersedia.', 100) }}
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-100 mt-auto text-right">
                            <a href="{{ route('student.courses.show', $course->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-yellow-50 p-4 rounded text-yellow-700">
                        Belum ada kelas yang tersedia saat ini.
                    </div>
                @endforelse

            </div>
            
        </div>
    </div>
</x-app-layout>