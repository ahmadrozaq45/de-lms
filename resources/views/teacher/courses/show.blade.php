<x-app-layout>
    <style>
        .tab-btn { padding:14px 24px; font-size:15px; font-weight:600; cursor:pointer; border:none; background:none; color:#64748b; border-bottom:3px solid transparent; transition:all 0.2s ease-in-out; }
        .tab-btn:hover { color:#1e293b; background-color:#f8fafc; }
        .tab-btn.active { color:#3b5bdb; border-bottom-color:#3b5bdb; background-color:transparent; }
        .tab-content { display:none; animation: fadeIn 0.3s ease-in-out; }
        .tab-content.active { display:block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .materi-item { transition: all 0.2s ease; border-left: 3px solid transparent; }
        .materi-item:hover { border-left-color: #3b5bdb; background-color: #f8fafc; }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <a href="{{ route('teacher.courses.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Daftar Kursus
        </a>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Course Header --}}
        <div style="background:white; border-radius:16px; overflow:hidden; border:1px solid #e2e8f0; margin-bottom:24px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.05);">
            <div style="height:200px; position:relative;">
                <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80"
                     style="width:100%; height:100%; object-fit:cover;" alt="Banner">
                <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(15,23,42,0.7), rgba(15,23,42,0.1));"></div>
                <div style="position:absolute; top:20px; right:20px; background:rgba(255,255,255,0.9); padding:6px 16px; border-radius:20px; font-size:13px; font-weight:700; color:#3b5bdb;">
                    Mode Pengajar
                </div>
            </div>
            <div style="padding:28px 32px;">
                <h1 style="font-size:28px; font-weight:800; color:#0f172a; margin:0 0 8px 0;">{{ $course->title }}</h1>
                <p style="font-size:15px; color:#475569; margin:0 0 20px 0; line-height:1.6;">{{ $course->description }}</p>
                <div style="display:flex; flex-wrap:wrap; align-items:center; gap:24px; font-size:14px; color:#475569; border-top:1px solid #f1f5f9; padding-top:16px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <svg width="18" height="18" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <span><strong>{{ $course->modules->count() }}</strong> modul</span>
                    </div>
                    <div style="margin-left:auto; display:flex; gap:8px;">
                        <a href="{{ route('teacher.courses.students', $course->id) }}"
                           style="display:inline-flex; align-items:center; gap:6px; background:#eff6ff; color:#3b5bdb; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; text-decoration:none;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Kelola Siswa
                        </a>
                        <a href="{{ route('teacher.courses.edit', $course->id) }}"
                           style="display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:#475569; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; text-decoration:none;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Kursus
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Container --}}
        <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);">

            <div style="display:flex; border-bottom:1px solid #e2e8f0; background:#f8fafc; padding:0 16px;">
                <button class="tab-btn active" onclick="switchTab(event,'materi')">Materi</button>
                <button class="tab-btn" onclick="switchTab(event,'diskusi')">Diskusi</button>
                <button class="tab-btn" onclick="switchTab(event,'tugas')">Tugas</button>
                <button class="tab-btn" onclick="switchTab(event,'ujian')">Ujian</button>
            </div>

            {{-- TAB MATERI --}}
            <div id="tab-materi" class="tab-content active" style="padding:32px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                    <h2 style="font-size:18px; font-weight:700; color:#1e293b; margin:0;">Kelola Modul & Materi</h2>
                    <button onclick="toggleModal('modal-add-module')"
                            style="display:inline-flex; align-items:center; gap:6px; background:#3b5bdb; color:white; font-size:14px; font-weight:600; padding:10px 20px; border-radius:10px; border:none; cursor:pointer;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Modul
                    </button>
                </div>

                @forelse ($course->modules as $module)
                    <div style="margin-bottom:24px; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
                        <div style="background:#f8fafc; padding:12px 18px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #e2e8f0;">
                            <h3 style="font-size:15px; font-weight:700; color:#1e293b; margin:0; display:flex; align-items:center; gap:8px;">
                                <span style="width:8px; height:8px; background:#3b5bdb; border-radius:50%; flex-shrink:0;"></span>
                                {{ $module->title }}
                            </h3>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <button onclick="toggleModal('modal-material-{{ $module->id }}')"
                                        style="background:#dcfce7; color:#16a34a; font-size:12px; font-weight:700; padding:5px 12px; border-radius:7px; border:none; cursor:pointer;">+ Materi</button>
                                <button onclick="toggleModal('modal-assignment-{{ $module->id }}')"
                                        style="background:#ede9fe; color:#6d28d9; font-size:12px; font-weight:700; padding:5px 12px; border-radius:7px; border:none; cursor:pointer;">+ Tugas</button>
                                <button onclick="toggleModal('modal-edit-modul-{{ $module->id }}')"
                                        style="background:#fef9c3; color:#a16207; padding:5px 9px; border-radius:7px; border:none; cursor:pointer;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('teacher.modules.destroy', $module->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus modul ini?')" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:#fee2e2; color:#dc2626; padding:5px 9px; border-radius:7px; border:none; cursor:pointer;">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div style="padding:14px 18px; display:flex; flex-direction:column; gap:8px;">
                            @forelse($module->materials as $material)
                                <div class="materi-item" style="display:flex; align-items:center; gap:14px; padding:12px 14px; border:1px solid #e2e8f0; border-radius:10px; background:white;">
                                    <div style="width:36px; height:36px; background:#eff6ff; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:14px; font-weight:600; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $material->title }}</div>
                                        <div style="font-size:12px; color:#64748b;">{{ Str::limit(strip_tags($material->content ?? 'Materi pembelajaran untuk modul ini.'), 70) }}</div>
                                    </div>
                                    <div style="display:flex; align-items:center; gap:5px; flex-shrink:0;">
                                        <button onclick="toggleModal('modal-edit-material-{{ $material->id }}')"
                                                style="background:#fef9c3; color:#a16207; padding:4px 8px; border-radius:6px; border:none; cursor:pointer;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('teacher.materials.destroy', $material->id) }}" method="POST"
                                              onsubmit="return confirm('Hapus materi ini?')" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="background:#fee2e2; color:#dc2626; padding:4px 8px; border-radius:6px; border:none; cursor:pointer;">
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </div>

                                {{-- MODAL EDIT MATERI --}}
                                <div id="modal-edit-material-{{ $material->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
                                    <div class="flex items-center justify-center min-h-screen p-4">
                                        <div class="fixed inset-0 bg-gray-900 bg-opacity-60" onclick="toggleModal('modal-edit-material-{{ $material->id }}')"></div>
                                        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full overflow-hidden">
                                            <form action="{{ route('teacher.materials.update', $material->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PUT')
                                                <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center">
                                                    <h3 class="text-xl font-bold">Edit Materi</h3>
                                                    <button type="button" onclick="toggleModal('modal-edit-material-{{ $material->id }}')" class="text-gray-400 hover:text-gray-600">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                                <div class="p-8 space-y-5">
                                                    <div>
                                                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Materi</label>
                                                        <input type="text" name="title" value="{{ $material->title }}" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3" required>
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-bold text-gray-700 mb-2">Tipe</label>
                                                        <select name="type" onchange="handleTypeChangeEdit(this, {{ $material->id }})" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3">
                                                            <option value="text" @selected($material->type == 'text')>📝 Teks / Artikel</option>
                                                            <option value="file" @selected($material->type == 'file')>📁 Upload File</option>
                                                        </select>
                                                    </div>
                                                    <div id="edit-content-section-{{ $material->id }}" class="{{ $material->type == 'file' ? 'hidden' : '' }}">
                                                        <label class="block text-sm font-bold text-gray-700 mb-2">Isi Konten</label>
                                                        <textarea name="content" class="edit-editor-container" data-material-id="{{ $material->id }}">{{ $material->content }}</textarea>
                                                    </div>
                                                    <div id="edit-file-section-{{ $material->id }}" class="{{ $material->type != 'file' ? 'hidden' : '' }} bg-gray-50 p-5 rounded-xl border-2 border-dashed border-gray-300">
                                                        <label class="block text-sm font-bold text-gray-700 mb-2">Ganti File (opsional)</label>
                                                        <input type="file" name="file_path" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.mkv" class="w-full text-sm text-gray-500">
                                                        @if($material->file_path)
                                                            <p class="text-xs text-gray-500 mt-2">File saat ini: <strong>{{ basename($material->file_path) }}</strong></p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="px-8 py-5 bg-gray-50 flex flex-row-reverse gap-3">
                                                    <button type="submit" class="bg-blue-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-blue-700 transition">Simpan</button>
                                                    <button type="button" onclick="toggleModal('modal-edit-material-{{ $material->id }}')" class="bg-white text-gray-600 px-8 py-2.5 rounded-xl font-bold border border-gray-200 hover:bg-gray-100 transition">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p style="color:#94a3b8; font-size:13px; font-style:italic; padding:4px 2px;">Belum ada materi di modul ini.</p>
                            @endforelse

                            @foreach($module->assignments as $assignment)
                                <div class="materi-item" style="display:flex; align-items:center; gap:14px; padding:12px 14px; border:1px solid #e0e7ff; border-radius:10px; background:#fafafe; border-left:3px solid #6366f1;">
                                    <div style="width:36px; height:36px; background:#f5f3ff; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div style="flex:1;">
                                        <div style="font-size:14px; font-weight:700; color:#1e1b4b; margin-bottom:3px;">
                                            <span style="font-size:10px; background:#eeebff; color:#6366f1; padding:1px 6px; border-radius:4px; margin-right:4px;">TUGAS</span>
                                            {{ $assignment->title }}
                                        </div>
                                        <div style="font-size:12px; color:#64748b;">
                                            Tenggat: <span style="color:#ef4444; font-weight:600;">{{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- MODAL TAMBAH MATERI --}}
                    <div id="modal-material-{{ $module->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen p-4">
                            <div class="fixed inset-0 bg-gray-900 bg-opacity-60" onclick="toggleModal('modal-material-{{ $module->id }}')"></div>
                            <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full overflow-hidden">
                                <form action="{{ route('teacher.modules.materials.store', $module->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center">
                                        <h3 class="text-xl font-bold">Tambah Materi ke: {{ $module->title }}</h3>
                                        <button type="button" onclick="toggleModal('modal-material-{{ $module->id }}')" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div class="p-8 space-y-5">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Judul Materi</label>
                                            <input type="text" name="title" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3" placeholder="Contoh: Dasar-dasar Algoritma" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Tipe</label>
                                            <select name="type" onchange="handleTypeChange(this, {{ $module->id }})" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3">
                                                <option value="text">📝 Teks / Artikel</option>
                                                <option value="file">📁 Upload File</option>
                                            </select>
                                        </div>
                                        <div id="content-section-{{ $module->id }}">
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Isi Konten</label>
                                            <textarea name="content" class="editor-container" data-module-id="{{ $module->id }}"></textarea>
                                        </div>
                                        <div id="file-section-{{ $module->id }}" class="hidden bg-gray-50 p-5 rounded-xl border-2 border-dashed border-gray-300">
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih File</label>
                                            <input type="file" name="file_path" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.mkv" class="w-full text-sm text-gray-500">
                                            <p class="text-xs text-gray-500 mt-2 italic">PDF, DOC/X, XLS/X, PPT/X, MP4, MKV. Maks 20MB.</p>
                                        </div>
                                    </div>
                                    <div class="px-8 py-5 bg-gray-50 flex flex-row-reverse gap-3">
                                        <button type="submit" class="bg-blue-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-blue-700 transition">Simpan</button>
                                        <button type="button" onclick="toggleModal('modal-material-{{ $module->id }}')" class="bg-white text-gray-600 px-8 py-2.5 rounded-xl font-bold border border-gray-200 hover:bg-gray-100 transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL TAMBAH TUGAS --}}
                    <div id="modal-assignment-{{ $module->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen p-4">
                            <div class="fixed inset-0 bg-gray-900 bg-opacity-60" onclick="toggleModal('modal-assignment-{{ $module->id }}')"></div>
                            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden text-left">
                                <form action="{{ route('teacher.modules.assignments.store', $module->id) }}" method="POST">
                                    @csrf
                                    <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center">
                                        <h3 class="text-xl font-bold">Tambah Tugas ke: {{ $module->title }}</h3>
                                        <button type="button" onclick="toggleModal('modal-assignment-{{ $module->id }}')" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div class="p-8 space-y-5">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Judul Tugas</label>
                                            <input type="text" name="title" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3" placeholder="Contoh: Tugas 1: Analisis Sistem" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Instruksi <span class="text-red-500">*</span></label>
                                            <textarea name="description" rows="4" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4" placeholder="Tuliskan petunjuk pengerjaan..." required></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-2">Tenggat Waktu</label>
                                                <input type="datetime-local" name="due_date" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-2">Nilai Maksimal</label>
                                                <input type="number" name="max_score" min="1" max="100" value="100" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-8 py-5 bg-gray-50 flex flex-row-reverse gap-3">
                                        <button type="submit" class="bg-indigo-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-indigo-700 transition">Simpan Tugas</button>
                                        <button type="button" onclick="toggleModal('modal-assignment-{{ $module->id }}')" class="bg-white text-gray-600 px-8 py-2.5 rounded-xl font-bold border border-gray-200 hover:bg-gray-100 transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- MODAL EDIT MODUL --}}
                    <div id="modal-edit-modul-{{ $module->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen p-4">
                            <div class="fixed inset-0 bg-gray-900 bg-opacity-60" onclick="toggleModal('modal-edit-modul-{{ $module->id }}')"></div>
                            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
                                <form action="{{ route('teacher.modules.update', $module->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center">
                                        <h3 class="text-xl font-bold">Edit Modul</h3>
                                        <button type="button" onclick="toggleModal('modal-edit-modul-{{ $module->id }}')" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div class="p-8 space-y-5">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Judul Modul</label>
                                            <input type="text" name="title" value="{{ $module->title }}" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Urutan</label>
                                            <input type="number" name="order" value="{{ $module->order }}" min="0" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3">
                                        </div>
                                    </div>
                                    <div class="px-8 py-5 bg-gray-50 flex flex-row-reverse gap-3">
                                        <button type="submit" class="bg-blue-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-blue-700 transition">Simpan</button>
                                        <button type="button" onclick="toggleModal('modal-edit-modul-{{ $module->id }}')" class="bg-white text-gray-600 px-8 py-2.5 rounded-xl font-bold border border-gray-200 hover:bg-gray-100 transition">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @empty
                    <div style="text-align:center; padding:48px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 16px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <p style="color:#64748b; font-weight:500;">Belum ada modul. Klik "Tambah Modul" untuk memulai.</p>
                    </div>
                @endforelse
            </div>

            {{-- TAB DISKUSI --}}
            <div id="tab-diskusi" class="tab-content" style="padding:32px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                    <h2 style="font-size:18px; font-weight:700; color:#1e293b; margin:0;">Forum Diskusi</h2>
                    <span style="font-size:12px; background:#f1f5f9; color:#64748b; padding:6px 14px; border-radius:20px; font-weight:600;">Segera Hadir</span>
                </div>
                <div style="text-align:center; padding:48px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1;">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 16px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <p style="color:#64748b; font-size:15px; font-weight:500; margin:0 0 8px;">Forum diskusi akan segera tersedia.</p>
                    <p style="color:#94a3b8; font-size:13px; margin:0;">Anda dapat memantau dan membalas pertanyaan siswa dari sini.</p>
                </div>
            </div>

            {{-- TAB TUGAS --}}
            <div id="tab-tugas" class="tab-content" style="padding:32px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                    <h2 style="font-size:18px; font-weight:700; color:#1e293b; margin:0;">Daftar Tugas</h2>
                    <span style="font-size:13px; color:#64748b;">Total: <strong>{{ $course->modules->sum(fn($m) => $m->assignments->count()) }}</strong> tugas</span>
                </div>

                @php $hasAssignment = false; @endphp
                @foreach ($course->modules as $module)
                    @foreach ($module->assignments as $assignment)
                        @php $hasAssignment = true; @endphp
                        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:20px 24px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                            <div style="flex:1;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                                    <span style="font-size:10px; font-weight:700; background:#f5f3ff; color:#6366f1; padding:2px 8px; border-radius:4px; border:1px solid #e0e7ff;">{{ $module->title }}</span>
                                    <h4 style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $assignment->title }}</h4>
                                </div>
                                <p style="font-size:13px; color:#64748b; margin:0 0 6px 0;">{{ $assignment->instructions ?? $assignment->description ?? 'Silakan baca instruksi pada detail tugas.' }}</p>
                                <span style="font-size:12px; color:#94a3b8;">Skor Maks: <strong style="color:#475569;">{{ $assignment->max_score ?? 100 }}</strong></span>
                            </div>
                            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px; margin-left:20px; flex-shrink:0;">
                                <span style="background:#fffbeb; color:#d97706; font-size:12px; font-weight:700; padding:5px 14px; border-radius:8px; border:1px solid #fef3c7;">
                                    Tenggat: {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y, H:i') }}
                                </span>
                                <span style="background:#dcfce7; color:#16a34a; font-size:12px; font-weight:600; padding:4px 12px; border-radius:8px;">
                                    0 Pengumpulan
                                </span>
                            </div>
                        </div>
                    @endforeach
                @endforeach

                @if(!$hasAssignment)
                    <div style="text-align:center; padding:48px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 16px;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p style="color:#64748b; font-weight:500; margin:0;">Belum ada tugas. Tambahkan melalui tab Materi.</p>
                    </div>
                @endif
            </div>

            {{-- TAB UJIAN --}}
            <div id="tab-ujian" class="tab-content" style="padding:32px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                    <h2 style="font-size:18px; font-weight:700; color:#1e293b; margin:0;">
                        Daftar Quiz
                        <span style="font-size:14px; font-weight:500; color:#94a3b8; margin-left:8px;">{{ $course->quizzes->count() }} quiz</span>
                    </h2>
                    <a href="{{ route('teacher.courses.quizzes.create', $course->id) }}"
                       style="display:inline-flex; align-items:center; gap:6px; background:#3b5bdb; color:white; font-size:14px; font-weight:600; padding:10px 20px; border-radius:10px; text-decoration:none;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Buat Quiz
                    </a>
                </div>

                @forelse($course->quizzes as $quiz)
                    <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:20px 24px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; gap:16px; transition:box-shadow .2s;"
                         onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)'"
                         onmouseout="this.style.boxShadow='none'">
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                                <span style="font-size:10px; font-weight:700; background:#eff3ff; color:#3b5bdb; padding:2px 8px; border-radius:4px; border:1px solid #c7d2fe;">QUIZ</span>
                                <h4 style="font-size:16px; font-weight:700; color:#1e293b; margin:0;">{{ $quiz->title }}</h4>
                            </div>
                            <div style="display:flex; flex-wrap:wrap; gap:14px; font-size:13px; color:#64748b;">
                                <span>⏱ {{ $quiz->time_limit }} menit</span>
                                <span>📋 {{ $quiz->questions->count() }} soal</span>
                                <span>✅ Lulus: {{ $quiz->passing_score }}%</span>
                                <span>👥 {{ $quiz->attempts->count() }} attempt</span>
                            </div>
                        </div>
                        <div style="display:flex; gap:8px; flex-shrink:0;">
                            <a href="{{ route('teacher.quizzes.results', $quiz->id) }}"
                               style="display:inline-flex; align-items:center; gap:5px; background:#dcfce7; color:#16a34a; font-size:12px; font-weight:700; padding:7px 14px; border-radius:8px; text-decoration:none;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                                Hasil
                            </a>
                            <a href="{{ route('teacher.quizzes.show', $quiz->id) }}"
                               style="display:inline-flex; align-items:center; gap:5px; background:#eff3ff; color:#3b5bdb; font-size:12px; font-weight:700; padding:7px 14px; border-radius:8px; text-decoration:none;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Kelola Soal
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:48px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 16px;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p style="color:#64748b; font-weight:500; margin:0 0 12px;">Belum ada quiz di kursus ini.</p>
                        <a href="{{ route('teacher.courses.quizzes.create', $course->id) }}"
                           style="display:inline-flex; align-items:center; gap:6px; background:#3b5bdb; color:white; font-size:14px; font-weight:600; padding:10px 20px; border-radius:10px; text-decoration:none;">
                            + Buat Quiz Pertama
                        </a>
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- MODAL TAMBAH MODUL --}}
    <div id="modal-add-module" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60" onclick="toggleModal('modal-add-module')"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
                <form action="{{ route('teacher.courses.modules.store', $course->id) }}" method="POST">
                    @csrf
                    <div class="px-8 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-xl font-bold">Tambah Modul Baru</h3>
                        <button type="button" onclick="toggleModal('modal-add-module')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Judul Modul</label>
                        <input type="text" name="title" class="w-full border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3" placeholder="Contoh: Pengantar Pemrograman" required>
                    </div>
                    <div class="px-8 py-5 bg-gray-50 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-blue-700 transition">Simpan Modul</button>
                        <button type="button" onclick="toggleModal('modal-add-module')" class="bg-white text-gray-600 px-8 py-2.5 rounded-xl font-bold border border-gray-200 hover:bg-gray-100 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        const editors = {};
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.editor-container').forEach(el => {
                const moduleId = el.getAttribute('data-module-id');
                ClassicEditor.create(el, { toolbar: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','undo','redo'] })
                    .then(editor => { editors['add-' + moduleId] = editor; }).catch(console.error);
            });
            document.querySelectorAll('.edit-editor-container').forEach(el => {
                const materialId = el.getAttribute('data-material-id');
                const parentSection = el.closest('[id^="edit-content-section-"]');
                if (parentSection && parentSection.classList.contains('hidden')) return;
                ClassicEditor.create(el, { toolbar: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','undo','redo'] })
                    .then(editor => { editors['edit-' + materialId] = editor; }).catch(console.error);
            });
        });

        function switchTab(e, tabId) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            e.target.classList.add('active');
            document.getElementById('tab-' + tabId).classList.add('active');
        }

        function toggleModal(id) {
            document.getElementById(id).classList.toggle('hidden');
        }

        function handleTypeChange(select, moduleId) {
            const c = document.getElementById('content-section-' + moduleId);
            const f = document.getElementById('file-section-' + moduleId);
            if (select.value === 'text') { c.classList.remove('hidden'); f.classList.add('hidden'); }
            else { c.classList.add('hidden'); f.classList.remove('hidden'); }
        }

        function handleTypeChangeEdit(select, materialId) {
            const c = document.getElementById('edit-content-section-' + materialId);
            const f = document.getElementById('edit-file-section-' + materialId);
            if (select.value === 'text') {
                c.classList.remove('hidden'); f.classList.add('hidden');
                if (!editors['edit-' + materialId]) {
                    const el = c.querySelector('.edit-editor-container');
                    if (el) ClassicEditor.create(el, { toolbar: ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','undo','redo'] })
                        .then(editor => { editors['edit-' + materialId] = editor; });
                }
            } else { c.classList.add('hidden'); f.classList.remove('hidden'); }
        }
    </script>
</x-app-layout>