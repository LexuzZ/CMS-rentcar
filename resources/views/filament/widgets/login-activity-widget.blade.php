<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="display:flex;align-items:center;justify-content:center;
                                width:38px;height:38px;border-radius:11px;
                                background:linear-gradient(135deg,#7c3aed,#6d28d9);
                                box-shadow:0 4px 12px rgba(124,58,237,.3);">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" y1="12" x2="3" y2="12" />
                        </svg>
                    </div>
                    <div>
                        <p style="margin:0;font-size:14px;font-weight:700;color:var(--fi-color-gray-950,#111827);">Login
                            Activity</p>
                        <p
                            style="margin:2px 0 0;font-size:11px;color:var(--fi-color-gray-500,#6b7280);font-weight:400;">
                            Riwayat aktivitas login pengguna
                        </p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:6px;
                            padding:4px 10px;border-radius:8px;
                            background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.3);">
                    <span style="width:6px;height:6px;border-radius:50%;background:#7c3aed;
                                 display:inline-block;animation:la-pulse 2s infinite;"></span>
                    <span style="font-size:11px;font-weight:600;color:#a78bfa;">Live</span>
                </div>
            </div>
        </x-slot>

        <style>
            @keyframes la-pulse {

                0%,
                100% {
                    opacity: 1
                }

                50% {
                    opacity: .3
                }
            }

            .la-stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
                margin-bottom: 18px;
            }

            @media(max-width:640px) {
                .la-stats {
                    grid-template-columns: 1fr
                }
            }

            .la-stat {
                border-radius: 14px;
                padding: 18px;
                border: 1px solid;
                position: relative;
                overflow: hidden;
                transition: transform .15s, box-shadow .15s;
            }

            .la-stat:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
            }

            .la-stat.success {
                background: linear-gradient(160deg, #f0fdf4, #dcfce7);
                border-color: #86efac;
            }

            .la-stat.failed {
                background: linear-gradient(160deg, #fff1f2, #fecdd3);
                border-color: #fca5a5;
            }

            .la-stat.users {
                background: linear-gradient(160deg, #f5f3ff, #ede9fe);
                border-color: #c4b5fd;
            }

            .la-stat::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                border-radius: 14px 14px 0 0;
            }

            .la-stat.success::before {
                background: linear-gradient(90deg, #10b981, #34d399);
            }

            .la-stat.failed::before {
                background: linear-gradient(90deg, #ef4444, #f87171);
            }

            .la-stat.users::before {
                background: linear-gradient(90deg, #7c3aed, #a78bfa);
            }

            .la-stat-bg-icon {
                position: absolute;
                right: -6px;
                bottom: -6px;
                opacity: .08;
                pointer-events: none;
            }

            .la-stat-top {
                display: flex;
                margin-bottom: 14px;
            }

            .la-stat-icon-wrap {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .la-stat.success .la-stat-icon-wrap {
                background: rgba(16, 185, 129, .15);
            }

            .la-stat.failed .la-stat-icon-wrap {
                background: rgba(239, 68, 68, .12);
            }

            .la-stat.users .la-stat-icon-wrap {
                background: rgba(124, 58, 237, .12);
            }

            .la-stat-val {
                font-size: 32px;
                font-weight: 900;
                line-height: 1;
                letter-spacing: -.04em;
            }

            .la-stat.success .la-stat-val {
                color: #047857;
            }

            .la-stat.failed .la-stat-val {
                color: #b91c1c;
            }

            .la-stat.users .la-stat-val {
                color: #5b21b6;
            }

            .la-stat-label {
                font-size: 11.5px;
                font-weight: 600;
                margin-top: 5px;
                color: #4b5563;
            }

            .dark .la-stat.success {
                background: linear-gradient(160deg, #052e16, #14532d);
                border-color: #166534;
            }

            .dark .la-stat.failed {
                background: linear-gradient(160deg, #1a0505, #3f1d1d);
                border-color: #7f1d1d;
            }

            .dark .la-stat.users {
                background: linear-gradient(160deg, #1e0a38, #2e1065);
                border-color: #4c1d95;
            }

            .dark .la-stat.success .la-stat-icon-wrap {
                background: rgba(16, 185, 129, .2);
            }

            .dark .la-stat.failed .la-stat-icon-wrap {
                background: rgba(239, 68, 68, .2);
            }

            .dark .la-stat.users .la-stat-icon-wrap {
                background: rgba(124, 58, 237, .2);
            }

            .dark .la-stat.success .la-stat-val {
                color: #4ade80;
            }

            .dark .la-stat.failed .la-stat-val {
                color: #f87171;
            }

            .dark .la-stat.users .la-stat-val {
                color: #c4b5fd;
            }

            .dark .la-stat-label {
                color: #a8a29e;
            }

            /* ── Filter bar ── */
            .la-filter-bar {
                display: flex;
                align-items: center;
                gap: 6px;
                margin-bottom: 14px;
                padding: 6px 8px;
                border-radius: 12px;
                flex-wrap: wrap;
                background: #f3f4f6;
                border: 1px solid #e5e7eb;
            }

            .dark .la-filter-bar {
                background: #1c1917;
                border-color: #292524;
            }

            .la-filter-group {
                display: flex;
                gap: 2px;
                border-radius: 8px;
                padding: 3px;
                box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
                background: #ffffff;
                border: 1px solid #e5e7eb;
            }

            .dark .la-filter-group {
                background: #111110;
                border-color: #292524;
                box-shadow: none;
            }

            .la-fbtn {
                padding: 4px 12px;
                border-radius: 6px;
                font-size: 11.5px;
                font-weight: 600;
                border: none;
                background: transparent;
                cursor: pointer;
                transition: all .12s;
                color: #4b5563;
            }

            .dark .la-fbtn {
                color: #a8a29e;
            }

            .la-fbtn:hover {
                color: #111827;
                background: #f9fafb;
            }

            .dark .la-fbtn:hover {
                color: #e5e7eb;
                background: transparent;
            }

            .la-fbtn.fp-active {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: #fff !important;
                box-shadow: 0 2px 8px rgba(124, 58, 237, .3);
            }

            .la-fbtn.fs-active-all {
                background: #1f2937;
                color: #fff !important;
            }

            .la-fbtn.fs-active-success {
                background: #059669;
                color: #fff !important;
            }

            .la-fbtn.fs-active-failed {
                background: #dc2626;
                color: #fff !important;
            }

            .la-filter-divider {
                width: 1px;
                height: 22px;
                background: #e5e7eb;
                margin: 0 2px;
                flex-shrink: 0;
            }

            .dark .la-filter-divider {
                background: #44403c;
            }

            .la-count-badge {
                margin-left: auto;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 7px;
                background: #fff;
                border: 1px solid #e5e7eb;
                color: #374151;
                box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            }

            .dark .la-count-badge {
                background: #292524;
                border-color: #44403c;
                color: #d4d0cb;
            }

            /* ── Table ── */
            .la-table-wrap {
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                overflow: hidden;
                box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
            }

            .dark .la-table-wrap {
                border-color: #292524;
                box-shadow: none;
            }

            .la-table {
                width: 100%;
                border-collapse: collapse;
            }

            .la-thead-row {
                background: #f3f4f6;
            }

            .dark .la-thead-row {
                background: #1c1917;
            }

            .la-th {
                padding: 10px 16px;
                font-size: 9.5px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: .1em;
                color: #6b7280;
                border-bottom: 1px solid #e5e7eb;
                text-align: left;
            }

            .dark .la-th {
                color: #78716c;
                border-color: #292524;
            }

            .la-tr {
                border-bottom: 1px solid #f3f4f6;
                transition: background .1s;
            }

            .dark .la-tr {
                border-color: #1c1917;
            }

            .la-tr:last-child {
                border-bottom: none;
            }

            .la-tr:hover {
                background: #f9fafb;
            }

            .dark .la-tr:hover {
                background: #111110;
            }

            .la-td {
                padding: 12px 16px;
                vertical-align: middle;
            }

            .la-avatar {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                flex-shrink: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                font-weight: 800;
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                color: #fff !important;
                box-shadow: 0 2px 8px rgba(124, 58, 237, .3);
            }

            .la-user-cell {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .la-user-name {
                font-size: 13px;
                font-weight: 700;
                color: #111827 !important;
                line-height: 1.3;
            }

            .dark .la-user-name {
                color: #f5f5f4 !important;
            }

            .la-user-email {
                font-size: 11px;
                color: #6b7280 !important;
                margin-top: 2px;
            }

            .dark .la-user-email {
                color: #78716c !important;
            }

            .la-status {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 5px 11px;
                border-radius: 100px;
                font-size: 11px;
                font-weight: 700;
                border: 1px solid;
                white-space: nowrap;
            }

            .la-status.success {
                background: #ecfdf5;
                color: #065f46 !important;
                border-color: #a7f3d0;
            }

            .la-status.failed {
                background: #fef2f2;
                color: #991b1b !important;
                border-color: #fca5a5;
            }

            .la-status-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                flex-shrink: 0;
            }

            .la-status.success .la-status-dot {
                background: #10b981;
                box-shadow: 0 0 0 2px rgba(16, 185, 129, .2);
            }

            .la-status.failed .la-status-dot {
                background: #ef4444;
                box-shadow: 0 0 0 2px rgba(239, 68, 68, .2);
            }

            .la-chips {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
            }

            .la-chip {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 4px 9px;
                border-radius: 6px;
                font-size: 10.5px;
                font-weight: 600;
                background: #f3f4f6;
                color: #374151 !important;
                border: 1px solid #e5e7eb;
            }

            .dark .la-chip {
                background: #292524;
                color: #d4d0cb !important;
                border-color: #44403c;
            }

            .la-chip svg {
                opacity: .6;
                flex-shrink: 0;
            }

            .la-time-rel {
                font-size: 12px;
                font-weight: 700;
                color: #111827 !important;
            }

            .dark .la-time-rel {
                color: #f5f5f4 !important;
            }

            .la-time-abs {
                font-size: 10.5px;
                color: #9ca3af !important;
                margin-top: 2px;
            }

            .dark .la-time-abs {
                color: #57534e !important;
            }

            .la-load-more {
                display: flex;
                justify-content: center;
                padding: 14px;
                background: #f9fafb;
                border-top: 1px solid #e5e7eb;
            }

            .dark .la-load-more {
                background: #1c1917;
                border-color: #292524;
            }

            .la-load-btn {
                padding: 8px 24px;
                border-radius: 9px;
                font-size: 12px;
                font-weight: 700;
                background: #fff;
                border: 1px solid #e5e7eb;
                color: #374151 !important;
                cursor: pointer;
                transition: all .12s;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
            }

            .dark .la-load-btn {
                background: #292524;
                border-color: #44403c;
                color: #e5e7eb !important;
            }

            .la-load-btn:hover {
                background: #faf5ff;
                border-color: #c4b5fd;
                color: #5b21b6 !important;
                box-shadow: 0 3px 10px rgba(124, 58, 237, .15);
            }

            .dark .la-load-btn:hover {
                background: #3c3836;
                border-color: #c4b5fd;
                color: #c4b5fd !important;
            }

            .la-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 52px 20px;
                text-align: center;
            }

            .la-empty-icon {
                width: 56px;
                height: 56px;
                border-radius: 16px;
                background: linear-gradient(135deg, #f5f3ff, #ede9fe);
                border: 1px solid #c4b5fd;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 14px;
                box-shadow: 0 4px 16px rgba(124, 58, 237, .12);
            }

            .dark .la-empty-icon {
                background: #1e0a38;
                border-color: #4c1d95;
            }

            .la-empty-title {
                font-size: 14px;
                font-weight: 700;
                color: #374151 !important;
                margin: 0;
            }

            .dark .la-empty-title {
                color: #d4d0cb !important;
            }

            .la-empty-sub {
                font-size: 12px;
                color: #9ca3af !important;
                margin: 5px 0 0;
            }

            .dark .la-empty-sub {
                color: #78716c !important;
            }

            /* ── Mobile responsif ── */
            @media(max-width:640px) {
                .la-stats {
                    grid-template-columns: 1fr;
                }

                /* Filter bar: biarkan wrap alami, badge turun ke baris baru */
                .la-filter-bar {
                    gap: 4px;
                    padding: 5px 6px;
                }

                .la-count-badge {
                    margin-left: 0;
                    width: 100%;
                    text-align: center;
                }

                /* Filter divider: sembunyikan dan ganti dengan margin */
                .la-filter-divider {
                    display: none;
                }

                .la-filter-group {
                    flex-wrap: wrap;
                }

                .la-fbtn {
                    padding: 4px 9px;
                    font-size: 11px;
                }

                /* Table: scroll horizontal agar tidak gepeng */
                .la-table-wrap {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                    border-radius: 10px;
                }

                .la-table {
                    min-width: 500px;
                }

                /* Kurangi padding sel agar lebih rapat */
                .la-th {
                    padding: 8px 10px;
                    font-size: 9px;
                }

                .la-td {
                    padding: 10px 10px;
                }

                /* Avatar & teks sedikit lebih kecil */
                .la-avatar {
                    width: 30px;
                    height: 30px;
                    border-radius: 8px;
                    font-size: 12px;
                }

                .la-user-cell {
                    gap: 7px;
                }

                .la-user-name {
                    font-size: 12px;
                }

                .la-stat-val {
                    font-size: 26px;
                }

                .la-stat {
                    padding: 14px;
                }
            }
        </style>

        <div class="la-stats">
            <div class="la-stat success">
                <svg class="la-stat-bg-icon" width="80" height="80" viewBox="0 0 24 24" fill="#10b981">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <div class="la-stat-top">
                    <div class="la-stat-icon-wrap">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </div>
                </div>
                <div class="la-stat-val">{{ $todaySuccess }}</div>
                <div class="la-stat-label">Login Berhasil Hari Ini</div>
            </div>

            <div class="la-stat failed">
                <svg class="la-stat-bg-icon" width="80" height="80" viewBox="0 0 24 24" fill="#ef4444">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
                <div class="la-stat-top">
                    <div class="la-stat-icon-wrap">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                    </div>
                </div>
                <div class="la-stat-val">{{ $todayFailed }}</div>
                <div class="la-stat-label">Login Gagal Hari Ini</div>
            </div>

            <div class="la-stat users">
                <svg class="la-stat-bg-icon" width="80" height="80" viewBox="0 0 24 24" fill="#7c3aed">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                </svg>
                <div class="la-stat-top">
                    <div class="la-stat-icon-wrap">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                </div>
                <div class="la-stat-val">{{ $uniqueUsers }}</div>
                <div class="la-stat-label">User Unik Hari Ini</div>
            </div>
        </div>

        <div class="la-filter-bar">
            <div class="la-filter-group">
                @foreach(['today' => 'Hari Ini', 'week' => '7 Hari', 'month' => 'Bulan Ini'] as $key => $label)
                    <button wire:click="setPeriod('{{ $key }}')"
                        class="la-fbtn {{ $filterPeriod === $key ? 'fp-active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="la-filter-divider"></div>
            <div class="la-filter-group">
                @foreach(['all' => 'Semua', 'success' => 'Berhasil', 'failed' => 'Gagal'] as $key => $label)
                    <button wire:click="setFilter('{{ $key }}')"
                        class="la-fbtn {{ $filterStatus === $key ? 'fs-active-' . $key : '' }}">{{ $label }}</button>
                @endforeach
            </div>
            <span class="la-count-badge">{{ $total }} log</span>
        </div>

        <div class="la-table-wrap">
            <table class="la-table">
                <thead>
                    <tr class="la-thead-row">
                        <th class="la-th">Pengguna</th>
                        <th class="la-th">Status</th>
                        <th class="la-th">Perangkat</th>
                        <th class="la-th">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr class="la-tr">
                            <td class="la-td">
                                <div class="la-user-cell">
                                    <div class="la-avatar">
                                        {{ mb_strtoupper(mb_substr($activity->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="la-user-name">{{ $activity->user?->name ?? 'Pengguna Dihapus' }}</div>
                                        {{-- <div class="la-user-email">{{ $activity->user?->email ?? '—' }}</div> --}}
                                    </div>
                                </div>
                            </td>
                            <td class="la-td">
                                <span class="la-status {{ $activity->status }}">
                                    <span class="la-status-dot"></span>
                                    {{ $activity->status === 'success' ? 'Berhasil' : 'Gagal' }}
                                </span>
                            </td>
                            <td class="la-td">
                                <div class="la-chips">
                                    @if($activity->browser)
                                        <span class="la-chip">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <circle cx="12" cy="12" r="10" />
                                                <line x1="2" y1="12" x2="22" y2="12" />
                                                <path
                                                    d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                            </svg>
                                            {{ $activity->browser }}
                                        </span>
                                    @endif
                                    @if($activity->platform)
                                        <span class="la-chip">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <rect x="2" y="3" width="20" height="14" rx="2" />
                                                <line x1="8" y1="21" x2="16" y2="21" />
                                                <line x1="12" y1="17" x2="12" y2="21" />
                                            </svg>
                                            {{ $activity->platform }}
                                        </span>
                                    @endif
                                    @if($activity->device && $activity->device !== 'Desktop')
                                        <span class="la-chip">{{ $activity->device }}</span>
                                    @endif
                                    @if(!$activity->browser && !$activity->platform)
                                        <span style="color:#9ca3af;font-size:13px;">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="la-td">
                                <div class="la-time-rel">{{ $activity->logged_in_at->locale('id')->diffForHumans() }}</div>
                                <div class="la-time-abs">{{ $activity->logged_in_at->format('d M Y, H:i') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="la-empty">
                                    <div class="la-empty-icon">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                            <polyline points="10 17 15 12 10 7" />
                                            <line x1="15" y1="12" x2="3" y2="12" />
                                        </svg>
                                    </div>
                                    <p class="la-empty-title">Tidak ada aktivitas</p>
                                    <p class="la-empty-sub">Belum ada login pada periode yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($hasMore)
                <div class="la-load-more">
                    <button wire:click="loadMore" class="la-load-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="loadMore">↓ &nbsp;Tampilkan Lebih Banyak</span>
                        <span wire:loading wire:target="loadMore">Memuat...</span>
                    </button>
                </div>
            @endif
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
