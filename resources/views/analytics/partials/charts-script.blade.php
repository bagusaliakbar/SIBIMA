    <!-- Chart.js & Datalabels Plugin -->
    <script src="{{ asset('assets/vendor/chartjs/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chartjs/chartjs-plugin-datalabels.min.js') }}"></script>

    <script>
        // Global chart collection for dynamic dark/light mode updates
        window.sibimaCharts = {};

        // Color palettes and theme configs
        function getChartThemeColors(isDark) {
            return {
                textColor: isDark ? '#94a3b8' : '#64748b',
                gridColor: isDark ? 'rgba(255, 255, 255, 0.07)' : 'rgba(0, 0, 0, 0.05)',
                tooltipBg: isDark ? '#0f172a' : '#1e293b',
                tooltipBorder: isDark ? '#334155' : '#cbd5e1',
                tooltipText: '#ffffff',
            };
        }

        // Initialize All Charts
        document.addEventListener('DOMContentLoaded', function () {
            const isDark = document.documentElement.classList.contains('dark');
            const theme = getChartThemeColors(isDark);

            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = theme.textColor;

            // Register ChartDataLabels plugin and configure global 'inside end' defaults
            if (typeof ChartDataLabels !== 'undefined') {
                Chart.register(ChartDataLabels);
                Chart.defaults.plugins.datalabels = {
                    anchor: 'end',
                    align: 'start',
                    color: '#ffffff',
                    font: {
                        family: "'Inter', sans-serif",
                        weight: 'bold',
                        size: 10
                    },
                    offset: 3,
                    clamp: true,
                    textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                    textStrokeWidth: 2,
                    formatter: function(value) {
                        return (value !== null && value !== undefined && value > 0) ? value : '';
                    },
                    display: function(context) {
                        const val = context.dataset.data[context.dataIndex];
                        return val !== null && val !== undefined && val > 0;
                    }
                };
            }

            // 1. Chart Seminar P1 (Horizontal Bar)
            const rawP1 = @json($chartSeminarP1);
            const ctxP1 = document.getElementById('chartSeminarP1').getContext('2d');
            window.sibimaCharts['chartSeminarP1'] = new Chart(ctxP1, {
                type: 'bar',
                data: {
                    labels: rawP1.labels,
                    datasets: [
                        {
                            label: 'Sudah Seminar',
                            data: rawP1.done,
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Seminar',
                            data: rawP1.pending,
                            backgroundColor: '#f59e0b',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { stacked: true, grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } },
                        y: { stacked: true, grid: { display: false }, ticks: { color: theme.textColor, font: { size: 10 } } }
                    }
                }
            });

            // 2. Chart Seminar P2 (Horizontal Bar)
            const rawP2 = @json($chartSeminarP2);
            const ctxP2 = document.getElementById('chartSeminarP2').getContext('2d');
            window.sibimaCharts['chartSeminarP2'] = new Chart(ctxP2, {
                type: 'bar',
                data: {
                    labels: rawP2.labels,
                    datasets: [
                        {
                            label: 'Sudah Seminar',
                            data: rawP2.done,
                            backgroundColor: '#06b6d4',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Seminar',
                            data: rawP2.pending,
                            backgroundColor: '#f97316',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { stacked: true, grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } },
                        y: { stacked: true, grid: { display: false }, ticks: { color: theme.textColor, font: { size: 10 } } }
                    }
                }
            });

            // 3. Chart Cohort Seminar (Stacked Column)
            const rawCohortSem = @json($chartCohortSeminar);
            const ctxCohortSem = document.getElementById('chartCohortSeminar').getContext('2d');
            window.sibimaCharts['chartCohortSeminar'] = new Chart(ctxCohortSem, {
                type: 'bar',
                data: {
                    labels: rawCohortSem.labels,
                    datasets: [
                        {
                            label: 'Sudah Seminar',
                            data: rawCohortSem.done,
                            backgroundColor: '#10b981',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Seminar',
                            data: rawCohortSem.pending,
                            backgroundColor: '#f59e0b',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { stacked: true, grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } }
                    }
                }
            });

            // 4. Chart Unfinished By Advisor (Grouped Horizontal Bar)
            const rawUnfinished = @json($chartUnfinishedByAdvisor);
            const ctxUnfinished = document.getElementById('chartUnfinishedByAdvisor').getContext('2d');
            window.sibimaCharts['chartUnfinishedByAdvisor'] = new Chart(ctxUnfinished, {
                type: 'bar',
                data: {
                    labels: rawUnfinished.labels,
                    datasets: [
                        {
                            label: 'Belum Lulus (P1)',
                            data: rawUnfinished.p1,
                            backgroundColor: '#ef4444',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Lulus (P2)',
                            data: rawUnfinished.p2,
                            backgroundColor: '#0ea5e9',
                            borderRadius: 6,
                        },
                        {
                            label: 'Batas Kuota',
                            data: rawUnfinished.quota,
                            backgroundColor: isDark ? 'rgba(148, 163, 184, 0.35)' : 'rgba(148, 163, 184, 0.4)',
                            borderColor: isDark ? '#94a3b8' : '#64748b',
                            borderWidth: 1.5,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'end',
                            align: 'start',
                            color: function(ctx) {
                                if (ctx.datasetIndex === 2) {
                                    return document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#1e293b';
                                }
                                return '#ffffff';
                            },
                            textStrokeColor: function(ctx) {
                                if (ctx.datasetIndex === 2) {
                                    return document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff';
                                }
                                return 'rgba(0, 0, 0, 0.45)';
                            },
                            textStrokeWidth: 2,
                            font: { weight: 'bold', size: 10 }
                        }
                    },
                    scales: {
                        x: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } },
                        y: { grid: { display: false }, ticks: { color: theme.textColor, font: { size: 10 } } }
                    }
                }
            });

            // 4b. Chart Distribution By Advisor (Stacked Column)
            const rawDistribution = @json($chartDistributionByAdvisor);
            const ctxDistribution = document.getElementById('chartDistributionByAdvisor').getContext('2d');
            window.sibimaCharts['chartDistributionByAdvisor'] = new Chart(ctxDistribution, {
                type: 'bar',
                data: {
                    labels: rawDistribution.labels,
                    datasets: [
                        {
                            label: 'Belum Seminar',
                            data: rawDistribution.belum_seminar,
                            backgroundColor: '#3b82f6',
                            borderRadius: 4,
                        },
                        {
                            label: 'Seminar',
                            data: rawDistribution.seminar,
                            backgroundColor: '#f97316',
                            borderRadius: 4,
                        },
                        {
                            label: 'Sidang Akhir',
                            data: rawDistribution.sidang_akhir,
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                        },
                        {
                            label: 'Kritikal (Sem >= 13)',
                            data: rawDistribution.kritikal,
                            backgroundColor: '#ef4444',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { boxWidth: 12, font: { weight: '600', size: 11 } }
                        },
                        tooltip: {
                            backgroundColor: theme.tooltipBg,
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    return (rawDistribution.full_labels && rawDistribution.full_labels[index])
                                        ? rawDistribution.full_labels[index]
                                        : context[0].label;
                                },
                                afterBody: function(context) {
                                    const index = context[0].dataIndex;
                                    const total = rawDistribution.totals ? rawDistribution.totals[index] : 0;
                                    return 'Total Belum Lulus: ' + total;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 10 },
                            textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                            textStrokeWidth: 2,
                            formatter: function(val) {
                                return (val && val > 0) ? val : '';
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: {
                                color: theme.textColor,
                                font: { size: 10, weight: '600' },
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: theme.gridColor },
                            ticks: { color: theme.textColor, stepSize: 1 }
                        }
                    }
                }
            });

            // 5. Chart Stages (Donut)
            const rawStages = @json($chartStages);
            const ctxStages = document.getElementById('chartStages').getContext('2d');
            window.sibimaCharts['chartStages'] = new Chart(ctxStages, {
                type: 'doughnut',
                data: {
                    labels: rawStages.labels,
                    datasets: [{
                        data: rawStages.data,
                        backgroundColor: [
                            '#f97316', // Judul
                            '#3b82f6', // Bimbingan Bab 1-3
                            '#eab308', // ACC Sempro
                            '#8b5cf6', // Bimbingan Bab 4-5
                            '#06b6d4', // ACC Sidang
                            '#10b981', // Lulus
                        ],
                        borderWidth: isDark ? 2 : 1,
                        borderColor: isDark ? '#1e293b' : '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: '500' } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 11 },
                            textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                            textStrokeWidth: 2
                        }
                    }
                }
            });

            // 6. Chart Cohort Defense (Grouped Column)
            const rawCohortDef = @json($chartCohortDefense);
            const ctxCohortDef = document.getElementById('chartCohortDefense').getContext('2d');
            window.sibimaCharts['chartCohortDefense'] = new Chart(ctxCohortDef, {
                type: 'bar',
                data: {
                    labels: rawCohortDef.labels,
                    datasets: [
                        {
                            label: 'Sudah Sidang / Lulus',
                            data: rawCohortDef.done,
                            backgroundColor: '#6366f1',
                            borderRadius: 6,
                        },
                        {
                            label: 'Belum Sidang',
                            data: rawCohortDef.pending,
                            backgroundColor: '#f43f5e',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } }
                    }
                }
            });

            // 7. Chart Monthly Trends (Smooth Area Line)
            const rawMonthly = @json($chartMonthlyTrends);
            const ctxMonthly = document.getElementById('chartMonthlyTrends').getContext('2d');
            const gradientOrange = ctxMonthly.createLinearGradient(0, 0, 0, 280);
            gradientOrange.addColorStop(0, 'rgba(249, 115, 22, 0.4)');
            gradientOrange.addColorStop(1, 'rgba(249, 115, 22, 0.0)');

            window.sibimaCharts['chartMonthlyTrends'] = new Chart(ctxMonthly, {
                type: 'line',
                data: {
                    labels: rawMonthly.labels,
                    datasets: [
                        {
                            label: 'Sesi Selesai (Hadir)',
                            data: rawMonthly.completed,
                            borderColor: '#ea580c',
                            backgroundColor: gradientOrange,
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointBackgroundColor: '#ea580c',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Sesi Pending / Izin / Lainnya',
                            data: rawMonthly.other,
                            borderColor: '#94a3b8',
                            backgroundColor: 'transparent',
                            borderDash: [5, 5],
                            tension: 0.35,
                            borderWidth: 2,
                            pointBackgroundColor: '#94a3b8',
                            pointRadius: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            offset: 4,
                            color: isDark ? '#f1f5f9' : '#1e293b',
                            textStrokeColor: isDark ? '#0f172a' : '#ffffff',
                            textStrokeWidth: 2,
                            font: { weight: 'bold', size: 10 }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } }
                    }
                }
            });

            // 8. Chart Health (Donut)
            const rawHealth = @json($chartHealth);
            const ctxHealth = document.getElementById('chartHealth').getContext('2d');
            window.sibimaCharts['chartHealth'] = new Chart(ctxHealth, {
                type: 'doughnut',
                data: {
                    labels: rawHealth.labels,
                    datasets: [{
                        data: rawHealth.data,
                        backgroundColor: [
                            '#10b981', // Aktif
                            '#f59e0b', // Waspada
                            '#ef4444', // Kritis
                        ],
                        borderWidth: isDark ? 2 : 1,
                        borderColor: isDark ? '#1e293b' : '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: '500' } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 11 },
                            textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                            textStrokeWidth: 2
                        }
                    }
                }
            });

            // 9. Chart Workload (Horizontal Bar)
            const rawWorkload = @json($chartWorkload);
            const ctxWorkload = document.getElementById('chartWorkload').getContext('2d');
            window.sibimaCharts['chartWorkload'] = new Chart(ctxWorkload, {
                type: 'bar',
                data: {
                    labels: rawWorkload.labels,
                    datasets: [
                        {
                            label: 'Mahasiswa Bimbingan Aktif',
                            data: rawWorkload.active,
                            backgroundColor: '#3b82f6',
                            borderRadius: 6,
                        },
                        {
                            label: 'Batas Kuota',
                            data: rawWorkload.quota,
                            backgroundColor: isDark ? 'rgba(148, 163, 184, 0.35)' : 'rgba(148, 163, 184, 0.4)',
                            borderColor: isDark ? '#94a3b8' : '#64748b',
                            borderWidth: 1.5,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'end',
                            align: 'start',
                            color: function(ctx) {
                                if (ctx.datasetIndex === 1) {
                                    return document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#1e293b';
                                }
                                return '#ffffff';
                            },
                            textStrokeColor: function(ctx) {
                                if (ctx.datasetIndex === 1) {
                                    return document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff';
                                }
                                return 'rgba(0, 0, 0, 0.45)';
                            },
                            textStrokeWidth: 2,
                            font: { weight: 'bold', size: 10 }
                        }
                    },
                    scales: {
                        x: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } },
                        y: { grid: { display: false }, ticks: { color: theme.textColor, font: { size: 10 } } }
                    }
                }
            });

            // 10. Chart Scores (Bar)
            const rawScores = @json($chartScores);
            const ctxScores = document.getElementById('chartScores').getContext('2d');
            window.sibimaCharts['chartScores'] = new Chart(ctxScores, {
                type: 'bar',
                data: {
                    labels: rawScores.labels,
                    datasets: [{
                        label: 'Jumlah Mahasiswa',
                        data: rawScores.data,
                        backgroundColor: [
                            '#10b981', // A
                            '#3b82f6', // B
                            '#f59e0b', // C
                            '#ea580c', // D
                            '#ef4444', // E
                        ],
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor, stepSize: 1 } }
                    }
                }
            });

            // 11. Chart Topics (Polar Area)
            const rawTopics = @json($chartTopics);
            const ctxTopics = document.getElementById('chartTopics').getContext('2d');
            window.sibimaCharts['chartTopics'] = new Chart(ctxTopics, {
                type: 'polarArea',
                data: {
                    labels: rawTopics.labels,
                    datasets: [{
                        data: rawTopics.data,
                        backgroundColor: [
                            'rgba(249, 115, 22, 0.75)',
                            'rgba(99, 102, 241, 0.75)',
                            'rgba(16, 185, 129, 0.75)',
                            'rgba(236, 72, 153, 0.75)',
                            'rgba(14, 165, 233, 0.75)',
                            'rgba(168, 85, 247, 0.75)',
                        ],
                        borderWidth: isDark ? 2 : 1,
                        borderColor: isDark ? '#1e293b' : '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            color: '#ffffff',
                            font: { weight: 'bold', size: 11 },
                            textStrokeColor: 'rgba(0, 0, 0, 0.45)',
                            textStrokeWidth: 2
                        }
                    },
                    scales: {
                        r: {
                            grid: { color: theme.gridColor },
                            ticks: { display: false }
                        }
                    }
                }
            });

            // 12. Chart Wave Duration (Bar)
            const rawWave = @json($chartWaveDuration);
            const ctxWave = document.getElementById('chartWaveDuration').getContext('2d');
            window.sibimaCharts['chartWaveDuration'] = new Chart(ctxWave, {
                type: 'bar',
                data: {
                    labels: rawWave.labels,
                    datasets: [{
                        label: 'Durasi Rata-rata (Bulan)',
                        data: rawWave.data,
                        backgroundColor: '#0d9488',
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { weight: '600', size: 11 } } },
                        tooltip: { backgroundColor: theme.tooltipBg, padding: 10, cornerRadius: 8 },
                        datalabels: {
                            anchor: 'end',
                            align: 'start',
                            formatter: function(val) {
                                return (val && val > 0) ? val + ' bln' : '';
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: theme.textColor } },
                        y: { grid: { color: theme.gridColor }, ticks: { color: theme.textColor } }
                    }
                }
            });

            // Listen for Dark Mode toggle via MutationObserver on <html> class
            const htmlEl = document.documentElement;
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.attributeName === 'class') {
                        const newIsDark = htmlEl.classList.contains('dark');
                        applyThemeToAllCharts(newIsDark);
                    }
                });
            });
            observer.observe(htmlEl, { attributes: true });
        });

        // Function to dynamically refresh all Chart.js colors on dark mode toggle
        function applyThemeToAllCharts(isDark) {
            const theme = getChartThemeColors(isDark);
            Chart.defaults.color = theme.textColor;

            // Update Batas Kuota dataset styling on theme change
            if (window.sibimaCharts['chartUnfinishedByAdvisor']) {
                const c = window.sibimaCharts['chartUnfinishedByAdvisor'];
                if (c.data && c.data.datasets && c.data.datasets[2]) {
                    c.data.datasets[2].backgroundColor = isDark ? 'rgba(148, 163, 184, 0.35)' : 'rgba(148, 163, 184, 0.4)';
                    c.data.datasets[2].borderColor = isDark ? '#94a3b8' : '#64748b';
                }
            }

            if (window.sibimaCharts['chartWorkload']) {
                const c = window.sibimaCharts['chartWorkload'];
                if (c.data && c.data.datasets && c.data.datasets[1]) {
                    c.data.datasets[1].backgroundColor = isDark ? 'rgba(148, 163, 184, 0.35)' : 'rgba(148, 163, 184, 0.4)';
                    c.data.datasets[1].borderColor = isDark ? '#94a3b8' : '#64748b';
                }
            }

            // Update border colors for doughnut & polar charts
            ['chartStages', 'chartHealth', 'chartTopics'].forEach(id => {
                if (window.sibimaCharts[id] && window.sibimaCharts[id].data && window.sibimaCharts[id].data.datasets && window.sibimaCharts[id].data.datasets[0]) {
                    window.sibimaCharts[id].data.datasets[0].borderColor = isDark ? '#1e293b' : '#ffffff';
                    window.sibimaCharts[id].data.datasets[0].borderWidth = isDark ? 2 : 1;
                }
            });

            Object.values(window.sibimaCharts).forEach(chart => {
                if (!chart) return;

                if (chart.options.scales) {
                    if (chart.options.scales.x) {
                        if (chart.options.scales.x.ticks) chart.options.scales.x.ticks.color = theme.textColor;
                        if (chart.options.scales.x.grid) chart.options.scales.x.grid.color = theme.gridColor;
                    }
                    if (chart.options.scales.y) {
                        if (chart.options.scales.y.ticks) chart.options.scales.y.ticks.color = theme.textColor;
                        if (chart.options.scales.y.grid) chart.options.scales.y.grid.color = theme.gridColor;
                    }
                    if (chart.options.scales.r && chart.options.scales.r.grid) {
                        chart.options.scales.r.grid.color = theme.gridColor;
                    }
                }

                if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                    chart.options.plugins.legend.labels.color = theme.textColor;
                }

                if (chart.options.plugins && chart.options.plugins.tooltip) {
                    chart.options.plugins.tooltip.backgroundColor = theme.tooltipBg;
                }

                if (chart.options.plugins && chart.options.plugins.datalabels) {
                    if (chart.canvas && chart.canvas.id === 'chartMonthlyTrends') {
                        chart.options.plugins.datalabels.color = isDark ? '#f1f5f9' : '#1e293b';
                        chart.options.plugins.datalabels.textStrokeColor = isDark ? '#0f172a' : '#ffffff';
                    }
                }

                chart.update();
            });
        }

        // Function to download a single chart as high-resolution PNG with solid background
        function downloadChartAsPng(canvasId, fileName) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            const isDark = document.documentElement.classList.contains('dark');
            const bgColor = isDark ? '#1e293b' : '#ffffff';

            // Create temporary canvas to paint solid background before saving
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = canvas.width;
            tempCanvas.height = canvas.height;
            const tempCtx = tempCanvas.getContext('2d');

            // Draw solid background
            tempCtx.fillStyle = bgColor;
            tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

            // Draw chart on top
            tempCtx.drawImage(canvas, 0, 0);

            // Trigger download
            const link = document.createElement('a');
            link.download = (fileName || 'grafik-sibima') + '-' + new Date().toISOString().slice(0, 10) + '.png';
            link.href = tempCanvas.toDataURL('image/png', 1.0);
            link.click();
        }

        // Function to download all charts sequentially as PNG
        function downloadAllChartsAsPng() {
            const chartIds = [
                { id: 'chartSeminarP1', name: 'grafik-seminar-pembimbing-1' },
                { id: 'chartSeminarP2', name: 'grafik-seminar-pembimbing-2' },
                { id: 'chartCohortSeminar', name: 'grafik-seminar-per-angkatan' },
                { id: 'chartUnfinishedByAdvisor', name: 'grafik-belum-lulus-per-pembimbing' },
                { id: 'chartDistributionByAdvisor', name: 'grafik-distribusi-bimbingan-per-dosen' },
                { id: 'chartStages', name: 'grafik-tahapan-skripsi' },
                { id: 'chartCohortDefense', name: 'grafik-sidang-per-angkatan' },
                { id: 'chartMonthlyTrends', name: 'grafik-tren-bimbingan-bulanan' },
                { id: 'chartHealth', name: 'grafik-keaktifan-bimbingan' },
                { id: 'chartWorkload', name: 'grafik-beban-kuota-dosen' },
                { id: 'chartScores', name: 'grafik-distribusi-nilai-sidang' },
                { id: 'chartTopics', name: 'grafik-topik-skripsi' },
                { id: 'chartWaveDuration', name: 'grafik-durasi-gelombang' },
            ];

            let delay = 0;
            chartIds.forEach(item => {
                setTimeout(() => {
                    downloadChartAsPng(item.id, item.name);
                }, delay);
                delay += 300; // staggering to allow browser download handling
            });
        }
    </script>
