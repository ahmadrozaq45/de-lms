<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DE-LMS — Learning Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink: #0d1117;
            --ink-2: #1e2733;
            --ink-muted: #5a6a7e;
            --ink-faint: #8595a5;
            --surface: #ffffff;
            --surface-2: #f4f6f9;
            --surface-3: #edf0f4;
            --accent: #2563eb;
            --accent-dark: #1d4ed8;
            --accent-light: #dbeafe;
            --teal: #0d9488;
            --teal-light: #ccfbf1;
            --amber: #d97706;
            --amber-light: #fef3c7;
            --border: #e2e8f0;
            --radius: 12px;
            --radius-sm: 8px;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--ink);
            font-size: 16px;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ── NAVBAR ── */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 5%;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
        }

        .nav-brand {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; color: var(--ink);
        }

        .nav-logo {
            width: 36px; height: 36px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-family: 'Sora', sans-serif; font-weight: 700; font-size: 13px;
        }

        .nav-name {
            font-family: 'Sora', sans-serif;
            font-weight: 600; font-size: 16px;
            letter-spacing: -0.3px;
        }

        .nav-links { display: flex; align-items: center; gap: 8px; }

        .btn-ghost {
            padding: 8px 18px; border-radius: 8px;
            font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500;
            color: var(--ink-muted); text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.15s ease;
            cursor: pointer; background: transparent;
        }
        .btn-ghost:hover { background: var(--surface-2); color: var(--ink); border-color: var(--border); }

        .btn-primary {
            padding: 8px 20px; border-radius: 8px;
            background: var(--accent); color: white;
            font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500;
            text-decoration: none; border: none; cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-primary:hover { background: var(--accent-dark); transform: translateY(-1px); }

        /* ── HERO ── */
        .hero {
            padding: 140px 5% 80px;
            text-align: center;
            background: linear-gradient(180deg, #f0f4ff 0%, #ffffff 100%);
            position: relative; overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute; top: -100px; left: 50%; transform: translateX(-50%);
            width: 700px; height: 500px;
            background: radial-gradient(ellipse, rgba(37,99,235,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--accent-light); color: var(--accent-dark);
            border: 1px solid rgba(37,99,235,0.2);
            padding: 5px 14px; border-radius: 100px;
            font-size: 13px; font-weight: 500; margin-bottom: 24px;
        }

        .hero-badge-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--accent);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.4; } }

        .hero h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(36px, 5vw, 58px);
            font-weight: 700; line-height: 1.15;
            letter-spacing: -1.5px; color: var(--ink);
            max-width: 680px; margin: 0 auto 20px;
        }

        .hero h1 span { color: var(--accent); }

        .hero-desc {
            font-size: 18px; color: var(--ink-muted); font-weight: 300;
            max-width: 520px; margin: 0 auto 36px; line-height: 1.7;
        }

        .hero-cta { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        .btn-primary-lg {
            padding: 13px 28px; border-radius: 10px;
            background: var(--accent); color: white;
            font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 500;
            text-decoration: none; border: none; cursor: pointer;
            transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary-lg:hover { background: var(--accent-dark); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,0.3); }

        .btn-outline-lg {
            padding: 13px 28px; border-radius: 10px;
            background: transparent; color: var(--ink);
            font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 500;
            text-decoration: none; border: 1.5px solid var(--border); cursor: pointer;
            transition: all 0.15s ease; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-outline-lg:hover { background: var(--surface-2); border-color: var(--ink-muted); }

        .hero-stats {
            display: flex; gap: 32px; justify-content: center; flex-wrap: wrap;
            margin-top: 48px; padding-top: 40px;
            border-top: 1px solid var(--border);
        }

        .stat-item { text-align: center; }

        .stat-num {
            font-family: 'Sora', sans-serif;
            font-size: 28px; font-weight: 700;
            color: var(--ink); letter-spacing: -1px;
        }

        .stat-label { font-size: 13px; color: var(--ink-faint); margin-top: 2px; }

        /* ── FEATURES ── */
        .section { padding: 80px 5%; max-width: 1200px; margin: 0 auto; }

        .section-label {
            font-size: 12px; font-weight: 600; letter-spacing: 1.5px;
            text-transform: uppercase; color: var(--accent);
            margin-bottom: 10px;
        }

        .section-title {
            font-family: 'Sora', sans-serif;
            font-size: clamp(26px, 3vw, 36px); font-weight: 700;
            letter-spacing: -0.8px; color: var(--ink);
            margin-bottom: 12px;
        }

        .section-desc { font-size: 16px; color: var(--ink-muted); max-width: 480px; }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px; margin-top: 48px;
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px;
            transition: all 0.2s ease;
        }

        .feature-card:hover {
            border-color: rgba(37,99,235,0.3);
            box-shadow: 0 4px 24px rgba(37,99,235,0.08);
            transform: translateY(-2px);
        }

        .feature-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-bottom: 16px;
        }

        .icon-blue { background: var(--accent-light); }
        .icon-teal { background: var(--teal-light); }
        .icon-amber { background: var(--amber-light); }

        .feature-card h3 {
            font-family: 'Sora', sans-serif;
            font-size: 16px; font-weight: 600;
            color: var(--ink); margin-bottom: 8px;
        }

        .feature-card p { font-size: 14px; color: var(--ink-muted); line-height: 1.6; }

        /* ── COURSES ── */
        .courses-wrap { background: var(--surface-2); padding: 80px 5%; }
        .courses-inner { max-width: 1200px; margin: 0 auto; }

        .search-bar {
            display: flex; gap: 12px; margin: 40px 0 28px;
            flex-wrap: wrap;
        }

        .search-input-wrap {
            flex: 1; min-width: 220px;
            position: relative;
        }

        .search-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--ink-faint); font-size: 16px; pointer-events: none;
        }

        .search-input {
            width: 100%; padding: 11px 14px 11px 40px;
            border: 1.5px solid var(--border); border-radius: 10px;
            font-family: 'DM Sans', sans-serif; font-size: 14px; color: var(--ink);
            background: var(--surface); outline: none;
            transition: border-color 0.15s;
        }
        .search-input:focus { border-color: var(--accent); }
        .search-input::placeholder { color: var(--ink-faint); }

        .filter-select {
            padding: 11px 16px; border-radius: 10px;
            border: 1.5px solid var(--border); background: var(--surface);
            font-family: 'DM Sans', sans-serif; font-size: 14px; color: var(--ink);
            outline: none; cursor: pointer; min-width: 140px;
            transition: border-color 0.15s;
        }
        .filter-select:focus { border-color: var(--accent); }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .course-card {
            background: var(--surface); border-radius: 14px;
            border: 1px solid var(--border); overflow: hidden;
            transition: all 0.2s ease;
        }
        .course-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.1); transform: translateY(-3px); }

        .course-thumb {
            height: 140px; display: flex; align-items: center; justify-content: center;
            font-size: 48px; position: relative; overflow: hidden;
        }

        .thumb-blue { background: linear-gradient(135deg, #dbeafe, #bfdbfe); }
        .thumb-teal { background: linear-gradient(135deg, #ccfbf1, #99f6e4); }
        .thumb-amber { background: linear-gradient(135deg, #fef3c7, #fde68a); }
        .thumb-purple { background: linear-gradient(135deg, #ede9fe, #ddd6fe); }
        .thumb-rose { background: linear-gradient(135deg, #ffe4e6, #fecdd3); }
        .thumb-green { background: linear-gradient(135deg, #dcfce7, #bbf7d0); }

        .course-body { padding: 20px; }

        .course-subject {
            font-size: 11px; font-weight: 600; letter-spacing: 1px;
            text-transform: uppercase; color: var(--accent);
            margin-bottom: 6px;
        }

        .course-title {
            font-family: 'Sora', sans-serif;
            font-size: 15px; font-weight: 600;
            color: var(--ink); margin-bottom: 6px; line-height: 1.4;
        }

        .course-teacher {
            font-size: 13px; color: var(--ink-faint);
            display: flex; align-items: center; gap: 5px;
            margin-bottom: 14px;
        }

        .course-footer {
            display: flex; align-items: center; justify-content: space-between;
            padding-top: 14px; border-top: 1px solid var(--surface-3);
        }

        .course-students {
            font-size: 12px; color: var(--ink-muted);
            display: flex; align-items: center; gap: 5px;
        }

        .course-badge {
            font-size: 11px; font-weight: 500;
            padding: 4px 10px; border-radius: 100px;
            background: var(--accent-light); color: var(--accent-dark);
        }

        .no-results {
            text-align: center; padding: 60px 20px; color: var(--ink-faint);
            display: none;
        }

        /* ── AUTH SECTION ── */
        .auth-section {
            padding: 80px 5%;
            background: var(--ink-2);
            position: relative; overflow: hidden;
        }

        .auth-section::before {
            content: '';
            position: absolute; top: -200px; right: -200px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(37,99,235,0.2) 0%, transparent 70%);
            pointer-events: none;
        }

        .auth-inner {
            max-width: 1200px; margin: 0 auto;
            display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
        }

        .auth-text h2 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(26px, 3vw, 38px); font-weight: 700;
            color: white; letter-spacing: -0.8px; margin-bottom: 14px;
        }

        .auth-text p { font-size: 16px; color: #94a3b8; font-weight: 300; line-height: 1.7; }

        .auth-features { margin-top: 28px; display: flex; flex-direction: column; gap: 14px; }

        .auth-feat-item { display: flex; align-items: center; gap: 12px; }

        .auth-feat-check {
            width: 22px; height: 22px; border-radius: 50%;
            background: rgba(37,99,235,0.2); border: 1px solid rgba(37,99,235,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; color: #93c5fd; flex-shrink: 0;
        }

        .auth-feat-item span { font-size: 14px; color: #cbd5e1; }

        .auth-cards { display: flex; flex-direction: column; gap: 16px; }

        .auth-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px; padding: 28px; position: relative;
            backdrop-filter: blur(10px);
        }

        .auth-card-featured {
            background: var(--surface);
            border: 1px solid var(--border);
        }

        .auth-card-badge {
            position: absolute; top: -10px; left: 20px;
            background: var(--accent); color: white;
            font-size: 11px; font-weight: 600; padding: 3px 12px; border-radius: 100px;
        }

        .auth-card h3 {
            font-family: 'Sora', sans-serif;
            font-size: 18px; font-weight: 600;
            margin-bottom: 6px;
        }

        .auth-card-featured h3 { color: var(--ink); }
        .auth-card:not(.auth-card-featured) h3 { color: white; }

        .auth-card p {
            font-size: 13px; margin-bottom: 18px;
        }

        .auth-card-featured p { color: var(--ink-muted); }
        .auth-card:not(.auth-card-featured) p { color: #94a3b8; }

        .auth-card-roles { display: flex; gap: 8px; margin-bottom: 18px; }

        .role-badge {
            padding: 5px 12px; border-radius: 8px;
            font-size: 12px; font-weight: 500;
        }

        .role-student { background: var(--accent-light); color: var(--accent-dark); }
        .role-teacher { background: var(--teal-light); color: #0f766e; }

        .auth-card-btns { display: flex; gap: 10px; }

        .btn-card-primary {
            flex: 1; padding: 10px 16px; border-radius: 8px;
            background: var(--accent); color: white;
            font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500;
            text-decoration: none; border: none; cursor: pointer;
            text-align: center; transition: all 0.15s ease;
        }
        .btn-card-primary:hover { background: var(--accent-dark); }

        .btn-card-outline {
            flex: 1; padding: 10px 16px; border-radius: 8px;
            background: transparent; color: var(--ink);
            font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500;
            text-decoration: none; border: 1.5px solid var(--border); cursor: pointer;
            text-align: center; transition: all 0.15s ease;
        }
        .btn-card-outline:hover { background: var(--surface-2); }

        .btn-card-outline-dark {
            flex: 1; padding: 10px 16px; border-radius: 8px;
            background: transparent; color: rgba(255,255,255,0.8);
            font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500;
            text-decoration: none; border: 1.5px solid rgba(255,255,255,0.2); cursor: pointer;
            text-align: center; transition: all 0.15s ease;
        }
        .btn-card-outline-dark:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.4); }

        /* ── FOOTER ── */
        footer {
            background: var(--ink); color: #64748b;
            padding: 32px 5%;
            text-align: center; font-size: 13px;
        }

        footer a { color: #94a3b8; text-decoration: none; }
        footer a:hover { color: white; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            nav { padding: 0 4%; }
            .nav-name { display: none; }
            .hero { padding: 110px 5% 60px; }
            .hero-stats { gap: 20px; }
            .auth-inner { grid-template-columns: 1fr; gap: 36px; }
            .auth-text { text-align: center; }
            .auth-features { align-items: center; }
        }

        /* scroll animate */
        .fade-up { opacity: 0; transform: translateY(20px); transition: opacity 0.5s ease, transform 0.5s ease; }
        .fade-up.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>
<body>

    {{-- ── NAVBAR ── --}}
    <nav>
        <a href="#" class="nav-brand">
            <div class="nav-logo">LMS</div>
            <span class="nav-name">DE-LMS</span>
        </a>
        <div class="nav-links">
            <a href="#courses" class="btn-ghost">Courses</a>
            <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-primary">Daftar Gratis</a>
            @endif
        </div>
    </nav>

    {{-- ── HERO ── --}}
    <section class="hero">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Platform Belajar Online
        </div>
        <h1>Belajar lebih <span>efektif</span>,<br>raih hasil lebih baik.</h1>
        <p class="hero-desc">
            DE-LMS menghubungkan siswa dan guru dalam satu platform terintegrasi — kuis interaktif, materi terstruktur, dan pemantauan kemajuan real-time.
        </p>
        <div class="hero-cta">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-primary-lg">
                    Mulai Belajar →
                </a>
            @endif
            <a href="#courses" class="btn-outline-lg">
                Jelajahi Kursus
            </a>
        </div>
        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-num">3 Peran</div>
                <div class="stat-label">Admin · Guru · Siswa</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">Multi Course</div>
                <div class="stat-label">Kursus terstruktur</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">Kuis AI</div>
                <div class="stat-label">Penilaian otomatis</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">Real-time</div>
                <div class="stat-label">Pantau kemajuan</div>
            </div>
        </div>
    </section>

    {{-- ── FEATURES ── --}}
    <div class="section fade-up">
        <div class="section-label">Fitur Unggulan</div>
        <h2 class="section-title">Semua yang kamu butuhkan<br>dalam satu platform</h2>
        <p class="section-desc">Dirancang untuk pengalaman belajar yang menyenangkan, terukur, dan efisien bagi seluruh ekosistem pendidikan.</p>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon icon-blue">📚</div>
                <h3>Materi Terstruktur</h3>
                <p>Kursus tersusun dalam modul-modul dengan materi dokumen, video, dan tugas yang terorganisir rapi.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon icon-teal">🧠</div>
                <h3>Kuis Interaktif</h3>
                <p>Buat dan ikuti kuis dengan penilaian otomatis. Guru bisa melihat hasil dan analisis per siswa secara langsung.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon icon-amber">📊</div>
                <h3>Pantau Kemajuan</h3>
                <p>Dashboard lengkap untuk guru memantau aktivitas, nilai, dan progres belajar setiap siswa di kelasnya.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon icon-teal">📝</div>
                <h3>Tugas & Pengumpulan</h3>
                <p>Buat assignment dengan tenggat waktu, siswa mengumpulkan secara online, guru memberi nilai dan feedback.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon icon-amber">🔒</div>
                <h3>Akses Berbasis Peran</h3>
                <p>Sistem role Admin, Guru, dan Siswa dengan izin akses yang tepat untuk masing-masing pengguna.</p>
            </div>
        </div>
    </div>

    {{-- ── COURSES ── --}}
    <section class="courses-wrap" id="courses">
        <div class="courses-inner">
            <div class="fade-up">
                <div class="section-label">Kursus Tersedia</div>
                <h2 class="section-title">Jelajahi kursus kami</h2>
                <p class="section-desc">Temukan kursus yang sesuai minat dan kebutuhanmu. Bergabung dan mulai belajar hari ini.</p>
            </div>

            <div class="search-bar fade-up">
                <div class="search-input-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-input" id="searchInput" placeholder="Cari nama kursus atau guru...">
                </div>
                <select class="filter-select" id="filterSubject">
                    <option value="">Semua Mata Pelajaran</option>
                    <option value="Matematika">Matematika</option>
                    <option value="Fisika">Fisika</option>
                    <option value="Bahasa">Bahasa</option>
                    <option value="Pemrograman">Pemrograman</option>
                    <option value="Sains">Sains</option>
                    <option value="IPS">IPS</option>
                </select>
            </div>

            <div class="courses-grid" id="coursesGrid">

                <div class="course-card" data-title="Aljabar Lanjutan" data-teacher="Bu Sari Dewi" data-subject="Matematika">
                    <div class="course-thumb thumb-blue">🔢</div>
                    <div class="course-body">
                        <div class="course-subject">Matematika</div>
                        <div class="course-title">Aljabar Lanjutan</div>
                        <div class="course-teacher">👤 Bu Sari Dewi</div>
                        <div class="course-footer">
                            <span class="course-students">👥 32 siswa</span>
                            <span class="course-badge">Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="course-card" data-title="Fisika Dasar" data-teacher="Pak Andi Susanto" data-subject="Fisika">
                    <div class="course-thumb thumb-teal">⚛️</div>
                    <div class="course-body">
                        <div class="course-subject">Fisika</div>
                        <div class="course-title">Fisika Dasar</div>
                        <div class="course-teacher">👤 Pak Andi Susanto</div>
                        <div class="course-footer">
                            <span class="course-students">👥 28 siswa</span>
                            <span class="course-badge">Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="course-card" data-title="Pemrograman Web" data-teacher="Pak Budi Rahman" data-subject="Pemrograman">
                    <div class="course-thumb thumb-purple">💻</div>
                    <div class="course-body">
                        <div class="course-subject">Pemrograman</div>
                        <div class="course-title">Pemrograman Web</div>
                        <div class="course-teacher">👤 Pak Budi Rahman</div>
                        <div class="course-footer">
                            <span class="course-students">👥 45 siswa</span>
                            <span class="course-badge">Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="course-card" data-title="Bahasa Inggris B2" data-teacher="Ms. Linda Carter" data-subject="Bahasa">
                    <div class="course-thumb thumb-rose">🌐</div>
                    <div class="course-body">
                        <div class="course-subject">Bahasa</div>
                        <div class="course-title">Bahasa Inggris B2</div>
                        <div class="course-teacher">👤 Ms. Linda Carter</div>
                        <div class="course-footer">
                            <span class="course-students">👥 38 siswa</span>
                            <span class="course-badge">Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="course-card" data-title="Biologi Sel" data-teacher="Bu Rina Astuti" data-subject="Sains">
                    <div class="course-thumb thumb-green">🔬</div>
                    <div class="course-body">
                        <div class="course-subject">Sains</div>
                        <div class="course-title">Biologi Sel</div>
                        <div class="course-teacher">👤 Bu Rina Astuti</div>
                        <div class="course-footer">
                            <span class="course-students">👥 24 siswa</span>
                            <span class="course-badge">Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="course-card" data-title="Sejarah Indonesia" data-teacher="Pak Hendra Wijaya" data-subject="IPS">
                    <div class="course-thumb thumb-amber">📜</div>
                    <div class="course-body">
                        <div class="course-subject">IPS</div>
                        <div class="course-title">Sejarah Indonesia</div>
                        <div class="course-teacher">👤 Pak Hendra Wijaya</div>
                        <div class="course-footer">
                            <span class="course-students">👥 31 siswa</span>
                            <span class="course-badge">Aktif</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="no-results" id="noResults">
                😕 Tidak ada kursus yang sesuai pencarian.
            </div>
        </div>
    </section>

    {{-- ── AUTH SECTION ── --}}
    <section class="auth-section fade-up">
        <div class="auth-inner">
            <div class="auth-text">
                <div class="section-label" style="color:#93c5fd;">Bergabung Sekarang</div>
                <h2>Siap memulai<br>perjalanan belajarmu?</h2>
                <p>Daftar gratis dalam hitungan detik. Pilih peranmu dan langsung mulai belajar atau mengajar.</p>
                <div class="auth-features">
                    <div class="auth-feat-item">
                        <div class="auth-feat-check">✓</div>
                        <span>Akses semua kursus yang tersedia</span>
                    </div>
                    <div class="auth-feat-item">
                        <div class="auth-feat-check">✓</div>
                        <span>Kuis dan tugas dengan penilaian otomatis</span>
                    </div>
                    <div class="auth-feat-item">
                        <div class="auth-feat-check">✓</div>
                        <span>Pantau kemajuan belajarmu secara real-time</span>
                    </div>
                    <div class="auth-feat-item">
                        <div class="auth-feat-check">✓</div>
                        <span>Diskusi langsung dengan guru dan teman</span>
                    </div>
                </div>
            </div>

            <div class="auth-cards">
                {{-- Card Daftar (featured) --}}
                <div class="auth-card auth-card-featured">
                    <div class="auth-card-badge">Baru di sini?</div>
                    <h3>Buat Akun Gratis</h3>
                    <p>Daftar sebagai siswa atau guru dan mulai dalam 2 menit.</p>
                    <div class="auth-card-roles">
                        <span class="role-badge role-student">👤 Siswa</span>
                        <span class="role-badge role-teacher">🎓 Guru</span>
                    </div>
                    <div class="auth-card-btns">
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-card-primary">Daftar Sekarang →</a>
                        @endif
                    </div>
                </div>

                {{-- Card Login --}}
                <div class="auth-card">
                    <h3>Sudah Punya Akun?</h3>
                    <p>Masuk dan lanjutkan belajarmu dari mana saja.</p>
                    <div class="auth-card-btns">
                        <a href="{{ route('login') }}" class="btn-card-outline-dark">Masuk →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── FOOTER ── --}}
    <footer>
        <p>© {{ date('Y') }} DE-LMS — Learning Management System. Dibuat dengan ❤️ untuk pendidikan Indonesia.</p>
    </footer>

    <script>
        // Search & filter
        const searchInput = document.getElementById('searchInput');
        const filterSubject = document.getElementById('filterSubject');
        const cards = document.querySelectorAll('.course-card');
        const noResults = document.getElementById('noResults');

        function filterCourses() {
            const q = searchInput.value.toLowerCase();
            const subject = filterSubject.value.toLowerCase();
            let visible = 0;

            cards.forEach(card => {
                const title = card.dataset.title.toLowerCase();
                const teacher = card.dataset.teacher.toLowerCase();
                const cardSubject = card.dataset.subject.toLowerCase();

                const matchQ = !q || title.includes(q) || teacher.includes(q);
                const matchSubject = !subject || cardSubject === subject;

                if (matchQ && matchSubject) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });

            noResults.style.display = visible === 0 ? 'block' : 'none';
        }

        searchInput.addEventListener('input', filterCourses);
        filterSubject.addEventListener('change', filterCourses);

        // Scroll reveal
        const observer = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.1 });

        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
    </script>

</body>
</html>