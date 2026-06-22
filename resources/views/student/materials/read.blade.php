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
                
                @if($material->type === 'file' && $material->file_path)
                    {{-- Materi File: tampilkan tombol download --}}
                    <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:12px; padding:28px 32px; display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div style="width:48px; height:48px; background:#0ea5e9; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
                            </div>
                            <div>
                                <div style="font-size:16px; font-weight:700; color:#0c4a6e;">{{ $material->title }}</div>
                                <div style="font-size:13px; color:#0369a1; margin-top:2px;">
                                    Format: <strong>{{ strtoupper(pathinfo($material->file_path, PATHINFO_EXTENSION)) }}</strong>
                                    &nbsp;•&nbsp; Klik tombol untuk mengunduh file materi
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('student.materials.download', $material->id) }}"
                           style="display:inline-flex; align-items:center; gap:8px; background:#0284c7; color:white; font-size:14px; font-weight:700; padding:12px 24px; border-radius:10px; text-decoration:none; white-space:nowrap; transition:background 0.2s;"
                           onmouseover="this.style.background='#0369a1'" onmouseout="this.style.background='#0284c7'">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Unduh File
                        </a>
                    </div>
                @elseif($material->type === 'file' && !$material->file_path)
                    {{-- Materi File tapi belum ada file yang diupload --}}
                    <div style="background:#fefce8; border:1px solid #fde68a; border-radius:12px; padding:24px; display:flex; align-items:center; gap:14px;">
                        <svg width="24" height="24" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span style="font-size:14px; color:#92400e; font-weight:500;">File materi belum tersedia. Silakan hubungi pengajar.</span>
                    </div>
                @else
                    {{-- Materi Teks --}}
                    <div class="prose max-w-none text-gray-700 text-lg leading-relaxed">
                        {!! $material->content ?? '<p style="color:#94a3b8; font-style:italic;">Isi materi belum ditambahkan.</p>' !!}
                    </div>
                @endif

                <div class="mt-12 pt-8 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-sm text-gray-500">Pastikan kamu sudah memahami materi ini.</span>
                    <form action="{{ route('student.materials.complete', $material->id) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-full font-bold hover:bg-blue-700 transition shadow-lg cursor-pointer">
                            Tandai Selesai & Lanjut
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>