<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Kursus: ') }} {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6 border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-800">Tambah Modul Baru</h3>
                    <form action="{{ route('teacher.courses.modules.store', $course->id) }}" method="POST">
                        @csrf
                        <div class="flex gap-4">
                            <input type="text" name="title" placeholder="Contoh: Pengantar Pengolahan Citra" class="border-gray-200 rounded-lg shadow-sm w-full focus:ring-blue-500 focus:border-blue-500" required>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">Simpan Modul</button>
                        </div>
                    </form>
                </div>
            </div>

            @foreach($course->modules as $module)
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6 border border-gray-100">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-full shadow-sm border border-gray-200">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-800 text-lg">{{ $module->title }}</h4>
                    </div>
                    <button onclick="toggleModal('modal-material-{{ $module->id }}')" class="bg-green-600 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        + Tambah Materi
                    </button>
                </div>
                
                <div class="p-6">
                    @if($module->description)
                        <div class="mb-6 text-gray-600 leading-relaxed prose max-w-none">
                            {!! $module->description !!}
                        </div>
                    @endif

                    <div class="space-y-4">
                        <p class="text-sm font-medium text-gray-500">Pelajari materi berikut ini:</p>
                        
                        @forelse($module->materials as $material)
                            <div class="flex items-center gap-4 p-2 group transition-all">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg shadow-sm
                                    @if($material->type == 'file') bg-purple-50 text-purple-600 
                                    @else bg-blue-50 text-blue-600 @endif">
                                    
                                    @if($material->type == 'file')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-700 hover:text-blue-600 transition cursor-pointer">{{ $material->title }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $material->type }}</span>
                                    </div>
                                    
                                    @if($material->type == 'text' && $material->content)
                                        <div class="mt-2 text-sm text-gray-600 prose-sm prose-blue max-w-none border-l-4 border-blue-100 pl-4 bg-blue-50/30 py-2 rounded-r-lg">
                                            {!! $material->content !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm italic">Belum ada materi di modul ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div id="modal-material-{{ $module->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" onclick="toggleModal('modal-material-{{ $module->id }}')"></div>
                    
                    <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden transform transition-all">
                        <form action="{{ route('teacher.modules.materials.store', $module->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="text-xl font-bold text-gray-900">Tambah Materi ke: {{ $module->title }}</h3>
                                <button type="button" onclick="toggleModal('modal-material-{{ $module->id }}')" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"></path></svg>
                                </button>
                            </div>

                            <div class="p-8 space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Materi</label>
                                    <input type="text" name="title" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3" placeholder="Contoh: Dasar-dasar Segmentasi Citra" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Materi</label>
                                    <select name="type" onchange="handleTypeChange(this, {{ $module->id }})" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3">
                                        <option value="text">📝 Teks / Artikel</option>
                                        <option value="file">📁 Upload File (Dokumen / Video)</option>
                                    </select>
                                </div>
                                
                                <div id="content-section-{{ $module->id }}" class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Isi Konten Materi</label>
                                    <textarea name="content" class="editor-container" data-module-id="{{ $module->id }}"></textarea>
                                </div>

                                <div id="file-section-{{ $module->id }}" class="mb-4 hidden bg-gray-50 p-6 rounded-xl border-2 border-dashed border-gray-300">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih File</label>
                                    <input type="file" name="file_path" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.mkv" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-500 mt-2 italic">Format didukung: PDF, DOC/X, XLS/X, PPT/X, MP4, MKV. (Ukuran maksimal: 20MB)</p>
                                </div>
                            </div>

                            <div class="px-8 py-6 bg-gray-50 flex flex-row-reverse gap-3">
                                <button type="submit" class="bg-blue-600 text-white px-10 py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition">Simpan</button>
                                <button type="button" onclick="toggleModal('modal-material-{{ $module->id }}')" class="bg-white text-gray-600 px-10 py-2.5 rounded-xl font-bold border border-gray-200 hover:bg-gray-100 transition">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        const editors = {};

        function initCKEditor() {
            document.querySelectorAll('.editor-container').forEach(el => {
                ClassicEditor
                    .create(el, {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
                    })
                    .then(editor => {
                        const moduleId = el.getAttribute('data-module-id');
                        editors[moduleId] = editor;
                    })
                    .catch(error => console.error(error));
            });
        }

        document.addEventListener('DOMContentLoaded', initCKEditor);

        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }

        function handleTypeChange(select, moduleId) {
            const contentSection = document.getElementById('content-section-' + moduleId);
            const fileSection = document.getElementById('file-section-' + moduleId);

            if (select.value === 'text') {
                contentSection.classList.remove('hidden');
                fileSection.classList.add('hidden');
            } else { // Jika valuenya 'file'
                contentSection.classList.add('hidden');
                fileSection.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>