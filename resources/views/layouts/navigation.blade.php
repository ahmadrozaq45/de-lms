@php $role = Auth::user()->role; @endphp
<nav x-data="{ open: false }" style="background:white; border-bottom:1px solid #e2e8f0; position:sticky; top:0; z-index:50;">
    <div style="max-width:1280px; margin:0 auto; padding:0 24px; height:60px; display:flex; align-items:center; justify-content:space-between; gap:16px;">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" style="display:flex; align-items:center; gap:9px; text-decoration:none; flex-shrink:0;">
            <div style="width:32px; height:32px; background:#3b5bdb; border-radius:8px; display:flex; align-items:center; justify-content:center; color:white; font-size:12px; font-weight:800;">LMS</div>
            <span style="font-weight:700; font-size:15px; color:#1e293b; letter-spacing:-0.3px;">DE-LMS</span>
        </a>

        {{-- Desktop Nav --}}
        <div class="hidden sm:flex" style="align-items:center; gap:2px; flex:1; margin-left:8px;">

            @php
            $navItems = match($role) {
                'admin' => [
                    ['label'=>'Dashboard', 'route'=>'admin.dashboard',  'match'=>'admin.dashboard'],
                    ['label'=>'User',      'route'=>'admin.users.index','match'=>'admin.users.*'],
                    ['label'=>'Report',    'route'=>'admin.report',     'match'=>'admin.report'],
                    ['label'=>'Setting',   'route'=>'settings.index',   'match'=>'settings.*'],
                ],
                'teacher' => [
                    ['label'=>'Dashboard', 'route'=>'teacher.dashboard',      'match'=>'teacher.dashboard'],
                    ['label'=>'My Course', 'route'=>'teacher.courses.index',  'match'=>'teacher.courses.*'],
                    ['label'=>'Report',    'route'=>'teacher.report',         'match'=>'teacher.report'],
                    ['label'=>'Setting',   'route'=>'settings.index',         'match'=>'settings.*'],
                ],
                'student' => [
                    ['label'=>'Dashboard', 'route'=>'student.dashboard',    'match'=>'student.dashboard'],
                    ['label'=>'My Course', 'route'=>'student.courses.index','match'=>'student.courses.*'],
                    ['label'=>'Report',    'route'=>'student.report',       'match'=>'student.report'],
                    ['label'=>'Setting',   'route'=>'settings.index',       'match'=>'settings.*'],
                ],
                default => [],
            };
            @endphp

            @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               style="padding:7px 14px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; transition:all 0.15s;
                      color:{{ $isActive ? '#3b5bdb' : '#64748b' }};
                      background:{{ $isActive ? '#eff6ff' : 'transparent' }};"
               onmouseover="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.background='#f8fafc'; this.style.color='#1e293b'; }"
               onmouseout="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#64748b'; }">
                {{ $item['label'] }}
            </a>
            @endforeach
        </div>

        {{-- Right: Role badge + User dropdown --}}
        <div class="hidden sm:flex" style="align-items:center; gap:12px;">
            <span style="font-size:11px; font-weight:700; padding:3px 10px; border-radius:100px;
                background:{{ $role==='admin' ? '#fee2e2' : ($role==='teacher' ? '#dbeafe' : '#dcfce7') }};
                color:{{ $role==='admin' ? '#991b1b' : ($role==='teacher' ? '#1e40af' : '#166534') }};">
                {{ ucfirst($role) }}
            </span>

            <div x-data="{ open: false }" style="position:relative;">
                <button @click="open = !open"
                        style="display:flex; align-items:center; gap:8px; padding:6px 12px 6px 8px; border:1px solid #e2e8f0; border-radius:10px; background:white; cursor:pointer; font-size:13px; font-weight:600; color:#1e293b; transition:all 0.15s;"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                    <div style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,#3b5bdb,#6366f1); display:flex; align-items:center; justify-content:center; color:white; font-size:11px; font-weight:800; flex-shrink:0;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span style="max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ Auth::user()->name }}</span>
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform 0.2s; flex-shrink:0;"><polyline points="6 9 12 15 18 9"/></svg>
                </button>

                <div x-show="open" @click.outside="open = false" x-transition
                     style="position:absolute; right:0; top:calc(100% + 8px); background:white; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.1); min-width:180px; overflow:hidden; z-index:100;">
                    <div style="padding:10px 14px; border-bottom:1px solid #f1f5f9;">
                        <div style="font-size:13px; font-weight:600; color:#1e293b;">{{ Auth::user()->name }}</div>
                        <div style="font-size:11px; color:#94a3b8; margin-top:1px;">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="{{ route('settings.index') }}"
                       style="display:flex; align-items:center; gap:8px; padding:10px 14px; font-size:13px; color:#475569; text-decoration:none; transition:background 0.15s;"
                       onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20v-1a8 8 0 0 1 16 0v1"/></svg>
                        Profil & Setting
                    </a>
                    <div style="border-top:1px solid #f1f5f9;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    style="width:100%; display:flex; align-items:center; gap:8px; padding:10px 14px; font-size:13px; color:#dc2626; background:transparent; border:none; cursor:pointer; text-align:left; transition:background 0.15s;"
                                    onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mobile hamburger --}}
        <button @click="open = !open" class="sm:hidden"
                style="padding:6px; border:none; background:transparent; cursor:pointer; color:#64748b;">
            <svg x-show="!open" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            <svg x-show="open" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition style="border-top:1px solid #e2e8f0; background:white; padding:12px 16px;">
        @foreach($navItems as $item)
        <a href="{{ route($item['route']) }}"
           style="display:block; padding:10px 14px; border-radius:8px; font-size:14px; font-weight:600; text-decoration:none; color:{{ request()->routeIs($item['match']) ? '#3b5bdb' : '#475569' }}; background:{{ request()->routeIs($item['match']) ? '#eff6ff' : 'transparent' }}; margin-bottom:2px;">
            {{ $item['label'] }}
        </a>
        @endforeach
        <div style="border-top:1px solid #f1f5f9; margin-top:8px; padding-top:8px;">
            <div style="font-size:13px; font-weight:600; color:#1e293b; padding:6px 14px;">{{ Auth::user()->name }}</div>
            <div style="font-size:11px; color:#94a3b8; padding:0 14px 8px;">{{ Auth::user()->email }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="width:100%; text-align:left; padding:10px 14px; font-size:13px; font-weight:600; color:#dc2626; background:transparent; border:none; cursor:pointer; border-radius:8px;">Keluar</button>
            </form>
        </div>
    </div>
</nav>