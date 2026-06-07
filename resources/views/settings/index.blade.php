<x-app-layout>
<style>
.st-wrap      { max-width:860px; margin:0 auto; padding:28px 24px; }
.tab-btn      { padding:10px 20px; font-size:13px; font-weight:600; cursor:pointer;
                border:none; background:none; color:#64748b;
                border-bottom:2px solid transparent; transition:all .2s; white-space:nowrap; }
.tab-btn.active { color:#3b5bdb; border-bottom-color:#3b5bdb; }
.tab-content  { display:none; }
.tab-content.active { display:block; }

.fl           { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.fi           { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px;
                font-size:14px; outline:none; box-sizing:border-box; transition:border-color .15s; }
.fi:focus     { border-color:#3b5bdb; }
.fi[readonly] { background:#f8fafc; color:#64748b; }

.btn-save     { padding:10px 26px; background:#3b5bdb; color:white; border:none;
                border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; }
.btn-save:hover { background:#2d45ba; }
.btn-danger   { padding:10px 24px; background:#dc2626; color:white; border:none;
                border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; }

.st-card      { background:white; border:1px solid #e2e8f0; border-radius:16px;
                margin-bottom:20px; overflow:hidden; }
.st-section   { padding:22px 24px; border-bottom:1px solid #f1f5f9; }
.st-section:last-child { border-bottom:none; }
.sec-title    { font-size:14px; font-weight:700; color:#1e293b; margin:0 0 4px; }
.sec-desc     { font-size:12px; color:#94a3b8; margin:0 0 16px; }
.row2         { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@media(max-width:600px){ .row2{ grid-template-columns:1fr; } }

.toggle-wrap  { display:flex; align-items:center; gap:10px; }
.toggle-lbl   { font-size:13px; color:#374151; font-weight:600; }
.color-preview{ width:36px; height:36px; border-radius:8px; border:2px solid #e2e8f0;
                display:inline-block; vertical-align:middle; margin-left:8px; transition:background .2s; }

/* API key mask */
.api-key-wrap { position:relative; }
.api-key-wrap .fi { padding-right:48px; font-family:monospace; }
.toggle-vis   { position:absolute; right:12px; top:50%; transform:translateY(-50%);
                background:none; border:none; cursor:pointer; color:#94a3b8; padding:0; }
</style>

<div class="st-wrap">
    <h1 style="font-size:21px; font-weight:800; color:#1e293b; margin:0 0 22px;">Pengaturan</h1>

    @if(session('success'))
    <div style="margin-bottom:20px; padding:12px 16px; background:#f0fdf4; border:1px solid #bbf7d0;
                border-radius:10px; color:#16a34a; font-weight:600; font-size:13px;">
        ✓ {{ session('success') }}
    </div>
    @endif

    <div class="st-card">
        {{-- User header --}}
        <div style="padding:22px 24px; background:linear-gradient(135deg,#3b5bdb,#6366f1);
                    display:flex; align-items:center; gap:16px;">
            <div style="width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,.25);
                        display:flex; align-items:center; justify-content:center;
                        color:white; font-size:20px; font-weight:800; flex-shrink:0;">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            <div>
                <div style="font-size:17px; font-weight:700; color:white;">{{ $user->name }}</div>
                <div style="font-size:13px; color:rgba(255,255,255,.75);">
                    {{ $user->email }} ·
                    <span style="background:rgba(255,255,255,.2); padding:2px 10px;
                                 border-radius:100px; font-size:11px; font-weight:700;">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Tab bar --}}
        <div style="display:flex; border-bottom:1px solid #e2e8f0; padding:0 16px; overflow-x:auto;">
            @php
                $activeTab = session('tab', 'profile');
                $tabs = [['profile','Profil'],['password','Password'],['danger','Hapus Akun']];
                if($user->role === 'admin') {
                    $tabs = array_merge($tabs,[
                        ['api','API & AI'],
                        ['theme','Tema'],
                        ['landingpage','Landing Page'],
                        ['certificate','Sertifikat'],
                    ]);
                }
            @endphp
            @foreach($tabs as [$id,$label])
            <button class="tab-btn {{ $activeTab === $id ? 'active' : '' }}"
                    onclick="switchTab(event,'{{ $id }}')">{{ $label }}</button>
            @endforeach
        </div>

        {{-- TAB: Profil --}}
        <div id="tab-profile" class="tab-content {{ $activeTab==='profile' ? 'active' : '' }}"
             style="padding:24px;">
            <form method="POST" action="{{ route('settings.profile') }}">
                @csrf @method('PATCH')
                <div style="margin-bottom:16px;">
                    <label class="fl">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name',$user->name) }}" class="fi">
                    @error('name')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:16px;">
                    <label class="fl">Email</label>
                    <input type="email" name="email" value="{{ old('email',$user->email) }}" class="fi">
                    @error('email')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:20px;">
                    <label class="fl">Role</label>
                    <input type="text" value="{{ ucfirst($user->role) }}" class="fi" readonly>
                    <p style="font-size:12px;color:#94a3b8;margin-top:4px;">Role hanya dapat diubah oleh Admin.</p>
                </div>
                <button type="submit" class="btn-save">Simpan Perubahan</button>
            </form>
        </div>

        {{-- TAB: Password --}}
        <div id="tab-password" class="tab-content {{ $activeTab==='password' ? 'active' : '' }}"
             style="padding:24px;">
            <form method="POST" action="{{ route('settings.password') }}">
                @csrf @method('PATCH')
                <div style="margin-bottom:16px;">
                    <label class="fl">Password Saat Ini</label>
                    <input type="password" name="current_password" class="fi">
                    @error('current_password')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:16px;">
                    <label class="fl">Password Baru</label>
                    <input type="password" name="password" class="fi">
                    @error('password')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:24px;">
                    <label class="fl">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="fi">
                </div>
                <button type="submit" class="btn-save">Perbarui Password</button>
            </form>
        </div>

        {{-- TAB: Danger --}}
        <div id="tab-danger" class="tab-content {{ $activeTab==='danger' ? 'active' : '' }}"
             style="padding:24px;">
            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px;
                        padding:16px 18px; margin-bottom:20px;">
                <div style="font-size:14px; font-weight:700; color:#dc2626; margin-bottom:4px;">Zona Berbahaya</div>
                <p style="font-size:13px; color:#991b1b; margin:0; line-height:1.6;">
                    Menghapus akun bersifat permanen. Semua data termasuk progress, kursus,
                    dan submission akan hilang selamanya.
                </p>
            </div>
            <form method="POST" action="{{ route('settings.delete') }}"
                  onsubmit="return confirm('Yakin ingin menghapus akun ini secara permanen?')">
                @csrf @method('DELETE')
                <div style="margin-bottom:16px;">
                    <label class="fl">Konfirmasi Password</label>
                    <input type="password" name="password" class="fi" style="border-color:#fca5a5;">
                    @error('password')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-danger">Hapus Akun Selamanya</button>
            </form>
        </div>

        @if($user->role === 'admin')

        {{-- TAB: API & AI --}}
        <div id="tab-api" class="tab-content {{ $activeTab==='api' ? 'active' : '' }}"
             style="padding:24px;">
            <p class="sec-title">Pengaturan AI</p>
            <p class="sec-desc">Konfigurasi provider dan API key untuk fitur rekomendasi AI.</p>
            <form method="POST" action="{{ route('settings.api') }}">
                @csrf @method('PATCH')
                <div style="margin-bottom:16px;">
                    <label class="fl">Provider AI</label>
                    <select name="ai_provider" class="fi">
                        <option value="anthropic" {{ ($appSettings['ai_provider']??'') === 'anthropic' ? 'selected' : '' }}>Anthropic (Claude) — Berbayar</option>
                        <option value="gemini"    {{ ($appSettings['ai_provider']??'') === 'gemini'    ? 'selected' : '' }}>Google Gemini — Gratis</option>
                        <option value="groq"      {{ ($appSettings['ai_provider']??'') === 'groq'      ? 'selected' : '' }}>Groq (Llama) — Gratis</option>
                        <option value="openai"    {{ ($appSettings['ai_provider']??'') === 'openai'    ? 'selected' : '' }}>OpenAI (GPT) — Berbayar</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label class="fl">API Key</label>
                    <div class="api-key-wrap">
                        <input type="text" name="ai_api_key" id="apiKeyInput"
                               value="{{ $appSettings['ai_api_key'] ?? '' }}"
                               class="fi" placeholder="sk-ant-... atau AIzaSy..."
                               autocomplete="off"
                               style="font-family:monospace; -webkit-text-security:disc;"
                               onfocus="this.style.webkitTextSecurity='none'"
                               onblur="this.style.webkitTextSecurity='disc'">
                        <button type="button" class="toggle-vis" onclick="toggleApiKey()" title="Tampilkan/sembunyikan">
                            <svg id="eyeIcon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('ai_api_key')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    <p style="font-size:12px;color:#94a3b8;margin-top:4px;">
                        API key disimpan terenkripsi di database, tidak hardcode di kode.
                    </p>
                </div>
                <div style="margin-bottom:20px;">
                    <label class="fl">Model AI</label>
                    <input type="text" name="ai_model"
                           value="{{ $appSettings['ai_model'] ?? 'claude-sonnet-4-6' }}"
                           class="fi" placeholder="claude-sonnet-4-6">
                    <p style="font-size:12px;color:#94a3b8;margin-top:4px;">
                        Contoh: <code>claude-sonnet-4-6</code>, <code>gpt-4o</code>
                    </p>
                </div>
                <button type="submit" class="btn-save">Simpan API Setting</button>
            </form>
        </div>

        {{-- TAB: Tema --}}
        <div id="tab-theme" class="tab-content {{ $activeTab==='theme' ? 'active' : '' }}"
             style="padding:24px;">
            <p class="sec-title">Tampilan Platform</p>
            <p class="sec-desc">Atur warna utama dan mode tampilan dashboard.</p>
            <form method="POST" action="{{ route('settings.theme') }}">
                @csrf @method('PATCH')
                <div class="row2" style="margin-bottom:16px;">
                    <div>
                        <label class="fl">Warna Utama</label>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="color" name="theme_color" id="colorPicker"
                                   value="{{ $appSettings['theme_color'] ?? '#3b5bdb' }}"
                                   style="width:48px; height:38px; border:1.5px solid #e2e8f0;
                                          border-radius:8px; cursor:pointer; padding:2px;"
                                   oninput="document.getElementById('colorHex').value=this.value;
                                            document.getElementById('colorPreviewBox').style.background=this.value;">
                            <input type="text" id="colorHex"
                                   value="{{ $appSettings['theme_color'] ?? '#3b5bdb' }}"
                                   class="fi" style="font-family:monospace; max-width:120px;"
                                   oninput="document.getElementById('colorPicker').value=this.value;
                                            document.getElementById('colorPreviewBox').style.background=this.value;">
                            <div id="colorPreviewBox" class="color-preview"
                                 style="background:{{ $appSettings['theme_color'] ?? '#3b5bdb' }};"></div>
                        </div>
                        @error('theme_color')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="fl">Mode Tampilan</label>
                        <select name="theme_mode" class="fi">
                            <option value="light" {{ ($appSettings['theme_mode']??'light') === 'light' ? 'selected' : '' }}>Light Mode</option>
                            <option value="dark"  {{ ($appSettings['theme_mode']??'light') === 'dark'  ? 'selected' : '' }}>Dark Mode</option>
                        </select>
                        <p style="font-size:12px;color:#94a3b8;margin-top:4px;">
                            Dark mode akan diterapkan pada seluruh halaman.
                        </p>
                    </div>
                </div>
                <button type="submit" class="btn-save">Simpan Tema</button>
            </form>
        </div>

        {{-- TAB: Landing Page --}}
        <div id="tab-landingpage" class="tab-content {{ $activeTab==='landingpage' ? 'active' : '' }}"
             style="padding:24px;">
            <p class="sec-title">Konten Landing Page</p>
            <p class="sec-desc">Atur teks dan konten yang tampil di halaman utama platform.</p>
            <form method="POST" action="{{ route('settings.landingpage') }}">
                @csrf @method('PATCH')
                <div style="margin-bottom:16px;">
                    <label class="fl">Judul Platform</label>
                    <input type="text" name="lp_title"
                           value="{{ old('lp_title', $appSettings['lp_title'] ?? '') }}"
                           class="fi" placeholder="Nama platform Anda">
                    @error('lp_title')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:16px;">
                    <label class="fl">Subtitle / Deskripsi Singkat</label>
                    <textarea name="lp_subtitle" rows="3" class="fi"
                              style="resize:vertical;" placeholder="Deskripsi singkat platform...">{{ old('lp_subtitle', $appSettings['lp_subtitle'] ?? '') }}</textarea>
                </div>
                <div style="margin-bottom:20px;">
                    <label class="toggle-wrap" style="cursor:pointer;">
                        <input type="checkbox" name="lp_show_courses" value="1"
                               {{ ($appSettings['lp_show_courses'] ?? '1') === '1' ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:#3b5bdb;">
                        <span class="toggle-lbl">Tampilkan daftar kursus di landing page</span>
                    </label>
                </div>
                <button type="submit" class="btn-save">Simpan Landing Page</button>
            </form>
        </div>

        {{-- TAB: Sertifikat --}}
        <div id="tab-certificate" class="tab-content {{ $activeTab==='certificate' ? 'active' : '' }}"
             style="padding:24px;">
            <p class="sec-title">Pengaturan Sertifikat</p>
            <p class="sec-desc">Konfigurasi template sertifikat yang di-generate otomatis setelah course selesai.</p>
            <form method="POST" action="{{ route('settings.certificate') }}">
                @csrf @method('PATCH')
                <div style="margin-bottom:16px;">
                    <label class="toggle-wrap" style="cursor:pointer; margin-bottom:16px;">
                        <input type="checkbox" name="cert_enabled" value="1"
                               id="certEnabled"
                               {{ ($appSettings['cert_enabled'] ?? '0') === '1' ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:#3b5bdb;"
                               onchange="document.getElementById('certFields').style.opacity=this.checked?'1':'.5'">
                        <span class="toggle-lbl">Aktifkan fitur sertifikat otomatis</span>
                    </label>
                </div>
                <div id="certFields" style="opacity:{{ ($appSettings['cert_enabled'] ?? '0') === '1' ? '1' : '.5' }}; transition:opacity .2s;">
                    <div style="margin-bottom:16px;">
                        <label class="fl">Nama Penerbit Sertifikat</label>
                        <input type="text" name="cert_issuer_name"
                               value="{{ old('cert_issuer_name', $appSettings['cert_issuer_name'] ?? '') }}"
                               class="fi" placeholder="Nama lembaga / platform">
                        @error('cert_issuer_name')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div style="margin-bottom:20px;">
                        <label class="fl">Teks Footer Sertifikat</label>
                        <textarea name="cert_footer_text" rows="2" class="fi"
                                  style="resize:vertical;" placeholder="Teks di bagian bawah sertifikat...">{{ old('cert_footer_text', $appSettings['cert_footer_text'] ?? '') }}</textarea>
                    </div>
                    {{-- Preview sertifikat sederhana --}}
                    <div style="border:2px solid #c7d2fe; border-radius:12px; padding:24px; text-align:center;
                                background:linear-gradient(135deg,#eff6ff,#f5f3ff); margin-bottom:20px;">
                        <div style="font-size:11px; font-weight:700; color:#6366f1; letter-spacing:2px;
                                    text-transform:uppercase; margin-bottom:8px;">Sertifikat Penyelesaian</div>
                        <div style="font-size:18px; font-weight:800; color:#1e293b; margin-bottom:4px;">Nama Siswa</div>
                        <div style="font-size:13px; color:#64748b; margin-bottom:12px;">
                            telah menyelesaikan kursus <strong>[Nama Kursus]</strong>
                        </div>
                        <div style="font-size:11px; color:#94a3b8; border-top:1px solid #c7d2fe;
                                    padding-top:10px; margin-top:4px;" id="certIssuerPreview">
                            {{ $appSettings['cert_issuer_name'] ?? 'DE-LMS' }}
                        </div>
                        <div style="font-size:11px; color:#94a3b8; margin-top:4px;" id="certFooterPreview">
                            {{ $appSettings['cert_footer_text'] ?? '' }}
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-save">Simpan Setting Sertifikat</button>
            </form>
        </div>

        @endif {{-- end admin only --}}
    </div>
</div>

<script>
function switchTab(e, name) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    e.target.classList.add('active');
    document.getElementById('tab-' + name).classList.add('active');
}

function toggleApiKey() {
    const input = document.getElementById('apiKeyInput');
    const isHidden = input.style.webkitTextSecurity === 'disc';
    input.style.webkitTextSecurity = isHidden ? 'none' : 'disc';
}

// Live preview sertifikat
const issuerInput = document.querySelector('[name="cert_issuer_name"]');
const footerInput = document.querySelector('[name="cert_footer_text"]');
if (issuerInput) {
    issuerInput.addEventListener('input', () => {
        const el = document.getElementById('certIssuerPreview');
        if (el) el.textContent = issuerInput.value || 'DE-LMS';
    });
}
if (footerInput) {
    footerInput.addEventListener('input', () => {
        const el = document.getElementById('certFooterPreview');
        if (el) el.textContent = footerInput.value;
    });
}
</script>
</x-app-layout>