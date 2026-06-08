<x-app-layout>
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('admin.users.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:13px; text-decoration:none; margin-bottom:20px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>Kembali
    </a>
    <div style="background:white; border:1px solid #e2e8f0; border-radius:16px; padding:32px;">
        <h1 style="font-size:20px; font-weight:800; color:#1e293b; margin:0 0 24px;">Edit User: {{ $user->name }}</h1>
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PATCH')
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none;">
                @error('name')<p style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none;">
                @error('email')<p style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Role</label>
                <select name="role" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none; background:white;">
                    <option value="student" @selected($user->role=='student')>Siswa</option>
                    <option value="teacher" @selected($user->role=='teacher')>Guru</option>
                    <option value="admin"   @selected($user->role=='admin')>Admin</option>
                </select>
            </div>
            <div style="margin-bottom:8px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Password Baru <span style="color:#94a3b8; font-weight:400;">(kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none;">
            </div>
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" style="width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none;">
            </div>
            <button type="submit" style="width:100%; padding:12px; background:#3b5bdb; color:white; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer;">Simpan Perubahan</button>
        </form>
    </div>
</div>
</x-app-layout>