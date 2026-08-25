<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
            <div>
                <x-breadcrumb :items="[
                    ['label' => 'Kalender Akademik', 'route' => null]
                ]" />
                <h2 class="font-black text-xl sm:text-2xl text-slate-800 dark:text-slate-100 leading-tight tracking-tight flex items-center">
                    Kalender Akademik Skripsi
                </h2>
            </div>
            
            <div class="flex items-center w-full md:w-auto">
                <div class="flex flex-wrap items-center gap-2.5 sm:gap-4 px-3 sm:px-4 py-2 bg-white dark:bg-slate-800 rounded-2xl shadow-xs border border-slate-100 dark:border-slate-700 text-[10px] font-bold w-full md:w-auto justify-between sm:justify-start">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span class="text-slate-600 dark:text-slate-300">Gelombang</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">Bimbingan</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">Seminar</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <span class="text-slate-600 dark:text-slate-300">Sidang</span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-slate-800/50 dark:backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-700/50 p-3.5 sm:p-6 lg:p-8">
        <div id="calendar-container" class="min-h-[500px] sm:min-h-[700px]">
            <div id="calendar"></div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
    <style>
        :root {
            --fc-border-color: #f1f5f9;
            --fc-daygrid-event-dot-width: 8px;
        }
        .dark {
            --fc-border-color: #334155;
            --fc-page-bg-color: transparent;
            --fc-neutral-bg-color: #1e293b;
            --fc-list-event-hover-bg-color: #334155;
        }
        .fc {
            font-family: 'Inter', sans-serif;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: -0.025em;
            color: #1e293b;
        }
        .dark .fc .fc-toolbar-title {
            color: #f1f5f9;
        }
        .fc .fc-button {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            transition: all 0.2s;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .fc .fc-button {
            background-color: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }
        .fc .fc-button:hover {
            background-color: #f8fafc;
            color: #1e293b;
        }
        .dark .fc .fc-button:hover {
            background-color: #334155;
            color: #f1f5f9;
        }
        .fc .fc-button-active {
            background-color: #ea580c !important;
            border-color: #ea580c !important;
            color: #ffffff !important;
        }
        .fc .fc-col-header-cell-cushion {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 1rem 0 !important;
        }
        .dark .fc .fc-col-header-cell-cushion {
            color: #94a3b8;
        }
        .fc-event {
            cursor: pointer;
            border-radius: 6px !important;
            padding: 2px 4px !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            border: 1px solid rgba(0,0,0,0.05) !important;
            transition: transform 0.2s;
        }
        .fc-event:hover {
            transform: translateY(-1px);
            filter: brightness(0.95);
        }

        /* Popover & List Dark Mode */
        .dark .fc-popover {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4) !important;
        }
        .dark .fc-popover-header {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
        }
        .dark .fc-list-day-cushion {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
        }
        .dark .fc-list-event:hover td {
            background-color: #334155 !important;
        }
        .dark .fc-list-empty {
            background-color: #1e293b !important;
            color: #94a3b8 !important;
        }

        /* Mobile Responsive Adjustments */
        @media (max-width: 640px) {
            .fc .fc-toolbar {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 0.625rem !important;
                margin-bottom: 1rem !important;
            }
            .fc .fc-toolbar-title {
                font-size: 1.05rem !important;
                text-align: center !important;
            }
            .fc .fc-toolbar-chunk {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                flex-wrap: wrap !important;
                gap: 0.25rem !important;
            }
            .fc .fc-button {
                padding: 0.35rem 0.65rem !important;
                font-size: 0.65rem !important;
                border-radius: 0.5rem !important;
            }
            .fc .fc-col-header-cell-cushion {
                font-size: 0.6rem !important;
                padding: 0.4rem 0 !important;
            }
            .fc-event {
                font-size: 9px !important;
                padding: 1px 3px !important;
            }
            .fc .fc-daygrid-day-number {
                font-size: 0.7rem !important;
                padding: 2px 4px !important;
            }
            .fc .fc-list-event-title,
            .fc .fc-list-event-time {
                font-size: 0.75rem !important;
            }
        }

        .tippy-box[data-theme~='sibima'] {
            background-color: #1e293b;
            color: white;
            border-radius: 12px;
            padding: 4px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255,255,255,0.1);
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var isMobile = window.innerWidth < 640;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: isMobile ? 'listMonth' : 'dayGridMonth',
                headerToolbar: isMobile ? {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                } : {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
                },
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan',
                    week: 'Minggu',
                    day: 'Hari',
                    list: 'Daftar'
                },
                locale: 'id',
                dayMaxEvents: 2,
                moreLinkClick: 'popover',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                events: '{{ route("calendar.events") }}',
                eventDidMount: function(info) {
                    const props = info.event.extendedProps;
                    let content = `
                        <div class="p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase" style="background-color: ${info.event.backgroundColor}; color: ${info.event.textColor}">
                                    ${props.type}
                                </span>
                            </div>
                            <h4 class="font-bold text-sm leading-tight text-white mb-1">${info.event.title}</h4>
                            ${props.npm ? `<p class="text-[10px] text-slate-400 font-medium mb-2">${props.npm}</p>` : ''}
                            
                            <div class="space-y-1.5 mt-3 border-t border-white/10 pt-3">
                                <div class="flex items-center gap-2 text-[10px] text-slate-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>${info.event.allDay ? 'Sepanjang Hari' : info.event.start.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) + ' - ' + (info.event.end ? info.event.end.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '')}</span>
                                </div>
                                ${props.location ? `
                                <div class="flex items-center gap-2 text-[10px] text-slate-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>${props.location}</span>
                                </div>
                                ` : ''}
                                ${props.description ? `
                                <div class="mt-2 text-[10px] text-slate-400 italic">
                                    ${props.description}
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    `;

                    tippy(info.el, {
                        content: content,
                        allowHTML: true,
                        theme: 'sibima',
                        placement: 'auto',
                        interactive: true,
                        animation: 'shift-away',
                        maxWidth: 280,
                        trigger: 'mouseenter click'
                    });
                },
                eventClick: function(info) {
                    const id = info.event.id;
                    if (id.startsWith('seminar_')) {
                        // window.location.href = '/seminar-schedules/' + id.replace('seminar_', '');
                    }
                }
            });
            calendar.render();

            // Responsive view switch on window resize
            window.addEventListener('resize', function() {
                var currentIsMobile = window.innerWidth < 640;
                if (currentIsMobile !== isMobile) {
                    isMobile = currentIsMobile;
                    calendar.changeView(isMobile ? 'listMonth' : 'dayGridMonth');
                }
            });

            // Support Dark Mode Refresh
            window.addEventListener('darkModeChanged', () => {
                calendar.render();
            });
        });
    </script>
    @endpush
</x-app-layout>
