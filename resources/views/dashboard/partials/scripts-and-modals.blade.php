    <script src="{{ asset('assets/vendor/chartjs/chart.umd.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = document.documentElement.classList.contains('dark');
            @if(Auth::user()->role !== 'mahasiswa')
                // Distribution Chart
                const distCtx = document.getElementById('distributionChart').getContext('2d');
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    new Chart(distCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Aktif', 'Selesai', 'Menunggu'],
                            datasets: [{
                                data: [{{ $thesisStatusCounts['active'] ?? 0 }}, {{ $thesisStatusCounts['completed'] ?? 0 }}, {{ $thesisStatusCounts['pending'] ?? 0 }}],
                                backgroundColor: ['#f97316', '#10b981', '#6366f1'],
                                borderWidth: 0,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'bottom', 
                                    labels: { 
                                        usePointStyle: true, 
                                        padding: 20, 
                                        font: { weight: 'bold', size: 10 },
                                        color: darkMode ? '#94a3b8' : '#64748b'
                                    } 
                                } 
                            },
                            cutout: '75%'
                        }
                    });
                @else
                    new Chart(distCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_keys($studentProgressDistribution)) !!},
                            datasets: [{
                                label: 'Mahasiswa',
                                data: {!! json_encode(array_values($studentProgressDistribution)) !!},
                                backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#6366f1'],
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b', stepSize: 1 } 
                                }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b' }
                                } 
                            },
                            plugins: { legend: { display: false } }
                        }
                    });
                @endif

                // Activity Chart
                const actCtx = document.getElementById('activityChart').getContext('2d');
                new Chart(actCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_keys($monthlyMentoringCounts)) !!},
                        datasets: [{
                            label: 'Sesi Selesai',
                            data: {!! json_encode(array_values($monthlyMentoringCounts)) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#3b82f6',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { 
                            y: { 
                                beginAtZero: true, 
                                grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                ticks: { color: darkMode ? '#94a3b8' : '#64748b', stepSize: 1 } 
                            }, 
                            x: { 
                                grid: { display: false },
                                ticks: { color: darkMode ? '#94a3b8' : '#64748b' }
                            } 
                        },
                        plugins: { legend: { display: false } }
                    }
                });

                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'kaprodi')
                    // Workload Chart
                    const workCtx = document.getElementById('workloadChart').getContext('2d');
                    new Chart(workCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_keys($dosenWorkload)) !!},
                            datasets: [{
                                label: 'Jumlah Mahasiswa',
                                data: {!! json_encode(array_values($dosenWorkload)) !!},
                                backgroundColor: '#10b981',
                                borderRadius: 8,
                                barThickness: 30
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                x: { 
                                    beginAtZero: true, 
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b', stepSize: 1 } 
                                }, 
                                y: { 
                                    grid: { display: false },
                                    ticks: { 
                                        color: darkMode ? '#f1f5f9' : '#1e293b',
                                        font: { weight: 'bold', size: 11 }
                                    }
                                } 
                            },
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: darkMode ? '#1e293b' : '#fff',
                                    titleColor: darkMode ? '#fff' : '#1e293b',
                                    bodyColor: darkMode ? '#cbd5e1' : '#64748b',
                                    borderColor: darkMode ? '#334155' : '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: false
                                }
                            }
                        }
                    });

                    // Score Distribution Chart
                    const scoreDistCtx = document.getElementById('scoreDistributionChart').getContext('2d');
                    new Chart(scoreDistCtx, {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode(array_keys($scoreDistribution)) !!},
                            datasets: [{
                                data: {!! json_encode(array_values($scoreDistribution)) !!},
                                backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#f97316', '#ef4444'],
                                borderWidth: 0,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'right', 
                                    labels: { 
                                        usePointStyle: true, 
                                        padding: 20, 
                                        font: { weight: 'bold', size: 10 },
                                        color: darkMode ? '#94a3b8' : '#64748b'
                                    } 
                                } 
                            },
                            cutout: '70%'
                        }
                    });

                    // Wave Duration Chart
                    const waveDurCtx = document.getElementById('waveDurationChart').getContext('2d');
                    new Chart(waveDurCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode(array_keys($waveDurationStats)) !!},
                            datasets: [{
                                label: 'Rata-rata (Bulan)',
                                data: {!! json_encode(array_values($waveDurationStats)) !!},
                                borderColor: '#f97316',
                                backgroundColor: 'rgba(249, 115, 22, 0.05)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 5,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#f97316',
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b' } 
                                }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b' }
                                } 
                            },
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + ' Bulan';
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // On-time Graduation Chart
                    const onTimeCtx = document.getElementById('onTimeGraduationChart').getContext('2d');
                    new Chart(onTimeCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Tepat Waktu', 'Terlambat'],
                            datasets: [{
                                data: [{{ $onTimeStats['Tepat Waktu'] }}, {{ $onTimeStats['Terlambat'] }}],
                                backgroundColor: ['#10b981', '#ef4444'],
                                borderWidth: 0,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'bottom', 
                                    labels: { 
                                        usePointStyle: true, 
                                        padding: 20, 
                                        font: { weight: 'bold', size: 10 },
                                        color: darkMode ? '#94a3b8' : '#64748b'
                                    } 
                                } 
                            },
                            cutout: '75%'
                        }
                    });

                    // Student Health Chart
                    const healthCtx = document.getElementById('studentHealthChart').getContext('2d');
                    new Chart(healthCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Normal', 'Kritis (Sem 13+)'],
                            datasets: [{
                                data: [{{ $studentHealthStats['Normal'] }}, {{ $studentHealthStats['Kritis'] }}],
                                backgroundColor: ['#3b82f6', '#f43f5e'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    position: 'bottom', 
                                    labels: { 
                                        usePointStyle: true, 
                                        padding: 20, 
                                        font: { weight: 'bold', size: 10 },
                                        color: darkMode ? '#94a3b8' : '#64748b'
                                    } 
                                } 
                            }
                        }
                    });

                    // Cohort Completion Chart
                    const cohortCtx = document.getElementById('cohortCompletionChart').getContext('2d');
                    new Chart(cohortCtx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode(array_keys($cohortCompletionData)) !!},
                            datasets: [{
                                label: 'Rata-rata Masa Studi (Tahun)',
                                data: {!! json_encode(array_values($cohortCompletionData)) !!},
                                backgroundColor: '#3b82f6',
                                borderRadius: 8,
                                barThickness: 40
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { 
                                y: { 
                                    beginAtZero: true, 
                                    suggestedMax: 7,
                                    grid: { color: darkMode ? '#334155' : '#f1f5f9' }, 
                                    ticks: { color: darkMode ? '#94a3b8' : '#64748b' } 
                                }, 
                                x: { 
                                    grid: { display: false },
                                    ticks: { 
                                        color: darkMode ? '#f1f5f9' : '#1e293b',
                                        font: { weight: 'bold', size: 10 }
                                    }
                                } 
                            },
                            plugins: { 
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: darkMode ? '#1e293b' : '#fff',
                                    titleColor: darkMode ? '#fff' : '#1e293b',
                                    bodyColor: darkMode ? '#cbd5e1' : '#64748b',
                                    borderColor: darkMode ? '#334155' : '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 12,
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + ' Tahun';
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Topic Trend Chart (Stacked Bar)
                    const topicTrendCtx = document.getElementById('topicTrendChart').getContext('2d');
                    const topicTrendData = {!! json_encode($topicTrends) !!};
                    const topicCohorts = Object.keys(topicTrendData);
                    
                    const allTopics = [...new Set(topicCohorts.flatMap(c => Object.keys(topicTrendData[c])))];
                    const topicColors = {
                        'Web Development': '#f59e0b', 'Mobile Development': '#10b981', 'Data Science': '#3b82f6',
                        'Security': '#f43f5e', 'Networking': '#8b5cf6', 'Lainnya': '#94a3b8'
                    };

                    new Chart(topicTrendCtx, {
                        type: 'bar',
                        data: {
                            labels: topicCohorts,
                            datasets: allTopics.map(topic => ({
                                label: topic,
                                data: topicCohorts.map(cohort => topicTrendData[cohort][topic] || 0),
                                backgroundColor: topicColors[topic] || '#cbd5e1',
                                borderRadius: 4
                            }))
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            scales: {
                                x: { stacked: true, grid: { display: false }, ticks: { color: darkMode ? '#94a3b8' : '#64748b', font: { weight: 'bold', size: 10 } } },
                                y: { stacked: true, beginAtZero: true, max: 100, grid: { color: darkMode ? '#334155' : '#f1f5f9' }, ticks: { color: darkMode ? '#94a3b8' : '#64748b', callback: v => v + '%' } }
                            },
                            plugins: {
                                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { weight: 'bold', size: 10 }, color: darkMode ? '#94a3b8' : '#64748b' } },
                                tooltip: {
                                    backgroundColor: darkMode ? '#1e293b' : '#fff', titleColor: darkMode ? '#fff' : '#1e293b', bodyColor: darkMode ? '#cbd5e1' : '#64748b',
                                    borderColor: darkMode ? '#334155' : '#e2e8f0', borderWidth: 1, padding: 12,
                                    callbacks: { label: c => c.dataset.label + ': ' + c.parsed.y + '%' }
                                }
                            }
                        }
                    });
                @endif
            @endif

            // Live Online Users Polling for Dashboard Widget
            @if(Auth::user()->role !== 'mahasiswa')
                const onlineEndpoint = "{{ route('dashboard.online-users') }}";
                function updateOnlineUsersWidget() {
                    fetch(onlineEndpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.json())
                    .then(data => {
                        const badge = document.getElementById('online-users-count-badge');
                        const mhsCount = document.getElementById('online-count-mhs');
                        const dosenCount = document.getElementById('online-count-dosen');
                        const adminCount = document.getElementById('online-count-admin');
                        const listContainer = document.getElementById('online-users-list');

                        if (badge) badge.innerText = `${data.count} Aktif`;
                        if (mhsCount) mhsCount.innerText = data.by_role.mahasiswa || 0;
                        if (dosenCount) dosenCount.innerText = data.by_role.dosen || 0;
                        if (adminCount) adminCount.innerText = (data.by_role.admin || 0) + (data.by_role.kaprodi || 0);

                        if (listContainer && data.users) {
                            if (data.users.length === 0) {
                                listContainer.innerHTML = '<div class="text-center py-6"><p class="text-xs text-slate-400 font-medium italic">Tidak ada pengguna aktif lain saat ini.</p></div>';
                            } else {
                                let html = '';
                                const currentUserId = {{ Auth::id() }};
                                data.users.forEach(u => {
                                    const isSelf = u.id == currentUserId;
                                    html += `
                                        <div class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors group">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="relative w-10 h-10 shrink-0">
                                                    <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-xs bg-slate-100 dark:bg-slate-800">
                                                        <img src="${u.avatar_url}" alt="" class="w-full h-full object-cover">
                                                    </div>
                                                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full shadow-xs z-10" title="Online"></span>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-orange-600 transition-colors">${u.name}</p>
                                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">${u.role}</span>
                                                </div>
                                            </div>
                                            ${!isSelf ? `
                                            <a href="${u.chat_url}" class="p-1.5 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 hover:bg-orange-500 hover:text-white transition-all shrink-0" title="Kirim Pesan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            </a>` : ''}
                                        </div>
                                    `;
                                });
                                listContainer.innerHTML = html;
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching online users:', err));
                }

                setInterval(updateOnlineUsersWidget, 10000);
            @endif
        });
    </script>

    @if(Auth::user()->role === 'mahasiswa' && $thesis && !$thesis->isAccSidangFinal())
    <div id="edit-thesis-modal" 
         x-data="{ open: false }" 
         x-show="open" 
         x-cloak
         @open-edit-thesis-modal.window="open = true"
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs transition-opacity" 
                 aria-hidden="true" 
                 @click="open = false; document.getElementById('edit-thesis-modal').classList.add('hidden'); document.getElementById('edit-thesis-modal').style.display = 'none';"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200/80 dark:border-slate-700/80">
                <form action="{{ route('theses.update', $thesis->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700/80 bg-slate-50/70 dark:bg-slate-900/50 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <h3 class="text-base font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight" id="modal-title">Revisi Judul / Deskripsi Skripsi</h3>
                        </div>
                        <button type="button" @click="open = false; document.getElementById('edit-thesis-modal').classList.add('hidden'); document.getElementById('edit-thesis-modal').style.display = 'none';" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="px-6 py-6 space-y-5">
                        <div>
                            <label for="title" class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Judul Skripsi <span class="text-orange-600">*</span></label>
                            <textarea name="title" id="title" rows="3" required class="mt-2 block w-full rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 p-3 text-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all font-medium leading-relaxed">{{ old('title', $thesis->final_title ?? $thesis->title) }}</textarea>
                            @error('title')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="abstract" class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Abstrak / Deskripsi</label>
                            <textarea name="abstract" id="abstract" rows="5" class="mt-2 block w-full rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 p-3 text-slate-900 dark:text-slate-100 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all font-medium leading-relaxed">{{ old('abstract', $thesis->abstract) }}</textarea>
                            @error('abstract')
                                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-slate-50/70 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-end gap-2.5">
                        <button type="button" @click="open = false; document.getElementById('edit-thesis-modal').classList.add('hidden'); document.getElementById('edit-thesis-modal').style.display = 'none';" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-xs cursor-pointer">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-orange-600/20 hover:scale-105 active:scale-95 cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
