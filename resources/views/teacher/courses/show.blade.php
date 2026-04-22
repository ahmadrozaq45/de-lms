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
                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
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
            <!-- Back -->
            <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </a>

            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <!-- Course Header -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h2>
                <p class="text-gray-500 mt-1">{{ $course->description }}</p>
                <div class="flex gap-4 mt-3 text-sm text-gray-400">
                    <span>{{ $course->modules->count() }} Modul</span>
                    <span>{{ $course->modules->sum(fn($m) => $m->materials->count()) }} Materi</span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <!-- Modul List -->
                <div class="col-span-2 space-y-4">
                    @forelse($course->modules->sortBy('order') as $module)
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="font-semibold text-gray-800">{{ $module->title }}</h3>
                            <span class="text-xs text-gray-400">{{ $module->materials->count() }} materi</span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @forelse($module->materials as $material)
                            <div class="flex items-center gap-3 px-5 py-3">
                                @php
                                    $iconColor = match($material->type) {
                                        'video' => 'text-red-500 bg-red-50',
                                        'pdf'   => 'text-orange-500 bg-orange-50',
                                        default => 'text-blue-500 bg-blue-50',
                                    };
                                @endphp
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold uppercase {{ $iconColor }}">
                                    {{ $material->type }}
                                </span>
                                <span class="text-sm text-gray-700">{{ $material->title }}</span>
                            </div>
                            @empty
                            <p class="px-5 py-3 text-sm text-gray-400 italic">Belum ada materi.</p>
                            @endforelse
                        </div>

                        <!-- Form tambah materi -->
                        <div class="px-5 py-4 border-t border-gray-100 bg-white">
                            <form method="POST" action="{{ route('teacher.modules.materials.store', $module->id) }}">
                                @csrf
                                <p class="text-xs font-medium text-gray-500 mb-2">+ Tambah Materi</p>
                                <div class="flex gap-2">
                                    <input type="text" name="title" placeholder="Judul materi"
                                           class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                    <select name="type" class="border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                        <option value="text">Text</option>
                                        <option value="video">Video</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm transition">
                                        Tambah
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">
                        <p>Belum ada modul. Tambahkan modul pertama di panel kanan.</p>
                    </div>
                    @endforelse
                </div>

                <!-- Sidebar: Tambah Modul -->
                <div class="space-y-4">
                    <div class="bg-white rounded-xl border border-gray-200 p-5">
                        <h3 class="font-semibold text-gray-800 mb-4">Tambah Modul Baru</h3>
                        <form method="POST" action="{{ route('teacher.courses.modules.store', $course->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Judul Modul</label>
                                <input type="text" name="title"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                                       placeholder="Contoh: Pengenalan React" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm text-gray-600 mb-1">Urutan</label>
                                <input type="number" name="order" value="{{ $course->modules->count() + 1 }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition">
                                Tambah Modul
                            </button>
                        </form>
                    </div>

                    <!-- Info -->
                    <div class="bg-blue-50 rounded-xl border border-blue-100 p-4 text-sm text-blue-700">
                        <p class="font-medium mb-1">Tips:</p>
                        <p>Tambahkan modul terlebih dahulu, lalu tambahkan materi ke dalam setiap modul.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>