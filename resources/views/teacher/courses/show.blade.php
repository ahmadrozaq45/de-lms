<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>
            <a href="{{ route('teacher.courses.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali ke Daftar Kursus</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Sidebar: Info + Tambah Modul --}}
                <div class="space-y-6">

                    {{-- Info Kursus --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-semibold text-gray-800 mb-2">Informasi Kursus</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ $course->description ?? 'Tidak ada deskripsi.' }}</p>
                        <p class="text-xs text-gray-400">Dibuat: {{ $course->created_at->format('d M Y') }}</p>
                        <div class="mt-4">
                            <a href="{{ route('teacher.courses.edit', $course->id) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded text-xs hover:bg-yellow-200 transition">
                                Edit Kursus
                            </a>
                        </div>
                    </div>

                    {{-- Form Tambah Modul --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Tambah Modul Baru</h3>
                        <form action="{{ route('teacher.courses.modules.store', $course->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Modul</label>
                                <input type="text" name="title"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="Contoh: Pengenalan Laravel" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Urutan</label>
                                <input type="number" name="order"
                                       class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="1" min="1" required>
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700 transition">
                                Simpan Modul
                            </button>
                        </form>
                    </div>

                </div>

                {{-- Konten: Daftar Modul & Materi --}}
                <div class="md:col-span-2 space-y-4">
                    <h3 class="font-semibold text-gray-800">Konten Pembelajaran</h3>

                    @if($course->modules->isEmpty())
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center text-gray-500 text-sm">
                            Belum ada modul. Silakan tambah modul di panel samping.
                        </div>
                    @endif

                    @foreach($course->modules as $module)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="flex justify-between items-center px-6 py-4 border-b bg-gray-50">
                                <span class="font-medium text-gray-800 text-sm">
                                    Modul {{ $module->order }}: {{ $module->title }}
                                </span>
                                <button
                                    onclick="document.getElementById('modal-{{ $module->id }}').classList.remove('hidden')"
                                    class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded text-xs hover:bg-green-200 transition">
                                    + Materi
                                </button>
                            </div>
                            <ul class="divide-y divide-gray-100">
                                @forelse($module->materials as $material)
                                    <li class="flex justify-between items-center px-6 py-3 text-sm">
                                        <span class="text-gray-800">{{ $material->title }}</span>
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs uppercase">{{ $material->type }}</span>
                                    </li>
                                @empty
                                    <li class="px-6 py-4 text-sm text-gray-400 text-center italic">Belum ada materi di modul ini.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Modal Tambah Materi --}}
                        <div id="modal-{{ $module->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
                                <div class="flex justify-between items-center px-6 py-4 border-b">
                                    <h4 class="font-semibold text-gray-800">Tambah Materi: {{ $module->title }}</h4>
                                    <button onclick="document.getElementById('modal-{{ $module->id }}').classList.add('hidden')"
                                            class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                                </div>
                                <form action="{{ route('teacher.modules.materials.store', $module->id) }}" method="POST">
                                    @csrf
                                    <div class="px-6 py-4 space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Materi</label>
                                            <input type="text" name="title"
                                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                   required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Materi</label>
                                            <select name="type" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="text">Teks / Artikel</option>
                                                <option value="video">Video (URL)</option>
                                                <option value="pdf">Dokumen PDF</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Konten / URL</label>
                                            <textarea name="content" rows="3"
                                                      class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 px-6 py-4 border-t bg-gray-50">
                                        <button type="button"
                                                onclick="document.getElementById('modal-{{ $module->id }}').classList.add('hidden')"
                                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300 transition">
                                            Batal
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700 transition">
                                            Simpan Materi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>
    </div>
</x-app-layout>