<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('student.courses.show', $material->module->course_id) }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Detail Kelas
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 border border-gray-100">
                <h1 class="text-4xl font-extrabold text-gray-900 mb-6">{{ $material->title }}</h1>
                
                <div class="prose max-w-none text-gray-700 text-lg leading-relaxed">
                    {!! $material->content ?? 'Isi materi kosong.' !!}
                </div>

                <div class="mt-12 pt-8 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pastikan kamu sudah memahami materi ini.</span>
                    <button class="px-8 py-3 bg-blue-600 text-white rounded-full font-bold hover:bg-blue-700 transition shadow-lg">
                        Tandai Selesai & Lanjut
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>