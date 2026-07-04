<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="display:flex; align-items:center; justify-content:center;
                            width:36px; height:36px; border-radius:10px;
                            background:linear-gradient(135deg,#eff6ff,#dbeafe);
                            border:1px solid #bfdbfe;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </div>
                <div style="line-height:1.3;">
                    <p style="margin:0; font-size:14px; font-weight:600; color:inherit;">Transaksi Bulan Ini</p>
                    <p style="margin:0; font-size:11px; color:#a8a29e; font-weight:400; margin-top:1px;">
                        {{ $bulan }}
                    </p>
                </div>
            </div>
        </x-slot>

        <style>
            .tc-stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                margin-bottom: 18px;
            }
            .tc-stat {
                background: #faf9f7;
                border: 1px solid #e7e5e4;
                border-radius: 10px;
                padding: 12px 14px;
                position: relative;
                overflow: hidden;
            }
            .tc-stat-bar {
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 3px;
                border-radius: 10px 10px 0 0;
            }
            .tc-stat-label {
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .07em;
                color: #a8a29e;
                margin-bottom: 5px;
            }
            .tc-stat-val {
                font-size: 14px;
                font-weight: 700;
                color: #1c1917;
                line-height: 1.2;
            }
            .tc-stat-sub {
                font-size: 10px;
                color: #c4bfbb;
                margin-top: 2px;
            }
            .tc-chart-wrap {
                position: relative;
                height: 220px;
            }
            @media (prefers-color-scheme: dark) {
                .tc-stat     { background: #1c1917; border-color: #292524; }
                .tc-stat-val { color: #fafaf9; }
            }
        </style>

        {{-- Stats --}}
        <div class="tc-stats">
            <div class="tc-stat">
                <div class="tc-stat-bar" style="background:linear-gradient(90deg,#3b82f6,#60a5fa);"></div>
                <div class="tc-stat-label">Total Bulan Ini</div>
                <div class="tc-stat-val">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</div>
                <div class="tc-stat-sub">semua transaksi</div>
            </div>
            <div class="tc-stat">
                <div class="tc-stat-bar" style="background:linear-gradient(90deg,#8b5cf6,#a78bfa);"></div>
                <div class="tc-stat-label">Rata-rata Harian</div>
                <div class="tc-stat-val">Rp {{ number_format($rataHarian, 0, ',', '.') }}</div>
                <div class="tc-stat-sub">per hari aktif</div>
            </div>
            <div class="tc-stat">
                <div class="tc-stat-bar" style="background:linear-gradient(90deg,#22c55e,#4ade80);"></div>
                <div class="tc-stat-label">Hari Tertinggi</div>
                <div class="tc-stat-val">
                    @if($hariTertinggi) Tgl {{ $hariTertinggi->day }} @else — @endif
                </div>
                <div class="tc-stat-sub">
                    @if($hariTertinggi) Rp {{ number_format($hariTertinggi->total, 0, ',', '.') }}
                    @else belum ada data @endif
                </div>
            </div>
        </div>

        {{-- Chart canvas --}}
        <div class="tc-chart-wrap">
            <canvas id="tc-chart-{{ $this->getId() }}"></canvas>
        </div>

        {{-- Load Chart.js from CDN then init --}}
        <script>
        (function () {
            var CANVAS_ID = 'tc-chart-{{ $this->getId() }}';
            var labels    = {!! $labels !!};
            var values    = {!! $values !!};

            function buildChart() {
                var canvas = document.getElementById(CANVAS_ID);
                if (!canvas) return;

                var dark = document.documentElement.classList.contains('dark');
                var ctx  = canvas.getContext('2d');

                var grad = ctx.createLinearGradient(0, 0, 0, 200);
                grad.addColorStop(0,   'rgba(59,130,246,.30)');
                grad.addColorStop(0.7, 'rgba(59,130,246,.05)');
                grad.addColorStop(1,   'rgba(59,130,246,0)');

                var gridColor  = dark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.05)';
                var labelColor = dark ? '#78716c' : '#a8a29e';
                var tooltipBg  = dark ? '#1c1917' : '#ffffff';
                var tooltipBorder = dark ? '#292524' : '#e7e5e4';
                var tooltipTitle  = dark ? '#a8a29e' : '#78716c';
                var tooltipBody   = dark ? '#fafaf9' : '#1c1917';

                if (window['_tc_' + CANVAS_ID]) {
                    window['_tc_' + CANVAS_ID].destroy();
                }

                window['_tc_' + CANVAS_ID] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pembayaran',
                            data: values,
                            fill: true,
                            backgroundColor: grad,
                            borderColor: '#3b82f6',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#3b82f6',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#3b82f6',
                            pointHoverBorderColor: '#ffffff',
                            tension: 0.4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: tooltipBg,
                                borderColor: tooltipBorder,
                                borderWidth: 1,
                                titleColor: tooltipTitle,
                                bodyColor: tooltipBody,
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    title: function(ctx) { return 'Tanggal ' + ctx[0].label; },
                                    label: function(ctx) {
                                        return ' Rp ' + Number(ctx.parsed.y).toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: gridColor },
                                border: { display: false },
                                ticks: { color: labelColor, font: { size: 11 }, maxTicksLimit: 15 }
                            },
                            y: {
                                grid: { color: gridColor },
                                border: { display: false },
                                ticks: {
                                    color: labelColor,
                                    font: { size: 11 },
                                    callback: function(v) {
                                        if (v >= 1000000) return 'Rp ' + (v/1000000).toFixed(1) + 'jt';
                                        if (v >= 1000)    return 'Rp ' + (v/1000).toFixed(0) + 'rb';
                                        return 'Rp ' + v;
                                    }
                                }
                            }
                        }
                    }
                });

                // Rebuild on dark mode toggle
                var observer = new MutationObserver(function() { buildChart(); });
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            }

            function loadChartJs(cb) {
                // If already loaded, just run
                if (typeof Chart !== 'undefined') { cb(); return; }

                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js';
                script.onload = cb;
                document.head.appendChild(script);
            }

            function init() {
                loadChartJs(buildChart);
            }

            // Run after DOM ready or immediately if already ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }

            // Livewire SPA navigation
            document.addEventListener('livewire:navigated', init);
        })();
        </script>

    </x-filament::section>
</x-filament-widgets::widget>
