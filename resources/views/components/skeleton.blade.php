@props([
    'type' => 'table', // 'table', 'card', 'chart', 'roadmap', 'text', 'user-card'
    'rows' => 5,
    'cols' => 5,
])

@if($type === 'table')
    <!-- Skeleton Table Loading -->
    <div {{ $attributes->merge(['class' => 'w-full animate-shimmer overflow-hidden']) }}>
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50/70 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-700/80">
                    <th class="py-4 px-6 w-12"><div class="h-4 w-4 rounded-md skeleton-box"></div></th>
                    <th class="py-4 px-6"><div class="h-3 w-28 rounded-md skeleton-box"></div></th>
                    <th class="py-4 px-6"><div class="h-3 w-40 rounded-md skeleton-box"></div></th>
                    <th class="py-4 px-6 text-center"><div class="h-3 w-20 mx-auto rounded-md skeleton-box"></div></th>
                    <th class="py-4 px-6"><div class="h-3 w-32 rounded-md skeleton-box"></div></th>
                    <th class="py-4 px-6 text-right"><div class="h-3 w-16 ml-auto rounded-md skeleton-box"></div></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                @for($i = 0; $i < $rows; $i++)
                    <tr class="transition-colors">
                        <td class="py-4 px-6">
                            <div class="w-4 h-4 rounded-md skeleton-box"></div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl skeleton-box shrink-0"></div>
                                <div class="space-y-2 flex-1">
                                    <div class="h-3.5 w-32 rounded-md skeleton-box"></div>
                                    <div class="h-2.5 w-20 rounded-md skeleton-box"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="space-y-2">
                                <div class="h-3.5 w-48 rounded-md skeleton-box"></div>
                                <div class="h-2.5 w-32 rounded-md skeleton-box"></div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="h-6 w-20 mx-auto rounded-lg skeleton-box"></div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="space-y-1.5">
                                <div class="h-3 w-28 rounded-md skeleton-box"></div>
                                <div class="h-2.5 w-24 rounded-md skeleton-box"></div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-7 h-7 rounded-lg skeleton-box"></div>
                                <div class="w-7 h-7 rounded-lg skeleton-box"></div>
                            </div>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

@elseif($type === 'chart')
    <!-- Skeleton Chart Loading -->
    <div {{ $attributes->merge(['class' => 'p-6 bg-white dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700/50 animate-shimmer']) }}>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-md skeleton-box"></div>
                <div class="h-3.5 w-40 rounded-md skeleton-box"></div>
            </div>
            <div class="h-6 w-20 rounded-lg skeleton-box"></div>
        </div>
        <div class="h-60 flex items-end justify-between gap-3 pt-6 px-2">
            <div class="w-full bg-slate-100 dark:bg-slate-700/40 rounded-t-xl skeleton-box" style="height: 40%;"></div>
            <div class="w-full bg-slate-100 dark:bg-slate-700/40 rounded-t-xl skeleton-box" style="height: 75%;"></div>
            <div class="w-full bg-slate-100 dark:bg-slate-700/40 rounded-t-xl skeleton-box" style="height: 55%;"></div>
            <div class="w-full bg-slate-100 dark:bg-slate-700/40 rounded-t-xl skeleton-box" style="height: 90%;"></div>
            <div class="w-full bg-slate-100 dark:bg-slate-700/40 rounded-t-xl skeleton-box" style="height: 65%;"></div>
            <div class="w-full bg-slate-100 dark:bg-slate-700/40 rounded-t-xl skeleton-box" style="height: 80%;"></div>
        </div>
        <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/50">
            <div class="h-3 w-16 rounded-md skeleton-box"></div>
            <div class="h-3 w-16 rounded-md skeleton-box"></div>
            <div class="h-3 w-16 rounded-md skeleton-box"></div>
            <div class="h-3 w-16 rounded-md skeleton-box"></div>
            <div class="h-3 w-16 rounded-md skeleton-box"></div>
            <div class="h-3 w-16 rounded-md skeleton-box"></div>
        </div>
    </div>

@elseif($type === 'card')
    <!-- Skeleton Stat Card Loading -->
    <div {{ $attributes->merge(['class' => 'p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm animate-shimmer flex items-center justify-between gap-4']) }}>
        <div class="space-y-2 flex-1">
            <div class="h-2.5 w-24 rounded-md skeleton-box"></div>
            <div class="h-6 w-16 rounded-lg skeleton-box"></div>
            <div class="h-2 w-32 rounded-md skeleton-box"></div>
        </div>
        <div class="w-12 h-12 rounded-2xl skeleton-box shrink-0"></div>
    </div>

@elseif($type === 'roadmap')
    <!-- Skeleton Roadmap / Stepper Loading -->
    <div {{ $attributes->merge(['class' => 'p-8 bg-white dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-700/50 animate-shimmer']) }}>
        <div class="flex items-center justify-between mb-8">
            <div class="space-y-2">
                <div class="h-5 w-64 rounded-lg skeleton-box"></div>
                <div class="h-3 w-40 rounded-md skeleton-box"></div>
            </div>
            <div class="h-8 w-28 rounded-xl skeleton-box"></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            @for($s = 1; $s <= 6; $s++)
                <div class="flex flex-col items-center text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl skeleton-box"></div>
                    <div class="h-3 w-16 rounded-md skeleton-box"></div>
                    <div class="h-2 w-20 rounded-md skeleton-box"></div>
                </div>
            @endfor
        </div>
    </div>

@else
    <!-- Default Skeleton Lines -->
    <div {{ $attributes->merge(['class' => 'space-y-3 animate-shimmer']) }}>
        @for($i = 0; $i < $rows; $i++)
            <div class="h-3.5 rounded-md skeleton-box" style="width: {{ rand(60, 100) }}%;"></div>
        @endfor
    </div>
@endif
