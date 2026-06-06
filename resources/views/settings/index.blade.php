<x-app-layout>
<style>
.tab-btn { padding:10px 20px; font-size:14px; font-weight:600; cursor:pointer; border:none; background:none; color:#64748b; border-bottom:2px solid transparent; transition:all 0.2s; }
.tab-btn.active { color:#3b5bdb; border-bottom-color:#3b5bdb; }
.tab-content { display:none; }
.tab-content.active { display:block; }
.field-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.field-input { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none; box-sizing:border-box; transition:border-color 0.15s; }
.field-input:focus { border-color:#3b5bdb; }
.btn-save { padding:11px 28px; background:#3b5bdb; color:white; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; transition:background 0.15s; }
.btn-save:hover { background:#2d45ba; }
</style>

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 style="font-size:22px; font-weight:800; color:#1e293b; margin:0 0 24px;">Pengaturan Akun</h1>

    @if(session('success'))
        <div style="margin-bottom:20px; padding:12px 16px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; color:#16a34a; font-weight:600; font-size:13px;">✓ {{ session('success') }}</div>
    @endif

    {{-- Profile Card --}}
    <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; margin-bottom:20px; overflow:hidden;">
        {{-- User identity header --}}
        <div style="padding:22px 24px; background:linear-gradient(135deg,#3b5bdb,#6366f1); display:flex; align-items:center; gap:16px;">
            <div style="width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,0.25); display:flex; align-items:center; justify-content:center; color:white; font-size:20px; font-weight:800; flex-shrink:0;">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            <div>
                <div style="font-size:17px; font-weight:700; color:white;">{{ $user->name }}</div>
                <div style="font-size:13px; color:rgba(255,255,255,0.75);">{{ $user->email }} · <span style="background:rgba(255,255,255,0.2); padding:2px 10px; border-radius:100px; font-size:11px; font-weight:700;">{{ ucfirst($user->role) }}</span></div>
            </div>
        </div>

        {{-- Tabs --}}
        <div style="display:flex; border-bottom:1px solid #e2e8f0; padding:0 16px;">
            <button class="tab-btn {{ !session('tab') || session('tab')=='profile' ? 'active' : '' }}" onclick="switchTab(event,'profile')">Profil</button>
            <button class="tab-btn {{ session('tab')=='password' ? 'active' : '' }}" onclick="switchTab(event,'password')">Password</button>
            <button class="tab-btn {{ session('tab')=='danger' ? 'active' : '' }}" onclick="switchTab(event,'danger')">Hapus Akun</button>
        </div>

        {{-- TAB: Profil --}}
        <div id="tab-profile" class="tab-content {{ !session('tab') || session('tab')=='profile' ? 'active' : '' }}" style="padding:24px;">
            <form method="POST" action="{{ route('settings.profile') }}">
                @csrf @method('PATCH')
                <div style="margin-bottom:16px;">
                    <label class="field-label">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="field-input">
                    @error('name')<p style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:20px;">
                    <label class="field-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="field-input">
                    @error('email')<p style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:16px;">
                    <label class="field-label">Role</label>
                    <input type="text" value="{{ ucfirst($user->role) }}" class="field-input" readonly style="background:#f8fafc; color:#64748b;">
                    <p style="font-size:12px; color:#94a3b8; margin-top:4px;">Role hanya dapat diubah oleh Admin.</p>
                </div>
                <button type="submit" class="btn-save">Simpan Perubahan</button>
            </form>
        </div>

        {{-- TAB: Password --}}
        <div id="tab-password" class="tab-content {{ session('tab')=='password' ? 'active' : '' }}" style="padding:24px;">
            <form method="POST" action="{{ route('settings.password') }}">
                @csrf @method('PATCH')
                <div style="margin-bottom:16px;">
                    <label class="field-label">Password Saat Ini</label>
                    <input type="password" name="current_password" class="field-input">
                    @error('current_password')<p style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:16px;">
                    <label class="field-label">Password Baru</label>
                    <input type="password" name="password" class="field-input">
                    @error('password')<p style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div style="margin-bottom:24px;">
                    <label class="field-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="field-input">
                </div>
                <button type="submit" class="btn-save">Perbarui Password</button>
            </form>
        </div>

        {{-- TAB: Danger --}}
        <div id="tab-danger" class="tab-content {{ session('tab')=='danger' ? 'active' : '' }}" style="padding:24px;">
            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:16px 18px; margin-bottom:20px;">
                <div style="font-size:14px; font-weight:700; color:#dc2626; margin-bottom:4px;">⚠️ Zona Berbahaya</div>
                <p style="font-size:13px; color:#991b1b; margin:0; line-height:1.6;">Menghapus akun bersifat permanen. Semua data termasuk progress belajar, kursus, dan submission akan hilang selamanya.</p>
            </div>
            <form method="POST" action="{{ route('settings.delete') }}"
                  onsubmit="return confirm('Yakin ingin menghapus akun ini secara permanen? Tindakan ini TIDAK bisa dibatalkan.')">
                @csrf @method('DELETE')
                <div style="margin-bottom:16px;">
                    <label class="field-label">Konfirmasi Password untuk Hapus Akun</label>
                    <input type="password" name="password" class="field-input" style="border-color:#fca5a5;">
                    @error('password')<p style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <button type="submit" style="padding:11px 24px; background:#dc2626; color:white; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer;">Hapus Akun Selamanya</button>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(e, name) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    e.target.classList.add('active');
    document.getElementById('tab-' + name).classList.add('active');
}
</script>
</x-app-layout>
