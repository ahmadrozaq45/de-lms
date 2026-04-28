<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Review Submission Siswa
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Daftar Submission --}}
                <div class="md:col-span-1 bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="font-semibold text-gray-700">Daftar Submission</h3>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @forelse($submissions as $submission)
                            <li>
                                <a href="{{ route('teacher.reviews.index', ['submission' => $submission->id]) }}"
                                   class="block p-4 hover:bg-blue-50 transition {{ isset($selected) && $selected->id === $submission->id ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                                    <p class="font-medium text-gray-800 text-sm">{{ $submission->student->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Status:
                                        <span class="capitalize font-semibold
                                            {{ $submission->status === 'graded' ? 'text-green-600' :
                                               ($submission->status === 'pending' ? 'text-yellow-600' : 'text-blue-600') }}">
                                            {{ $submission->status }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $submission->created_at->diffForHumans() }}</p>
                                </a>
                            </li>
                        @empty
                            <li class="p-4 text-sm text-gray-400 italic">Belum ada submission.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Detail & Form Review --}}
                <div class="md:col-span-2 bg-white shadow-sm rounded-lg">
                    @if($selected)
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-1">
                                Submission dari: {{ $selected->student->name ?? '-' }}
                            </h3>
                            <p class="text-sm text-gray-500 mb-4">
                                Dikumpulkan: {{ $selected->created_at->format('d M Y, H:i') }}
                            </p>

                            @if($selected->file_path)
                                <a href="{{ $selected->file_path }}" target="_blank"
                                   class="inline-block mb-6 px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Lihat File Submission
                                </a>
                            @endif

                            <form action="{{ route('teacher.reviews.update', $selected->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nilai (0–100)</label>
                                    <input type="number" name="score" min="0" max="100"
                                           value="{{ old('score', $selected->score) }}"
                                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    @error('score')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Feedback</label>
                                    <textarea name="feedback" rows="4"
                                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('feedback', $selected->teacher_feedback) }}</textarea>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Aksi</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="action" value="approve"
                                                   {{ old('action', $selected->status) === 'graded' ? 'checked' : '' }}>
                                            <span class="text-sm">Approve (Graded)</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="action" value="send"
                                                   {{ old('action', $selected->status) === 'reviewed' ? 'checked' : '' }}>
                                            <span class="text-sm">Kirim Feedback (Reviewed)</span>
                                        </label>
                                    </div>
                                    @error('action')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-semibold">
                                    Simpan Review
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-6 flex items-center justify-center h-full text-gray-400 italic">
                            Pilih submission dari daftar kiri untuk mulai mereview.
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
