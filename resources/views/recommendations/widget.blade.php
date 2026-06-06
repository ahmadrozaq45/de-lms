{{--
  Component: recommendations.widget
  Drop this anywhere in a Blade view to show the top 5 recommendations.
  Usage: @include('recommendations.widget')
--}}
<div id="rec-widget" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9.663 17h4.673M12 3v1m6.364 1.636-.707.707M21 12h-1M4 12H3m3.343-5.657-.707-.707m2.828 9.9a5 5 0 1 1 7.072 0l-.548.547A3.374 3.374 0 0 0 14 18.469V19a2 2 0 1 1-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <span class="text-sm font-medium text-gray-800 dark:text-white">Rekomendasi AI</span>
        </div>
        <a href="{{ route('recommendations.index') }}"
           class="text-xs text-indigo-500 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
            Lihat semua →
        </a>
    </div>

    <div id="rec-widget-list" class="divide-y divide-gray-50 dark:divide-gray-700/50">
        {{-- Loaded via JS --}}
        <div class="px-5 py-4 text-xs text-gray-400 animate-pulse">Memuat rekomendasi…</div>
    </div>
</div>

<script>
(function() {
    const colorMap = {
        blue:   { bg: 'bg-blue-50 dark:bg-blue-900/20',    text: 'text-blue-600 dark:text-blue-400'   },
        amber:  { bg: 'bg-amber-50 dark:bg-amber-900/20',  text: 'text-amber-600 dark:text-amber-400' },
        teal:   { bg: 'bg-teal-50 dark:bg-teal-900/20',    text: 'text-teal-600 dark:text-teal-400'   },
        purple: { bg: 'bg-purple-50 dark:bg-purple-900/20',text: 'text-purple-600 dark:text-purple-400'},
    };

    fetch('/recommendations/widget')
        .then(r => r.json())
        .then(items => {
            const list = document.getElementById('rec-widget-list');
            if (!items.length) {
                list.innerHTML = '<div class="px-5 py-4 text-xs text-gray-400">Belum ada rekomendasi.</div>';
                return;
            }
            list.innerHTML = items.map(item => {
                const c = colorMap[item.type_color] || colorMap.blue;
                return `
                <a href="/recommendations/${item.id}/goto"
                   class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition group"
                   data-rec-id="${item.id}">
                    <span class="mt-0.5 inline-flex items-center justify-center w-6 h-6 rounded-full shrink-0 ${c.bg} ${c.text}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-medium text-gray-800 dark:text-white truncate">${item.title}</span>
                            <span class="text-xs ${c.text} shrink-0">${Math.round(item.score)}%</span>
                        </div>
                        <span class="text-xs ${c.text} font-medium">${item.type_label}</span>
                    </div>
                </a>`;
            }).join('');
        })
        .catch(() => {
            document.getElementById('rec-widget-list').innerHTML =
                '<div class="px-5 py-4 text-xs text-red-400">Gagal memuat rekomendasi.</div>';
        });
})();
</script>
