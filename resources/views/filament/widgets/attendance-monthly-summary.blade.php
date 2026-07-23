<x-filament-widgets::widget>
    <x-filament::section>

        <style>
            /* ── Header ── */
            .ams-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 20px;
                flex-wrap: wrap;
            }
            .ams-title { margin: 0; font-size: 15px; font-weight: 700; color: inherit; }
            .ams-subtitle { margin: 3px 0 0; font-size: 11.5px; color: #9ca3af; }

            /* ── Filter group ── */
            .ams-filters {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                align-items: center;
            }
            .ams-select {
                padding: 6px 10px;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #111827;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
                transition: border-color .12s;
                outline: none;
            }
            .ams-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
            .dark .ams-select {
                background: #1c1917;
                border-color: #292524;
                color: #f5f5f4;
            }

            /* ── Stat cards grid ── */
            .ams-stats {
                display: grid;
                grid-template-columns: repeat(6, 1fr);
                gap: 10px;
                margin-bottom: 18px;
            }
            @media (max-width: 1024px) { .ams-stats { grid-template-columns: repeat(3, 1fr); } }
            @media (max-width: 640px)  { .ams-stats { grid-template-columns: repeat(2, 1fr); } }

            .ams-card {
                border-radius: 14px;
                padding: 16px;
                border: 1px solid;
                position: relative;
                overflow: hidden;
                transition: transform .15s, box-shadow .15s;
            }
            .ams-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.08); }

            /* Top accent */
            .ams-card::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 3px;
                border-radius: 14px 14px 0 0;
            }

            /* Color variants */
            .ams-card.gray     { background: linear-gradient(160deg,#f9fafb,#f3f4f6); border-color: #e5e7eb; }
            .ams-card.green    { background: linear-gradient(160deg,#f0fdf4,#dcfce7); border-color: #86efac; }
            .ams-card.yellow   { background: linear-gradient(160deg,#fffbeb,#fef3c7); border-color: #fcd34d; }
            .ams-card.blue     { background: linear-gradient(160deg,#eff6ff,#dbeafe); border-color: #93c5fd; }
            .ams-card.red      { background: linear-gradient(160deg,#fff1f2,#fecdd3); border-color: #fca5a5; }
            .ams-card.slate    { background: linear-gradient(160deg,#f8fafc,#f1f5f9); border-color: #cbd5e1; }

            .ams-card.gray::before  { background: linear-gradient(90deg,#6b7280,#9ca3af); }
            .ams-card.green::before { background: linear-gradient(90deg,#10b981,#34d399); }
            .ams-card.yellow::before{ background: linear-gradient(90deg,#f59e0b,#fbbf24); }
            .ams-card.blue::before  { background: linear-gradient(90deg,#3b82f6,#60a5fa); }
            .ams-card.red::before   { background: linear-gradient(90deg,#ef4444,#f87171); }
            .ams-card.slate::before { background: linear-gradient(90deg,#94a3b8,#cbd5e1); }

            /* Dark variants */
            .dark .ams-card.gray   { background: linear-gradient(160deg,#1c1917,#292524); border-color: #44403c; }
            .dark .ams-card.green  { background: linear-gradient(160deg,#052e16,#14532d); border-color: #166534; }
            .dark .ams-card.yellow { background: linear-gradient(160deg,#1c1408,#3d2e00); border-color: #92400e; }
            .dark .ams-card.blue   { background: linear-gradient(160deg,#0c1a33,#1e3a5f); border-color: #1e40af; }
            .dark .ams-card.red    { background: linear-gradient(160deg,#1a0505,#3f1d1d); border-color: #7f1d1d; }
            .dark .ams-card.slate  { background: linear-gradient(160deg,#1c1917,#292524); border-color: #44403c; }

            .ams-card-icon {
                width: 34px; height: 34px; border-radius: 9px;
                display: flex; align-items: center; justify-content: center;
                margin-bottom: 12px;
            }
            .ams-card.gray   .ams-card-icon { background: rgba(107,114,128,.12); }
            .ams-card.green  .ams-card-icon { background: rgba(16,185,129,.15); }
            .ams-card.yellow .ams-card-icon { background: rgba(245,158,11,.15); }
            .ams-card.blue   .ams-card-icon { background: rgba(59,130,246,.12); }
            .ams-card.red    .ams-card-icon { background: rgba(239,68,68,.12); }
            .ams-card.slate  .ams-card-icon { background: rgba(148,163,184,.15); }

            .ams-card-val {
                font-size: 28px; font-weight: 900;
                line-height: 1; letter-spacing: -.04em;
            }
            .ams-card.gray   .ams-card-val { color: #374151 !important; }
            .ams-card.green  .ams-card-val { color: #047857 !important; }
            .ams-card.yellow .ams-card-val { color: #92400e !important; }
            .ams-card.blue   .ams-card-val { color: #1e40af !important; }
            .ams-card.red    .ams-card-val { color: #991b1b !important; }
            .ams-card.slate  .ams-card-val { color: #475569 !important; }

            .dark .ams-card.gray   .ams-card-val { color: #d4d0cb !important; }
            .dark .ams-card.green  .ams-card-val { color: #4ade80 !important; }
            .dark .ams-card.yellow .ams-card-val { color: #fcd34d !important; }
            .dark .ams-card.blue   .ams-card-val { color: #60a5fa !important; }
            .dark .ams-card.red    .ams-card-val { color: #f87171 !important; }
            .dark .ams-card.slate  .ams-card-val { color: #94a3b8 !important; }

            .ams-card-label {
                font-size: 11px; font-weight: 600;
                margin-top: 5px; color: #6b7280 !important;
                text-transform: uppercase; letter-spacing: .05em;
            }
            .dark .ams-card-label { color: #78716c !important; }

            /* ── Progress section ── */
            .ams-progress-wrap {
                border-radius: 16px;
                border: 1px solid #e5e7eb;
                background: #f9fafb;
                padding: 20px;
            }
            .dark .ams-progress-wrap { background: #1c1917; border-color: #292524; }

            .ams-progress-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 10px;
            }
            .ams-progress-label {
                font-size: 12px; font-weight: 700;
                color: #374151 !important; text-transform: uppercase; letter-spacing: .07em;
            }
            .dark .ams-progress-label { color: #d4d0cb !important; }

            .ams-progress-pct {
                font-size: 20px; font-weight: 900; letter-spacing: -.03em;
            }
            .ams-progress-pct.good   { color: #047857 !important; }
            .ams-progress-pct.medium { color: #92400e !important; }
            .ams-progress-pct.bad    { color: #991b1b !important; }
            .dark .ams-progress-pct.good   { color: #4ade80 !important; }
            .dark .ams-progress-pct.medium { color: #fcd34d !important; }
            .dark .ams-progress-pct.bad    { color: #f87171 !important; }

            .ams-track {
                height: 10px; border-radius: 100px;
                background: #e5e7eb; overflow: hidden;
                display: flex; gap: 2px;
            }
            .dark .ams-track { background: #292524; }

            .ams-bar-hadir {
                border-radius: 100px;
                transition: width .5s ease;
                background: linear-gradient(90deg, #10b981, #34d399);
            }
            .ams-bar-terlambat {
                border-radius: 100px;
                transition: width .5s ease;
                background: linear-gradient(90deg, #f59e0b, #fbbf24);
            }

            /* Info chips below progress */
            .ams-info-row {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 14px;
            }
            .ams-info-chip {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 4px 10px;
                border-radius: 100px;
                font-size: 11.5px;
                font-weight: 600;
                background: #fff;
                border: 1px solid #e5e7eb;
                color: #374151 !important;
            }
            .dark .ams-info-chip { background: #292524; border-color: #44403c; color: #d4d0cb !important; }
            .ams-info-chip.warn {
                background: #fef2f2; border-color: #fca5a5; color: #991b1b !important;
            }
            .dark .ams-info-chip.warn { background: #3f1d1d; border-color: #7f1d1d; color: #f87171 !important; }
        </style>

        @php $s = $this->getSummary(); $isSuperadmin = Auth::user()->role === 'superadmin'; @endphp

        {{-- ── Header ── --}}
        <div class="ams-header">
            <div>
                <p class="ams-title">Ringkasan Kehadiran Bulanan</p>
                <p class="ams-subtitle">
                    {{ $this->getMonthLabel() }} {{ $this->selectedYear }}
                    &nbsp;·&nbsp; {{ $this->getSelectedUserName() }}
                </p>
            </div>

            <div class="ams-filters">
                {{-- Bulan --}}
                <select wire:model.live="selectedMonth" class="ams-select">
                    @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $v => $l)
                        <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>

                {{-- Tahun --}}
                <select wire:model.live="selectedYear" class="ams-select">
                    @foreach(range(now()->year, now()->year - 3) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                {{-- Karyawan — hanya superadmin --}}
                @if($isSuperadmin)
                    <select wire:model.live="selectedUserId" class="ams-select" style="min-width:160px;">
                        <option value="">Semua Karyawan</option>
                        @foreach($this->getUsers() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>

        {{-- ── Stat cards ── --}}
        <div class="ams-stats">

            <div class="ams-card gray">
                <div class="ams-card-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="ams-card-val">{{ $s['hari_kerja'] }}</div>
                <div class="ams-card-label">Hari Kerja</div>
            </div>

            <div class="ams-card green">
                <div class="ams-card-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div class="ams-card-val">{{ $s['hadir'] }}</div>
                <div class="ams-card-label">Hadir</div>
            </div>

            <div class="ams-card yellow">
                <div class="ams-card-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="ams-card-val">{{ $s['terlambat'] }}</div>
                <div class="ams-card-label">Terlambat</div>
            </div>

            <div class="ams-card blue">
                <div class="ams-card-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <div class="ams-card-val">{{ $s['izin'] }}</div>
                <div class="ams-card-label">Izin</div>
            </div>

            <div class="ams-card red">
                <div class="ams-card-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div class="ams-card-val">{{ $s['alpha'] }}</div>
                <div class="ams-card-label">Alpha</div>
            </div>

            <div class="ams-card slate">
                <div class="ams-card-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div class="ams-card-val">{{ $s['tidak_tercatat'] }}</div>
                <div class="ams-card-label">Tdk Tercatat</div>
            </div>

        </div>

        {{-- ── Progress ── --}}
        @php
            $pctClass = $s['persentase'] >= 80 ? 'good' : ($s['persentase'] >= 60 ? 'medium' : 'bad');
            $pctHadir = $s['hari_kerja'] > 0 ? round($s['hadir'] / $s['hari_kerja'] * 100) : 0;
            $pctLate  = $s['hari_kerja'] > 0 ? round($s['terlambat'] / $s['hari_kerja'] * 100) : 0;
        @endphp

        <div class="ams-progress-wrap">
            <div class="ams-progress-top">
                <span class="ams-progress-label">Tingkat Kehadiran</span>
                <span class="ams-progress-pct {{ $pctClass }}">{{ $s['persentase'] }}%</span>
            </div>

            <div class="ams-track">
                <div class="ams-bar-hadir"     style="width:{{ $pctHadir }}%"></div>
                <div class="ams-bar-terlambat" style="width:{{ $pctLate }}%"></div>
            </div>

            <div class="ams-info-row">
                <span class="ams-info-chip">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $s['total_hadir'] }} dari {{ $s['hari_kerja'] }} hari hadir fisik
                </span>
                <span class="ams-info-chip">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Rata-rata check-in: <strong>{{ $s['avg_check_in'] }}</strong>
                </span>
                @if($s['persentase'] < 80)
                    <span class="ams-info-chip warn">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Di bawah standar minimum 80%
                    </span>
                @endif
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
