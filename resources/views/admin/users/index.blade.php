<x-app-layout>
<style>
.badge { display:inline-block; padding:3px 10px; border-radius:100px; font-size:11px; font-weight:700; }
.badge-admin   { background:#fee2e2; color:#991b1b; }
.badge-teacher { background:#dbeafe; color:#1e40af; }
.badge-student { background:#dcfce7; color:#166534; }
.action-btn { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; border:none; cursor:pointer; transition:all 0.15s; }
</style>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:28px;">
        <div>
            <h1 style="font-size:22px; font-weight:800; color:#1e293b; margin:0 0 2px;">Manajemen User</h1>
            <p style="font-size:13px; color:#64748b; margin:0;">Kelola seluruh pengguna platform</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           style="display:inline-flex; align-items:center; gap:6px; background:#3b5bdb; color:white; padding:10px 20px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah User
        </a>
    </div>

    @if(session('success'))
        <div style="margin-bottom:18px; padding:12px 16px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; color:#16a34a; font-weight:600; font-size:13px;">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="margin-bottom:18px; padding:12px 16px; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; color:#dc2626; font-weight:600; font-size:13px;">✗ {{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(120px, 1fr)); gap:14px; margin-bottom:24px;">
        @foreach([['Total', $stats['total'], '#3b5bdb'],['Admin', $stats['admin'], '#dc2626'],['Guru', $stats['teacher'], '#1d4ed8'],['Siswa', $stats['student'], '#16a34a']] as [$label, $val, $color])
        <div style="background:white; border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px;">
            <div style="font-size:26px; font-weight:800; color:{{ $color }};">{{ $val }}</div>
            <div style="font-size:12px; font-weight:600; color:#94a3b8; text-transform:uppercase; margin-top:2px;">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
        <div style="position:relative; flex:1; min-width:200px;">
            <svg width="14" height="14" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24" style="position:absolute; left:12px; top:50%; transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                   style="width:100%; padding:9px 12px 9px 34px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13px; outline:none;" />
        </div>
        <select name="role" style="padding:9px 14px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13px; outline:none; background:white;">
            <option value="">Semua Role</option>
            <option value="admin"   @selected(request('role')=='admin')>Admin</option>
            <option value="teacher" @selected(request('role')=='teacher')>Guru</option>
            <option value="student" @selected(request('role')=='student')>Siswa</option>
        </select>
        <button type="submit" style="padding:9px 18px; background:#3b5bdb; color:white; border:none; border-radius:9px; font-size:13px; font-weight:600; cursor:pointer;">Filter</button>
        @if(request()->hasAny(['search','role']))
            <a href="{{ route('admin.users.index') }}" style="padding:9px 14px; border:1px solid #e2e8f0; border-radius:9px; font-size:13px; color:#64748b; text-decoration:none;">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    <div style="background:white; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid #f1f5f9; background:#f8fafc;">
                    <th style="padding:12px 20px; text-align:left; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Nama</th>
                    <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Role</th>
                    <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Kursus/Kelas</th>
                    <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Bergabung</th>
                    <th style="padding:12px 20px; text-align:right; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr style="border-bottom:1px solid #f8fafc; transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                    <td style="padding:14px 20px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#3b5bdb,#6366f1); display:flex; align-items:center; justify-content:center; color:white; font-size:13px; font-weight:700; flex-shrink:0;">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-size:14px; font-weight:600; color:#1e293b;">{{ $user->name }}</div>
                                <div style="font-size:12px; color:#94a3b8;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:14px 16px;">
                        <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td style="padding:14px 16px; text-align:center; font-size:14px; color:#475569; font-weight:600;">
                        @if($user->role === 'teacher') {{ $user->teacher_courses_count }} kursus
                        @elseif($user->role === 'student') {{ $user->enrollments_count }} kelas
                        @else —
                        @endif
                    </td>
                    <td style="padding:14px 16px; font-size:13px; color:#64748b;">{{ $user->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 20px; text-align:right;">
                        <div style="display:flex; gap:6px; justify-content:flex-end;">
                            <a href="{{ route('admin.users.edit', $user) }}" class="action-btn" style="background:#eff6ff; color:#3b5bdb;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn" style="background:#fef2f2; color:#dc2626;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; padding:48px; color:#94a3b8;">Tidak ada user ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">{{ $users->links() }}</div>
</div>
</x-app-layout>