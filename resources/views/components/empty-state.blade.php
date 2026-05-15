@props([
    'title' => 'Tidak ada data ditemukan',
    'description' => 'Coba ubah kata kunci pencarian atau filter Anda.',
    'icon' => 'folder-open',
    'colspan' => 1
])

<tr>
    <td colspan="{{ $colspan }}" class="py-16 text-center">
        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-900 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
            @if($icon === 'folder-open')
                <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
            @elseif($icon === 'clock')
                <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif($icon === 'search')
                <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            @else
                <svg class="h-8 w-8 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif
        </div>
        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-widest font-black">{{ $description }}</p>
    </td>
</tr>
