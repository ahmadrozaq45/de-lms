{{--
  Partials: recommendations.card
  Variables: $rec (AiRecommendation), $cfg (type config), $c (color classes)
--}}
<div class="group relative bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700
            hover:border-{{ $cfg['color'] }}-200 dark:hover:border-{{ $cfg['color'] }}-700
            hover:shadow-md transition-all duration-200"
     data-rec-id="{{ $rec->id }}">

    {{-- Score badge ─────────────────────────────────────────────────────── --}}
    <div class="absolute top-3 right-3">
        <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full
                     {{ $c['bg'] }} {{ $c['icon'] }} ring-1 ring-inset ring-current ring-opacity-20">
            {{ number_format($rec->score, 0) }}%
        </span>
    </div>

    <div class="p-5">
        {{-- Icon + Type label ────────────────────────────────────────────── --}}
        <div class="flex items-center gap-2 mb-3">
            <div class="{{ $c['icon'] }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $cfg['icon'] }}"/>
                </svg>
            </div>
            <span class="text-xs uppercase tracking-wide font-medium text-gray-400 dark:text-gray-500">
                {{ $rec->getTypeLabel() }}
            </span>
        </div>

        {{-- Title & description ──────────────────────────────────────────── --}}
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white leading-snug mb-1 pr-8">
            {{ $rec->title }}
        </h3>
        @if($rec->description)
            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                {{ $rec->description }}
            </p>
        @endif

        {{-- Basis chips ──────────────────────────────────────────────────── --}}
        @if($rec->basis && isset($rec->basis['signal']))
            <div class="mt-3 flex flex-wrap gap-1">
                @php
                    $signalLabels = [
                        'nilai'        => ['Nilai',     'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400'],
                        'quiz'         => ['Quiz',      'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400'],
                        'tugas'        => ['Tugas',     'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400'],
                        'progress'     => ['Progress',  'bg-teal-50 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400'],
                        'progress+nilai' => ['Progress + Nilai', 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400'],
                    ];
                    $signals = explode('+', $rec->basis['signal']);
                @endphp
                @foreach($signals as $sig)
                    @if(isset($signalLabels[trim($sig)]))
                        @php [$label, $classes] = $signalLabels[trim($sig)]; @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $classes }}">
                            {{ $label }}
                        </span>
                    @endif
                @endforeach

                @if(isset($rec->basis['avg_quiz_score']))
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                        Avg {{ number_format($rec->basis['avg_quiz_score'], 1) }}
                    </span>
                @endif
                @if(isset($rec->basis['avg_nilai']))
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                        Nilai {{ number_format($rec->basis['avg_nilai'], 1) }}
                    </span>
                @endif
            </div>
        @endif

        {{-- CTA row ─────────────────────────────────────────────────────── --}}
        <div class="mt-4 flex items-center justify-between">
            @if($rec->target_type && $rec->target_id)
                <a href="{{ route('recommendations.goto', ['id' => $rec->id]) }}"
                   class="inline-flex items-center gap-1.5 text-xs font-medium {{ $c['icon'] }} hover:underline"
                   data-action="clicked">
                    Mulai Sekarang
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            @else
                <span></span>
            @endif

            {{-- Dismiss button ──────────────────────────────────────────── --}}
            <button type="button"
                    onclick="dismissRec({{ $rec->id }}, this)"
                    class="text-xs text-gray-300 dark:text-gray-600 hover:text-gray-500 dark:hover:text-gray-400 transition"
                    title="Sembunyikan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
async function dismissRec(id, btn) {
    try {
        await fetch(`/recommendations/${id}/feedback`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ action: 'dismissed' }),
        });
        const card = btn.closest('[data-rec-id]');
        card.style.transition = 'opacity 0.3s, transform 0.3s';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => card.remove(), 300);
    } catch (e) {
        console.error('Dismiss failed', e);
    }
}
</script>
@endpush
@endonce
