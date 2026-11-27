@props([
    'id' => null,
    'name' => null,
    'value' => '',
    'min' => null,
    'max' => null,
    'placeholder' => 'Pilih tanggal...',
    'required' => false,
    'disabled' => false,
    'compact' => false,
    'borderless' => false,
    'title' => null,
    'onchange' => null,
    'minYear' => null,
    'maxYear' => null,
    'inputClass' => null,
])

@php
    $wrapperClass = trim((string) $attributes->get('class', 'w-full'));
    $rootAttrs = $attributes->except([
        'class', 'id', 'name', 'value', 'min', 'max', 'placeholder',
        'required', 'disabled', 'compact', 'borderless', 'title',
        'onchange', 'minYear', 'maxYear', 'inputClass', 'type',
    ]);

    $defaultTriggerClass = $borderless
        ? 'w-full h-8 px-1.5 text-xs font-semibold bg-transparent border-0 text-slate-800 dark:text-slate-100 rounded-lg hover:bg-slate-200/50 dark:hover:bg-slate-800/70 focus:outline-none focus:ring-2 focus:ring-orange-500/30'
        : ($compact
            ? 'w-full h-9 px-2.5 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20'
            : 'w-full h-10 px-3 text-xs font-medium rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 hover:border-slate-400 dark:hover:border-slate-600 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20');

    $resolvedTriggerClass = $inputClass ?: $defaultTriggerClass;
@endphp

