@extends('layouts.app')

@section('title', 'Rekomendasi AI')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 px-4">
    <div class="max-w-5xl mx-auto">

        {{-- ── Header ──────────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                              d="M9.663 17h4.673M12 3v1m6.364 1.636-.707.707M21 12h-1M4 12H3m3.343-5.657-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Rekomendasi AI
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Berdasarkan nilai, quiz, tugas, dan progress belajarmu
                </p>
            </div>

            <form method="POST" action="{{ route('recommendations.refresh') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15"/>
                    </svg>
                    Perbarui
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 px-4 py-3 text-sm text-green-700 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Summary bar ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            @php
                $typeConfig = [
                    'next_material' => ['label' => 'Materi',   'color' => 'blue',   'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    'topic'         => ['label' => 'Topik',    'color' => 'amber',  'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636-.707.707M21 12h-1M4 12H3m3.343-5.657-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                    'course'        => ['label' => 'Course',   'color' => 'teal',   'icon' => 'M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4'],
                    'practice'      => ['label' => 'Latihan',  'color' => 'purple', 'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 1 1 3.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'],
                ];

                $colorMap = [
                    'blue'   => ['bg' => 'bg-blue-50 dark:bg-blue-900/20',   'icon' => 'text-blue-500',   'border' => 'border-blue-100 dark:border-blue-800'],
                    'amber'  => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'icon' => 'text-amber-500',  'border' => 'border-amber-100 dark:border-amber-800'],
                    'teal'   => ['bg' => 'bg-teal-50 dark:bg-teal-900/20',   'icon' => 'text-teal-500',   'border' => 'border-teal-100 dark:border-teal-800'],
                    'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-900/20','icon' => 'text-purple-500','border' => 'border-purple-100 dark:border-purple-800'],
                ];
            @endphp

            @foreach($typeConfig as $typeKey => $cfg)
                @php
                    $count = $summary['by_type'][$typeKey] ?? 0;
                    $c = $colorMap[$cfg['color']];
                @endphp
                <div class="rounded-xl border {{ $c['border'] }} {{ $c['bg'] }} p-4 flex items-center gap-3">
                    <div class="{{ $c['icon'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="{{ $cfg['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ $count }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $cfg['label'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Recommendation cards ─────────────────────────────────────────── --}}
        @if($recommendations->isEmpty())
            <div class="text-center py-20">
                <svg class="mx-auto w-14 h-14 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
                          d="M9.663 17h4.673M12 3v1m6.364 1.636-.707.707M21 12h-1M4 12H3m3.343-5.657-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada rekomendasi tersedia.</p>
                <form method="POST" action="{{ route('recommendations.refresh') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition">
                        Buat Rekomendasi
                    </button>
                </form>
            </div>
        @else
            <div class="space-y-10">
                @foreach($typeConfig as $typeKey => $cfg)
                    @if(isset($recommendations[$typeKey]) && $recommendations[$typeKey]->isNotEmpty())
                        @php $c = $colorMap[$cfg['color']]; @endphp
                        <section>
                            <h2 class="text-base font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-4">
                                <span class="{{ $c['icon'] }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $cfg['icon'] }}"/>
                                    </svg>
                                </span>
                                {{ $cfg['label'] }}
                                <span class="ml-1 text-xs px-2 py-0.5 rounded-full {{ $c['bg'] }} {{ $c['icon'] }}">
                                    {{ $recommendations[$typeKey]->count() }}
                                </span>
                            </h2>

                            <div class="grid sm:grid-cols-2 gap-4">
                                @foreach($recommendations[$typeKey] as $rec)
                                    @include('recommendations.card', ['rec' => $rec, 'cfg' => $cfg, 'c' => $c])
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection
