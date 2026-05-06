<x-app-layout>
    <style>
        /* CSS Dasar untuk Tabs dan Kartu */
        .tab-btn { padding:14px 24px; font-size:15px; font-weight:600; cursor:pointer; border:none; background:none; color:#64748b; border-bottom:3px solid transparent; transition:all 0.2s ease-in-out; }
        .tab-btn:hover { color:#1e293b; background-color:#f8fafc; }
        .tab-btn.active { color:#3b5bdb; border-bottom-color:#3b5bdb; background-color:transparent; }
        .tab-content { display:none; animation: fadeIn 0.3s ease-in-out; } 
        .tab-content.active { display:block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        /* Style tambahan untuk materi */
        .materi-item { transition: all 0.2s ease; border-left: 3px solid transparent; }
        .materi-item:hover { border-left-color: #3b5bdb; background-color: #f8fafc; }
    </style>

    <!-- ========================================== -->
    <!-- CONTAINER PEMBUNGKUS (Agar Tidak Mepet)    -->
    <!-- ========================================== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Tombol Kembali -->
        <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 font-medium text-sm mb-6 transition-colors">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Dashboard
        </a>

        <!-- Hero Banner Utama -->
        <div style="background:white; border-radius:16px; overflow:hidden; border:1px solid #e2e8f0; margin-bottom:24px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
            <!-- Bagian Gambar -->
            <div style="height:260px; position:relative;">
                <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80" 
                     style="width:100%; height:100%; object-fit:cover;" alt="Banner Kursus">
                <!-- Gradien gelap agar teks kalau ada di atas gambar tetap terbaca -->
                <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(15,23,42,0.7), rgba(15,23,42,0.1));"></div>
                
                <!-- Badge Kategori / Status (Opsional) -->
                <div style="position:absolute; top:20px; right:20px; background:rgba(255,255,255,0.9); padding:6px 16px; border-radius:20px; font-size:13px; font-weight:700; color:#3b5bdb; backdrop-filter:blur(4px);">
                    Course Aktif
                </div>
            </div>
            
            <!-- Bagian Informasi Kursus -->
            <div style="padding:32px;">
                <h1 style="font-size:32px; font-weight:800; color:#0f172a; margin:0 0 12px 0; line-height:1.2;">{{ $course->title }}</h1>
                <p style="font-size:16px; color:#475569; margin:0 0 24px 0; line-height:1.6; max-width:800px;">{{ $course->description }}</p>
                
                <div style="display:flex; flex-wrap:wrap; align-items:center; gap:32px; font-size:15px; color:#475569; border-top:1px solid #f1f5f9; padding-top:20px;">
                    <!-- Instruktur -->
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div style="width:32px; height:32px; background:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                            <svg width="16" height="16" fill="#64748b" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <span>Instruktur: <strong style="color:#1e293b;">{{ $course->teacher->name ?? 'Papa Zola' }}</strong></span>
                    </div>
                    
                    <!-- Jumlah Modul -->
                    <div style="display:flex; align-items:center; gap:8px;">
                        <svg width="20" height="20" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <span><strong>{{ $course->modules->count() }}</strong> modul</span>
                    </div>
                    
                    <!-- Progress Belajar -->
                    <div style="display:flex; align-items:center; gap:12px; margin-left:auto;">
                        <span style="font-weight:600;">Progress: <strong style="color:#3b5bdb;">65%</strong></span>
                        <div style="width:120px; height:8px; background:#e2e8f0; border-radius:10px; overflow:hidden;">
                            <div style="width:65%; height:100%; background:#3b5bdb;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sistem Tabs (Materi, Diskusi, Tugas, Ujian) -->
        <div style="background:white; border-radius:16px; border:1px solid #e2e8f0; overflow:hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            
            <!-- Header Tabs -->
            <div style="display:flex; border-bottom:1px solid #e2e8f0; background:#f8fafc; padding:0 16px;">
                <button class="tab-btn active" onclick="switchTab(event,'materi')">Materi</button>
                <button class="tab-btn" onclick="switchTab(event,'diskusi')">Diskusi</button>
                <button class="tab-btn" onclick="switchTab(event,'tugas')">Tugas</button>
                <button class="tab-btn" onclick="switchTab(event,'ujian')">Ujian</button>
            </div>

            <!-- TAB CONTENT: Materi -->
            <div id="tab-materi" class="tab-content active" style="padding:32px;">
                @forelse ($course->modules as $module)
                    <div style="margin-bottom:32px;">
                        <h3 style="font-size:18px; font-weight:700; color:#1e293b; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                            <span style="width:8px; height:8px; background:#3b5bdb; border-radius:50%;"></span>
                            {{ $module->title }}
                        </h3>
                        
                        <div style="display:flex; flex-direction:column; gap:12px;">
                            @foreach ($module->materials as $material)
                                <div class="materi-item" style="display:flex; align-items:center; gap:16px; padding:16px; border:1px solid #e2e8f0; border-radius:12px;">
                                    <div style="width:40px; height:40px; background:#eff6ff; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <!-- Ikon Buku -->
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-size:15px; font-weight:600; color:#0f172a; margin-bottom:4px;">{{ $material->title }}</div>
                                        <div style="font-size:13px; color:#64748b;">{{ Str::limit($material->content ?? 'Materi pembelajaran untuk modul ini.', 80) }}</div>
                                    </div>
                                    <div style="margin-left:auto;">
                                        <!-- Ikon Ceklis (Bisa diatur kondisional nanti) -->
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:40px; color:#64748b;">
                        <p>Belum ada materi tersedia untuk kursus ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- TAB CONTENT: Diskusi -->
            <div id="tab-diskusi" class="tab-content" style="padding:32px; text-align:center;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 16px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <p style="color:#64748b; font-size:15px; font-weight:500;">Forum diskusi akan segera hadir. Nantikan interaksi dengan teman sekelas!</p>
            </div>

            <!-- TAB CONTENT: Tugas -->
            <div id="tab-tugas" class="tab-content" style="padding:32px;">
                @forelse ($course->assignments ?? [] as $assignment)
                    <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:24px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; transition: transform 0.2s, box-shadow 0.2s;" 
                         onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.05)'" 
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                                <h4 style="font-size:18px; font-weight:700; color:#1e293b; margin:0;">
                                    {{ str_contains(strtolower($assignment->title), 'quiz') ? 'Quiz' : 'Tugas' }}: {{ $assignment->title }}
                                </h4>
                            </div>
                            <p style="font-size:14px; color:#64748b; margin:0 0 12px 0; max-width:85%; line-height:1.5;">{{ $assignment->description }}</p>
                            <div style="font-size:13px; font-weight:600; color:#94a3b8;">
                                Max Score: <span style="color:#475569;">{{ $assignment->max_score ?? 100 }}</span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:12px;">
                            <span style="background:#fffbeb; color:#d97706; font-size:12px; font-weight:700; padding:6px 16px; border-radius:8px; border:1px solid #fef3c7;">
                                Due: {{ isset($assignment->due_date) ? \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') : 'Tanpa Batas' }}
                            </span>
                            <a href="{{ route('student.assignments.show', $assignment->id) }}" 
                               style="display:inline-flex; align-items:center; gap:8px; background:#3b5bdb; color:white; font-size:14px; font-weight:600; padding:10px 24px; border-radius:10px; text-decoration:none; transition: background 0.2s;"
                               onmouseover="this.style.background='#2d45ba'" onmouseout="this.style.background='#3b5bdb'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M5 3l14 9-14 9V3z"/></svg>
                                Kerjakan
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:48px; background:#f8fafc; border-radius:12px; border:1px dashed #cbd5e1;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="margin:0 auto 16px;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p style="color:#475569; font-weight:500;">Hore! Belum ada tugas atau kuis yang tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- TAB CONTENT: Ujian -->
            <div id="tab-ujian" class="tab-content" style="padding:32px; text-align:center;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" style="margin:0 auto 16px;"><polygon points="12 2 2 7 12 12 22 7 12 2"/></svg>
                <p style="color:#64748b; font-size:15px; font-weight:500;">Jadwal ujian belum dipublikasikan oleh instruktur.</p>
            </div>
        </div>

        <!-- Tombol Daftar (Melayang di pojok kanan bawah jika belum enroll) -->
        @if(!$isEnrolled)
            <div style="position:fixed; bottom:32px; right:32px; z-index:50;">
                <form action="{{ route('student.enroll') }}" method="POST">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <button type="submit" style="display:flex; align-items:center; gap:8px; background:#3b5bdb; color:white; border:none; border-radius:12px; padding:16px 32px; font-size:16px; font-weight:700; cursor:pointer; box-shadow:0 10px 25px rgba(59,91,219,0.4); transition: transform 0.2s, box-shadow 0.2s;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 30px rgba(59,91,219,0.5)'" 
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px rgba(59,91,219,0.4)'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Daftar ke Kursus Ini
                    </button>
                </form>
            </div>
        @endif

    </div> <!-- Penutup Container max-w-7xl -->

    <!-- Script untuk mengontrol Tabs -->
    <script>
        function switchTab(e, tabId) {
            // Menghapus kelas 'active' dari semua tombol tab
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            // Menghapus kelas 'active' dari semua isi konten tab
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Menambahkan kelas 'active' pada tombol yang diklik
            e.target.classList.add('active');
            // Menampilkan konten tab yang sesuai dengan ID yang dituju
            document.getElementById('tab-' + tabId).classList.add('active');
        }
    </script>
</x-app-layout>