<div class="relative {{ $wrapperClass }}"
     x-data="sibimaDatePicker({
         value: @js((string) ($value ?? '')),
         min: @js($min),
         max: @js($max),
         compact: @js((bool) $compact),
         required: @js((bool) $required),
         disabled: @js((bool) $disabled),
         placeholder: @js($placeholder),
         minYear: @js($minYear),
         maxYear: @js($maxYear),
     })"
     x-modelable="value"
     {{ $rootAttrs }}>

    {{-- Real form input (submits YYYY-MM-DD, supports native required validation & getElementById) --}}
    <input type="text"
           x-ref="input"
           @if($id) id="{{ $id }}" @endif
           @if($name) name="{{ $name }}" @endif
           value="{{ $value ?? '' }}"
           @if($required) required @endif
           @if($disabled) disabled @endif
           @if($onchange) onchange="{{ $onchange }}" @endif
           @input="syncFromInput($event.target.value)"
           @focus="if (!open && !disabled) openCalendar()"
           @keydown.prevent
           tabindex="-1"
           autocomplete="off"
           aria-hidden="true"
           class="absolute bottom-0 left-3 w-px h-px opacity-0 pointer-events-none">

    {{-- Visible Interactive Trigger Button --}}
    <button type="button"
            x-ref="trigger"
            @click="toggleCalendar()"
            @if($disabled) disabled @endif
            @if($title) title="{{ $title }}" @endif
            :aria-expanded="open ? 'true' : 'false'"
            aria-haspopup="dialog"
            class="inline-flex items-center justify-between gap-2 transition-colors {{ $resolvedTriggerClass }} {{ $disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}"
            :class="open && !{{ $borderless ? 'true' : 'false' }} ? 'border-orange-500 dark:border-orange-500 ring-2 ring-orange-500/20' : ''">
        <span class="flex items-center gap-2 min-w-0 truncate">
            <svg class="{{ $compact || $borderless ? 'w-3.5 h-3.5' : 'w-4 h-4' }} shrink-0 transition-colors"
                 :class="value ? 'text-orange-600 dark:text-orange-400' : 'text-slate-400 dark:text-slate-500'"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="truncate"
                  :class="value ? '' : 'text-slate-400 dark:text-slate-500 font-normal'"
                  x-text="displayValue || placeholder"></span>
        </span>
        @unless($borderless)
            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0 transition-transform duration-150"
                 :class="open ? 'rotate-180 text-orange-600 dark:text-orange-400' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
            </svg>
        @endunless
    </button>

    {{-- Teleported Calendar Popover (never clipped by modal or card overflow-hidden) --}}
    <template x-teleport="body">
        <div x-show="open"
             x-ref="popover"
             x-cloak
             x-transition:enter="transition ease-out duration-120"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             :style="popoverStyle"
             role="dialog"
             aria-modal="false"
             aria-label="Pilih Tanggal"
             class="fixed z-[9999] rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/15 dark:shadow-slate-950/60 p-3.5 select-none">

            {{-- Header: Prev | Month & Year Quick Dropdowns | Next --}}
            <div class="flex items-center justify-between gap-1.5 mb-3">
                <button type="button"
                        @click="prevMonth()"
                        aria-label="Bulan sebelumnya"
                        class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-colors shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <div class="flex items-center gap-1.5 flex-1 min-w-0 justify-center">
                    <select x-model.number="viewMonth"
                            aria-label="Pilih bulan"
                            class="h-8 pl-2.5 pr-7 py-0 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs font-bold focus:border-orange-500 focus:ring-1 focus:ring-orange-500 cursor-pointer flex-1 min-w-0">
                        <template x-for="(mName, mIdx) in months" :key="mIdx">
                            <option :value="mIdx" x-text="mName" :selected="viewMonth === mIdx"></option>
                        </template>
                    </select>

                    <select x-model.number="viewYear"
                            aria-label="Pilih tahun"
                            class="h-8 pl-2.5 pr-7 py-0 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-xs font-bold tabular-nums focus:border-orange-500 focus:ring-1 focus:ring-orange-500 cursor-pointer w-[84px] shrink-0">
                        <template x-for="yr in yearOptions" :key="yr">
                            <option :value="yr" x-text="yr" :selected="viewYear === yr"></option>
                        </template>
                    </select>
                </div>

                <button type="button"
                        @click="nextMonth()"
                        aria-label="Bulan berikutnya"
                        class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-colors shrink-0 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            {{-- Weekday Labels (Sen–Sab neutral, Min red) --}}
            <div class="grid grid-cols-7 text-center text-[11px] font-bold mb-1.5">
                <span class="py-1 text-slate-500 dark:text-slate-400">Sen</span>
                <span class="py-1 text-slate-500 dark:text-slate-400">Sel</span>
                <span class="py-1 text-slate-500 dark:text-slate-400">Rab</span>
                <span class="py-1 text-slate-500 dark:text-slate-400">Kam</span>
                <span class="py-1 text-slate-500 dark:text-slate-400">Jum</span>
                <span class="py-1 text-slate-500 dark:text-slate-400">Sab</span>
                <span class="py-1 text-rose-600 dark:text-rose-400">Min</span>
            </div>

            {{-- 6x7 Calendar Days Grid --}}
            <div class="grid grid-cols-7 gap-0.5 text-center">
                <template x-for="cell in calendarDays" :key="cell.iso">
                    <button type="button"
                            @click="selectCell(cell)"
                            :disabled="cell.isDisabled"
                            class="h-8 w-full rounded-lg text-xs tabular-nums flex items-center justify-center transition-colors"
                            :class="dayButtonClass(cell)"
                            x-text="cell.day">
                    </button>
                </template>
            </div>

            {{-- Footer Controls: Hari Ini & Hapus --}}
            <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <button type="button"
                        @click="selectToday()"
                        :disabled="!canSelectToday"
                        class="font-bold transition-colors"
                        :class="canSelectToday ? 'text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 cursor-pointer' : 'text-slate-300 dark:text-slate-700 cursor-not-allowed'">
                    Hari Ini
                </button>

                <div class="flex items-center gap-3">
                    <button type="button"
                            x-show="value && !required"
                            @click="clearDate()"
                            class="font-semibold text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors cursor-pointer">
                        Hapus
                    </button>
                    <button type="button"
                            @click="closeCalendar()"
                            class="font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
