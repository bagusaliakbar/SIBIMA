@props([
    'message' => '',
    'isMe' => false,
    'theme' => 'indigo', // 'indigo' or 'rose'
])

@php
    $lines = explode("\n", str_replace("\r", "", (string) $message));
    $points = [];
    $extraLines = [];
    $isPointList = false;

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '') continue;

        if (preg_match('/^(\d+)[\.\)]\s*(.+)$/u', $trimmed, $m)) {
            $isPointList = true;
            $points[] = [
                'num' => $m[1],
                'text' => $m[2],
            ];
        } elseif ($isPointList && !empty($points)) {
            $extraLines[] = $trimmed;
        }
    }
@endphp

@if($isPointList && count($points) > 0)
    <div class="space-y-2.5 my-0.5">
        <div class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider {{ $isMe ? 'text-white/80' : ($theme === 'rose' ? 'text-rose-600 dark:text-rose-400' : ($theme === 'orange' ? 'text-orange-600 dark:text-orange-400' : 'text-indigo-600 dark:text-indigo-400')) }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span>Daftar Poin Revisi ({{ count($points) }} Poin)</span>
        </div>

        <div class="space-y-1.5">
            @foreach($points as $p)
                <div class="flex items-start gap-2.5 p-2.5 rounded-xl transition-all {{ $isMe 
                    ? 'bg-white/10 border border-white/15' 
                    : 'bg-slate-50/80 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-700/60' }}">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] font-black shrink-0 mt-0.5 shadow-xs {{ $isMe 
                        ? ($theme === 'rose' ? 'bg-white text-rose-700' : ($theme === 'orange' ? 'bg-white text-orange-700' : 'bg-white text-indigo-700')) 
                        : ($theme === 'rose' ? 'bg-rose-600 text-white' : ($theme === 'orange' ? 'bg-orange-600 text-white' : 'bg-indigo-600 text-white')) }}">
                        {{ $p['num'] }}
                    </span>
                    <div class="text-xs font-semibold leading-relaxed flex-1 {{ $isMe ? 'text-white' : 'text-slate-800 dark:text-slate-100' }}">
                        {{ $p['text'] }}
                    </div>
                </div>
            @endforeach
        </div>

        @if(!empty($extraLines))
            <div class="mt-2 pt-2 border-t text-xs font-medium leading-relaxed {{ $isMe ? 'border-white/20 text-white/90' : 'border-slate-100 dark:border-slate-700 text-slate-600 dark:text-slate-300' }}">
                {!! nl2br(e(implode("\n", $extraLines))) !!}
            </div>
        @endif
    </div>
@else
    {!! nl2br(e($message)) !!}
@endif
