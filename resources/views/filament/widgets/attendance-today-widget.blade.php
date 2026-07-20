<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="display:flex;align-items:center;justify-content:center;
                                width:38px;height:38px;border-radius:11px;
                                background:linear-gradient(135deg,#10b981,#059669);
                                box-shadow:0 4px 12px rgba(16,185,129,.3);">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <polyline points="16 11 18 13 22 9" />
                        </svg>
                    </div>
                    <div>
                        <p style="margin:0;font-size:14px;font-weight:700;color:var(--fi-color-gray-950,#111827);">
                            Kehadiran Hari Ini</p>
                        <p
                            style="margin:2px 0 0;font-size:11px;color:var(--fi-color-gray-500,#6b7280);font-weight:400;">
                            {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:6px;padding:4px 10px;border-radius:8px;
                            background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);">
                    <span style="width:6px;height:6px;border-radius:50%;background:#10b981;
                                 display:inline-block;animation:atw-pulse 2s infinite;"></span>
                    <span
                        style="font-size:11px;font-weight:600;color:#059669;">{{ $attended->count() }}/{{ $totalStaff }}
                        hadir</span>
                </div>
            </div>
        </x-slot>

        <style>
            @keyframes atw-pulse {

                0%,
                100% {
                    opacity: 1
                }

                50% {
                    opacity: .3
                }
            }

            /* ── Stats ── */
            .atw-stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                margin-bottom: 16px;
            }

            @media(max-width:480px) {
                .atw-stats {
                    grid-template-columns: 1fr;
                    gap: 8px;
                }
            }

            .atw-stat {
                border-radius: 12px;
                padding: 14px 16px;
                border: 1px solid;
                position: relative;
                overflow: hidden;
                transition: transform .15s;
            }

            .atw-stat:hover {
                transform: translateY(-1px);
            }

            .atw-stat::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                border-radius: 12px 12px 0 0;
            }

            .atw-stat.hadir {
                background: linear-gradient(160deg, #f0fdf4, #dcfce7);
                border-color: #86efac;
            }

            .atw-stat.terlambat {
                background: linear-gradient(160deg, #fffbeb, #fef3c7);
                border-color: #fcd34d;
            }

            .atw-stat.belum {
                background: linear-gradient(160deg, #f8fafc, #f1f5f9);
                border-color: #cbd5e1;
            }

            .atw-stat.hadir::before {
                background: linear-gradient(90deg, #10b981, #34d399);
            }

            .atw-stat.terlambat::before {
                background: linear-gradient(90deg, #f59e0b, #fbbf24);
            }

            .atw-stat.belum::before {
                background: linear-gradient(90deg, #94a3b8, #cbd5e1);
            }

            .dark .atw-stat.hadir {
                background: linear-gradient(160deg, #052e16, #14532d);
                border-color: #166534;
            }

            .dark .atw-stat.terlambat {
                background: linear-gradient(160deg, #1c1408, #3d2e00);
                border-color: #92400e;
            }

            .dark .atw-stat.belum {
                background: linear-gradient(160deg, #1c1917, #292524);
                border-color: #44403c;
            }

            .atw-stat-icon {
                width: 36px;
                height: 36px;
                border-radius: 9px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 10px;
            }

            .atw-stat.hadir .atw-stat-icon {
                background: rgba(16, 185, 129, .15);
            }

            .atw-stat.terlambat .atw-stat-icon {
                background: rgba(245, 158, 11, .15);
            }

            .atw-stat.belum .atw-stat-icon {
                background: rgba(148, 163, 184, .15);
            }

            .atw-stat-val {
                font-size: 26px;
                font-weight: 900;
                line-height: 1;
                letter-spacing: -.03em;
            }

            .atw-stat.hadir .atw-stat-val {
                color: #047857 !important;
            }

            .atw-stat.terlambat .atw-stat-val {
                color: #92400e !important;
            }

            .atw-stat.belum .atw-stat-val {
                color: #475569 !important;
            }

            .dark .atw-stat.hadir .atw-stat-val {
                color: #4ade80 !important;
            }

            .dark .atw-stat.terlambat .atw-stat-val {
                color: #fcd34d !important;
            }

            .dark .atw-stat.belum .atw-stat-val {
                color: #94a3b8 !important;
            }

            .atw-stat-label {
                font-size: 11px;
                font-weight: 600;
                margin-top: 4px;
                color: #4b5563 !important;
            }

            .dark .atw-stat-label {
                color: #a8a29e !important;
            }

            /* ── Progress bar ── */
            .atw-progress-wrap {
                margin-bottom: 16px;
            }

            .atw-progress-label {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 6px;
            }

            .atw-progress-title {
                font-size: 11px;
                font-weight: 700;
                color: #6b7280 !important;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .dark .atw-progress-title {
                color: #78716c !important;
            }

            .atw-progress-pct {
                font-size: 12px;
                font-weight: 800;
                color: #047857 !important;
            }

            .dark .atw-progress-pct {
                color: #4ade80 !important;
            }

            .atw-progress-track {
                height: 8px;
                border-radius: 100px;
                background: #e5e7eb;
                overflow: hidden;
                display: flex;
                gap: 2px;
            }

            .dark .atw-progress-track {
                background: #292524;
            }

            .atw-progress-hadir {
                background: linear-gradient(90deg, #10b981, #34d399);
                border-radius: 100px;
                transition: width .4s;
            }

            .atw-progress-terlambat {
                background: linear-gradient(90deg, #f59e0b, #fbbf24);
                border-radius: 100px;
                transition: width .4s;
            }

            /* ── List ── */
            .atw-list-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 10px;
            }

            .atw-list-title {
                font-size: 11px;
                font-weight: 700;
                color: #6b7280 !important;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .dark .atw-list-title {
                color: #78716c !important;
            }

            .atw-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 32px 16px;
                text-align: center;
            }

            .atw-empty-icon {
                width: 48px;
                height: 48px;
                border-radius: 14px;
                background: linear-gradient(135deg, #f0fdf4, #dcfce7);
                border: 1px solid #86efac;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 10px;
            }

            .dark .atw-empty-icon {
                background: #052e16;
                border-color: #166534;
            }

            .atw-empty-title {
                font-size: 13px;
                font-weight: 700;
                color: #374151 !important;
                margin: 0;
            }

            .dark .atw-empty-title {
                color: #d4d0cb !important;
            }

            .atw-empty-sub {
                font-size: 11px;
                color: #9ca3af !important;
                margin: 4px 0 0;
            }

            .dark .atw-empty-sub {
                color: #78716c !important;
            }

            .atw-list {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .atw-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 12px;
                border-radius: 10px;
                border: 1px solid;
                background: #f9fafb;
                border-color: #e5e7eb;
                transition: background .1s;
            }

            .dark .atw-item {
                background: #1c1917;
                border-color: #292524;
            }

            .atw-item:hover {
                background: #f3f4f6;
            }

            .dark .atw-item:hover {
                background: #111110;
            }

            .atw-avatar {
                width: 34px;
                height: 34px;
                border-radius: 9px;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                font-weight: 800;
                color: #fff !important;
                background: linear-gradient(135deg, #10b981, #059669);
                box-shadow: 0 2px 6px rgba(16, 185, 129, .25);
            }

            .atw-avatar.late {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                box-shadow: 0 2px 6px rgba(245, 158, 11, .25);
            }

            .atw-item-info {
                flex: 1;
                min-width: 0;
            }

            .atw-item-name {
                font-size: 13px;
                font-weight: 700;
                color: #111827 !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .dark .atw-item-name {
                color: #f5f5f4 !important;
            }

            .atw-item-time {
                font-size: 11px;
                color: #6b7280 !important;
                margin-top: 1px;
            }

            .dark .atw-item-time {
                color: #78716c !important;
            }

            .atw-badge {
                flex-shrink: 0;
                padding: 3px 9px;
                border-radius: 100px;
                font-size: 10.5px;
                font-weight: 700;
                border: 1px solid;
            }

            .atw-badge.hadir {
                background: #ecfdf5;
                color: #065f46 !important;
                border-color: #a7f3d0;
            }

            .atw-badge.terlambat {
                background: #fffbeb;
                color: #78350f !important;
                border-color: #fcd34d;
            }

            .atw-rank {
                flex-shrink: 0;
                width: 20px;
                text-align: center;
                font-size: 11px;
                font-weight: 800;
                color: #9ca3af !important;
            }

            .dark .atw-rank {
                color: #57534e !important;
            }

            .atw-rank.top {
                color: #f59e0b !important;
            }

            /* ── Belum absen ── */
            .atw-absent-section {
                margin-top: 14px;
            }

            .atw-absent-title {
                font-size: 11px;
                font-weight: 700;
                color: #6b7280 !important;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .dark .atw-absent-title {
                color: #78716c !important;
            }

            .atw-absent-list {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            .atw-absent-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 5px 10px;
                border-radius: 8px;
                background: #fff1f2;
                border: 1px solid #fecdd3;
                font-size: 11.5px;
                font-weight: 600;
                color: #9f1239 !important;
            }

            .dark .atw-absent-chip {
                background: #3f1d1d;
                border-color: #7f1d1d;
                color: #fda4af !important;
            }

            .atw-absent-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #f43f5e;
                flex-shrink: 0;
            }
        </style>

        @php
            $pct = $totalStaff > 0 ? round($attended->count() / $totalStaff * 100) : 0;
            $pctHadir = $totalStaff > 0 ? round($totalHadir / $totalStaff * 100) : 0;
            $pctLate = $totalStaff > 0 ? round($totalTerlambat / $totalStaff * 100) : 0;


            // User yang belum absen
            // $absentUserIds = $attended->pluck('user_id')->toArray();
            // $absentUsers = \App\Models\User::whereNotIn('id', $absentUserIds)
            //     ->orderBy('name')
            //     ->get();
        @endphp

        {{-- ── Stats ── --}}
        <div class="atw-stats">
            <div class="atw-stat hadir">
                <div class="atw-stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <div class="atw-stat-val">{{ $totalHadir }}</div>
                <div class="atw-stat-label">Tepat Waktu</div>
            </div>
            <div class="atw-stat terlambat">
                <div class="atw-stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <div class="atw-stat-val">{{ $totalTerlambat }}</div>
                <div class="atw-stat-label">Terlambat</div>
            </div>
            <div class="atw-stat belum">
                <div class="atw-stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
                <div class="atw-stat-val">{{ $totalBelum }}</div>
                <div class="atw-stat-label">Belum Absen</div>
            </div>
        </div>

        {{-- ── Progress bar ── --}}
        <div class="atw-progress-wrap">
            <div class="atw-progress-label">
                <span class="atw-progress-title">Tingkat Kehadiran</span>
                <span class="atw-progress-pct">{{ $pct }}%</span>
            </div>
            <div class="atw-progress-track">
                <div class="atw-progress-hadir" style="width:{{ $pctHadir }}%"></div>
                <div class="atw-progress-terlambat" style="width:{{ $pctLate }}%"></div>
            </div>
        </div>

        {{-- ── Daftar yang sudah absen ── --}}
        <div class="atw-list-header">
            <span class="atw-list-title">Sudah Absen ({{ $attended->count() }})</span>
        </div>

        @if($attended->isEmpty())
            <div class="atw-empty">
                <div class="atw-empty-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <polyline points="16 11 18 13 22 9" />
                    </svg>
                </div>
                <p class="atw-empty-title">Belum ada yang absen</p>
                <p class="atw-empty-sub">Data kehadiran hari ini akan muncul di sini.</p>
            </div>
        @else
            <div class="atw-list">
                @foreach($attended as $i => $record)
                    <div class="atw-item">
                        <span class="atw-rank {{ $i < 3 ? 'top' : '' }}">
                            {{ $i < 3 ? ['🥇', '🥈', '🥉'][$i] : ($i + 1) }}
                        </span>
                        <div class="atw-avatar {{ $record->status === 'terlambat' ? 'late' : '' }}">
                            {{ mb_strtoupper(mb_substr($record->user?->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="atw-item-info">
                            <div class="atw-item-name">{{ $record->user?->name ?? 'Pengguna Dihapus' }}</div>
                            <div class="atw-item-time">
                                Masuk {{ \Carbon\Carbon::parse($record->check_in_time)->format('H:i') }}
                                · {{ number_format($record->distance_meters, 0) }} m dari kantor
                            </div>
                        </div>
                        <span class="atw-badge {{ $record->status }}">
                            {{ $record->status === 'terlambat' ? 'Terlambat' : 'Hadir' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ── Yang belum absen ── --}}
        @if($absentUsers->isNotEmpty())
            <div class="atw-absent-section">
                <div class="atw-absent-title">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    Belum Absen ({{ $absentUsers->count() }})
                </div>
                <div class="atw-absent-list">
                    @foreach($absentUsers as $user)
                        <span class="atw-absent-chip">
                            <span class="atw-absent-dot"></span>
                            {{ $user->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
