<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Kursus Saya') }}
            </h2>
            <a href="{{ route('teacher.courses.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-md shadow-indigo-100 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                </svg>
                Tambah Kursus
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                
                @forelse($courses as $course)
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between overflow-hidden">
                        
                        <div class="p-6 flex-1">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                </svg>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1">
                                {{ $course->title }}
                            </h3>
                            
                            <p class="text-sm text-gray-500 mb-4 h-10 line-clamp-2">
                                {{ $course->description ?? 'Belum ada deskripsi untuk kursus ini.' }}
                            </p>

                            <div class="flex flex-wrap items-center gap-3 text-xs border-t border-gray-100 pt-4 mt-2">
                                <span class="bg-blue-50 text-blue-600 font-bold px-2.5 py-1 rounded-md">
                                    {{ $course->modules->count() }} Modul
                                </span>
                                <span class="text-gray-400 font-medium">
                                    Dibuat: {{ \Carbon\Carbon::parse($course->created_at)->format('d M Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-row items-center justify-between gap-2">
                            <a href="{{ route('teacher.courses.show', $course->id) }}" 
                            class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                                Kelola
                            </a>
                            
                            <a href="{{ route('teacher.courses.edit', $course->id) }}" 
                            style="background-color: #f59e0b;"
                            class="flex-1 text-center hover:opacity-90 text-white text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                                Edit
                            </a>
                            
                            <form action="{{ route('teacher.courses.destroy', $course->id) }}" method="POST" 
                                class="flex-1"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini beserta seluruh modulnya?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        style="background-color: #ef4444;"
                                        class="w-full hover:opacity-90 text-white text-xs font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                                    Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                        </svg>
                        <h4 class="text-base font-bold text-gray-700 mb-1">Belum Ada Kursus</h4>
                        <p class="text-sm text-gray-400 mb-4">Anda belum membuat kursus pengajaran apa pun saat ini.</p>
                    </div>
                @endforelse

            </div> {{-- Akhir Grid --}}

        </div>
    </div>
</x-app-layout>