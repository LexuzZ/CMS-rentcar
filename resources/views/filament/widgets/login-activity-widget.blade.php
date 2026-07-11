<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="display:flex; align-items:center; justify-content:center;
                            width:36px; height:36px; border-radius:10px;
                            background:#f5f3ff; border:1px solid #c4b5fd;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </div>
                <div style="line-height:1.3;">
                    <p style="margin:0; font-size:14px; font-weight:600; color:inherit;">Login Activity</p>
                    <p style="margin:0; font-size:11px; color:#a8a29e; font-weight:400; margin-top:1px;">
                        Riwayat aktivitas login pengguna
                    </p>
                </div>
            </div>
        </x-slot>

        <style>
            /* ── Stats ── */
            .la-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:16px; }
            @media(max-width:600px){ .la-stats { grid-template-columns:1fr; } }

            .la-stat {
                display:flex; align-items:center; gap:12px;
                padding:14px 16px; border-radius:12px; border:1px solid;
                position:relative; overflow:hidden;
            }
            .la-stat-glow {
                position:absolute; right:-16px; top:-16px;
                width:64px; height:64px; border-radius:50%; opacity:.08; pointer-events:none;
            }
            .la-stat.success { background:#f6fef9; border-color:#d1fae5; }
            .la-stat.failed  { background:#fff8f8; border-color:#fee2e2; }
            .la-stat.users   { background:#faf8ff; border-color:#ede9fe; }
            .la-stat.success .la-stat-glow { background:#16a34a; }
            .la-stat.failed  .la-stat-glow { background:#dc2626; }
            .la-stat.users   .la-stat-glow { background:#7c3aed; }

            .la-stat-icon {
                width:38px; height:38px; border-radius:10px;
                display:flex; align-items:center; justify-content:center; flex-shrink:0;
                border:1px solid;
            }
            .la-stat.success .la-stat-icon { background:#ecfdf5; border-color:#a7f3d0; }
            .la-stat.failed  .la-stat-icon { background:#fef2f2; border-color:#fca5a5; }
            .la-stat.users   .la-stat-icon { background:#f5f3ff; border-color:#c4b5fd; }

            .la-stat-val   { font-size:22px; font-weight:800; line-height:1; margin-bottom:2px; }
            .la-stat.success .la-stat-val { color:#065f46; }
            .la-stat.failed  .la-stat-val { color:#991b1b; }
            .la-stat.users   .la-stat-val { color:#5b21b6; }
            .la-stat-label { font-size:11px; color:#9ca3af; font-weight:500; }

            /* ── Filters ── */
            .la-filters {
                display:flex; align-items:center; gap:6px;
                margin-bottom:14px; flex-wrap:wrap;
                padding:8px 10px; background:#faf9f7;
                border:1px solid #f0eeed; border-radius:10px;
            }
            .la-filter-group { display:flex; gap:3px; }
            .la-btn {
                padding:5px 11px; border-radius:6px; font-size:11.5px; font-weight:600;
                border:1px solid transparent; background:transparent; color:#9ca3af;
                cursor:pointer; transition:all .12s;
            }
            .la-btn:hover { background:#fff; color:#374151; border-color:#e5e7eb; }
            .la-btn.active-period  { background:#fff; border-color:#c4b5fd; color:#5b21b6; box-shadow:0 1px 3px rgba(124,58,237,.12); }
            .la-btn.active-all     { background:#fff; border-color:#e5e7eb; color:#111827; box-shadow:0 1px 3px rgba(0,0,0,.06); }
            .la-btn.active-success { background:#fff; border-color:#a7f3d0; color:#065f46; box-shadow:0 1px 3px rgba(22,163,74,.1); }
            .la-btn.active-failed  { background:#fff; border-color:#fca5a5; color:#991b1b; box-shadow:0 1px 3px rgba(220,38,38,.1); }
            .la-divider-v { width:1px; height:20px; background:#e5e7eb; margin:0 2px; flex-shrink:0; }
            .la-total-badge {
                margin-left:auto; font-size:11px; font-weight:600;
                padding:3px 9px; border-radius:6px;
                background:#fff; border:1px solid #e5e7eb; color:#9ca3af;
            }

            /* ── Table wrap ── */
            .la-table-wrap {
                border:1px solid #e9e7e4; border-radius:12px; overflow:hidden;
                background:#fff;
            }
            .la-table { width:100%; border-collapse:collapse; }

            .la-th {
                padding:9px 16px; font-size:10px; font-weight:700;
                text-transform:uppercase; letter-spacing:.08em;
                color:#b0aaa4; background:#faf9f7;
                border-bottom:1px solid #f0eeed; text-align:left;
            }

            .la-tr { border-bottom:1px solid #f7f5f3; transition:background .1s; }
            .la-tr:last-child { border-bottom:none; }
            .la-tr:hover { background:#fdfcfb; }

            .la-td { padding:11px 16px; vertical-align:middle; }

            /* Avatar */
            .la-avatar {
                width:32px; height:32px; border-radius:9px; flex-shrink:0;
                display:inline-flex; align-items:center; justify-content:center;
                font-size:12px; font-weight:800;
                background:linear-gradient(135deg,#f5f3ff,#ede9fe);
                color:#6d28d9; border:1px solid #c4b5fd;
            }
            .la-user-cell  { display:flex; align-items:center; gap:10px; }
            .la-user-name  { font-size:13px; font-weight:600; color:#111827; }
            .la-user-email { font-size:11px; color:#9ca3af; margin-top:1px; }

            /* Status */
            .la-status {
                display:inline-flex; align-items:center; gap:5px;
                padding:3px 9px; border-radius:100px;
                font-size:11px; font-weight:700; border:1px solid; white-space:nowrap;
            }
            .la-status.success { background:#ecfdf5; color:#065f46; border-color:#a7f3d0; }
            .la-status.failed  { background:#fef2f2; color:#991b1b; border-color:#fca5a5; }
            .la-status-dot { width:5px; height:5px; border-radius:50%; flex-shrink:0; }
            .la-status.success .la-status-dot { background:#10b981; }
            .la-status.failed  .la-status-dot { background:#ef4444; }

            /* Chips */
            .la-chips { display:flex; flex-wrap:wrap; gap:4px; }
            .la-chip {
                display:inline-flex; align-items:center; gap:3px;
                padding:2px 8px; border-radius:5px;
                font-size:10.5px; font-weight:500;
                background:#f7f6f4; color:#6b7280; border:1px solid #ede9e4;
            }

            /* IP */
            .la-ip {
                font-family:ui-monospace,monospace; font-size:11.5px;
                color:#6b7280; background:#f7f6f4;
                padding:2px 7px; border-radius:5px; border:1px solid #ede9e4;
                display:inline-block;
            }

            /* Time */
            .la-time-rel { font-size:12px; font-weight:600; color:#374151; }
            .la-time-abs { font-size:10.5px; color:#b0aaa4; margin-top:2px; }

            /* Load more */
            .la-load-more { display:flex; justify-content:center; padding:14px; background:#faf9f7; border-top:1px solid #f0eeed; }
            .la-load-btn {
                padding:7px 22px; border-radius:8px; font-size:12px; font-weight:600;
                background:#fff; border:1px solid #e5e7eb; color:#6b7280;
                cursor:pointer; transition:all .12s;
            }
            .la-load-btn:hover { background:#f7f6f4; border-color:#d1d5db; color:#374151; }

            /* Empty */
            .la-empty { display:flex; flex-direction:column; align-items:center; padding:44px 20px; text-align:center; }
            .la-empty-icon {
                width:48px; height:48px; border-radius:50%;
                background:#f5f3ff; border:1px solid #c4b5fd;
                display:flex; align-items:center; justify-content:center; margin-bottom:12px;
            }
            .la-empty-title { font-size:13px; font-weight:600; color:#9ca3af; margin:0; }
            .la-empty-sub   { font-size:11px; color:#d1d5db; margin:4px 0 0; }

            /* Dark mode */
            @media (prefers-color-scheme: dark) {
                .la-stat.success { background:#052e16; border-color:#14532d; }
                .la-stat.failed  { background:#1a0505; border-color:#3f1d1d; }
                .la-stat.users   { background:#1e0a38; border-color:#4c1d95; }
                .la-stat.success .la-stat-icon { background:#052e16; border-color:#166534; }
                .la-stat.failed  .la-stat-icon { background:#1a0505; border-color:#991b1b; }
                .la-stat.users   .la-stat-icon { background:#1e0a38; border-color:#5b21b6; }
                .la-filters      { background:#1c1917; border-color:#292524; }
                .la-btn:hover    { background:#292524; color:#e5e7eb; border-color:#44403c; }
                .la-btn.active-period  { background:#2e1065; border-color:#5b21b6; color:#c4b5fd; }
                .la-btn.active-all     { background:#292524; border-color:#44403c; color:#f9fafb; }
                .la-btn.active-success { background:#052e16; border-color:#166534; color:#4ade80; }
                .la-btn.active-failed  { background:#1a0505; border-color:#991b1b; color:#f87171; }
                .la-total-badge  { background:#292524; border-color:#44403c; color:#78716c; }
                .la-divider-v    { background:#44403c; }
                .la-table-wrap   { border-color:#292524; background:#0c0a09; }
                .la-th           { background:#1c1917; border-color:#292524; color:#78716c; }
                .la-tr           { border-color:#1c1917; }
                .la-tr:hover     { background:#111110; }
                .la-user-name    { color:#f9fafb; }
                .la-user-email   { color:#6b7280; }
                .la-time-rel     { color:#e5e7eb; }
                .la-time-abs     { color:#57534e; }
                .la-ip           { background:#1c1917; color:#a8a29e; border-color:#292524; }
                .la-chip         { background:#1c1917; color:#a8a29e; border-color:#292524; }
                .la-load-more    { background:#1c1917; border-color:#292524; }
                .la-load-btn     { background:#292524; border-color:#44403c; color:#a8a29e; }
                .la-empty-icon   { background:#1e0a38; border-color:#4c1d95; }
            }
        </style>

        {{-- ── Stats ── --}}
        <div class="la-stats">
            <div class="la-stat success">
                <div class="la-stat-glow"></div>
                <div class="la-stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div>
                    <div class="la-stat-val">{{ $todaySuccess }}</div>
                    <div class="la-stat-label">Login Berhasil Hari Ini</div>
                </div>
            </div>
            <div class="la-stat failed">
                <div class="la-stat-glow"></div>
                <div class="la-stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </div>
                <div>
                    <div class="la-stat-val">{{ $todayFailed }}</div>
                    <div class="la-stat-label">Login Gagal Hari Ini</div>
                </div>
            </div>
            <div class="la-stat users">
                <div class="la-stat-glow"></div>
                <div class="la-stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7c3aed"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="la-stat-val">{{ $uniqueUsers }}</div>
                    <div class="la-stat-label">User Unik Hari Ini</div>
                </div>
            </div>
        </div>

        {{-- ── Filters ── --}}
        <div class="la-filters">
            <div class="la-filter-group">
                @foreach(['today' => 'Hari Ini', 'week' => '7 Hari', 'month' => 'Bulan Ini'] as $key => $label)
                    <button wire:click="setPeriod('{{ $key }}')"
                            class="la-btn {{ $filterPeriod === $key ? 'active-period' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="la-divider-v"></div>

            <div class="la-filter-group">
                @foreach(['all' => 'Semua', 'success' => 'Berhasil', 'failed' => 'Gagal'] as $key => $label)
                    <button wire:click="setFilter('{{ $key }}')"
                            class="la-btn {{ $filterStatus === $key ? 'active-'.$key : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <span class="la-total-badge">{{ $total }} aktivitas</span>
        </div>

        {{-- ── Table ── --}}
        <div class="la-table-wrap">
            <table class="la-table">
                <thead>
                    <tr>
                        <th class="la-th">Pengguna</th>
                        <th class="la-th">Status</th>
                        <th class="la-th">Perangkat</th>
                        <th class="la-th">IP Address</th>
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
                                        <div class="la-user-email">{{ $activity->user?->email ?? '—' }}</div>
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
                                        <span class="la-chip">{{ $activity->browser }}</span>
                                    @endif
                                    @if($activity->platform)
                                        <span class="la-chip">{{ $activity->platform }}</span>
                                    @endif
                                    @if($activity->device && $activity->device !== 'Desktop')
                                        <span class="la-chip">{{ $activity->device }}</span>
                                    @endif
                                    @if(!$activity->browser && !$activity->platform)
                                        <span style="color:#d1d5db;font-size:12px;">—</span>
                                    @endif
                                </div>
                            </td>

                            <td class="la-td">
                                <span class="la-ip">{{ $activity->ip_address ?? '—' }}</span>
                            </td>

                            <td class="la-td">
                                <div class="la-time-rel">
                                    {{ $activity->logged_in_at->locale('id')->diffForHumans() }}
                                </div>
                                <div class="la-time-abs">
                                    {{ $activity->logged_in_at->format('d M Y, H:i') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="la-empty">
                                    <div class="la-empty-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                             stroke="#7c3aed" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                            <polyline points="10 17 15 12 10 7"/>
                                            <line x1="15" y1="12" x2="3" y2="12"/>
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
                        <span wire:loading.remove wire:target="loadMore">Tampilkan Lebih Banyak</span>
                        <span wire:loading wire:target="loadMore">Memuat...</span>
                    </button>
                </div>
            @endif
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
