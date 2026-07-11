<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="display:flex; align-items:center; justify-content:center;
                            width:36px; height:36px; border-radius:10px;
                            background:#f5f3ff; border:1px solid #ddd6fe;">
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
            /* ── Summary stats ── */
            .la-stats { display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
            .la-stat {
                display:flex; align-items:center; gap:8px;
                padding:10px 14px; border-radius:10px;
                border:1px solid; flex:1; min-width:140px;
            }
            .la-stat.success { background:#f0fdf4; border-color:#bbf7d0; }
            .la-stat.failed  { background:#fef2f2; border-color:#fecaca; }
            .la-stat.users   { background:#f5f3ff; border-color:#ddd6fe; }
            .la-stat-icon {
                width:32px; height:32px; border-radius:8px;
                display:flex; align-items:center; justify-content:center; flex-shrink:0;
            }
            .la-stat.success .la-stat-icon { background:#dcfce7; }
            .la-stat.failed  .la-stat-icon { background:#fee2e2; }
            .la-stat.users   .la-stat-icon { background:#ede9fe; }
            .la-stat-val  { font-size:20px; font-weight:800; line-height:1; }
            .la-stat.success .la-stat-val { color:#166534; }
            .la-stat.failed  .la-stat-val { color:#991b1b; }
            .la-stat.users   .la-stat-val { color:#5b21b6; }
            .la-stat-label { font-size:10.5px; color:#a8a29e; margin-top:2px; }

            /* ── Filters ── */
            .la-filters { display:flex; gap:8px; margin-bottom:14px; flex-wrap:wrap; }
            .la-filter-group { display:flex; gap:4px; }
            .la-btn {
                padding:5px 12px; border-radius:7px; font-size:11.5px; font-weight:600;
                border:1px solid #e7e5e4; background:#faf9f7; color:#78716c;
                cursor:pointer; transition:all .12s;
            }
            .la-btn:hover { background:#f0eeed; }
            .la-btn.active.period { background:#f5f3ff; border-color:#ddd6fe; color:#5b21b6; }
            .la-btn.active.status-all     { background:#f5f4f2; border-color:#e7e5e4; color:#1c1917; }
            .la-btn.active.status-success { background:#f0fdf4; border-color:#bbf7d0; color:#166534; }
            .la-btn.active.status-failed  { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
            .la-divider-v { width:1px; background:#e7e5e4; margin:0 4px; }

            /* ── Table ── */
            .la-table-wrap {
                border:1px solid #e7e5e4; border-radius:12px; overflow:hidden;
            }
            .la-table { width:100%; border-collapse:collapse; }
            .la-th {
                padding:9px 14px; font-size:10px; font-weight:700;
                text-transform:uppercase; letter-spacing:.07em;
                color:#a8a29e; background:#faf9f7;
                border-bottom:1px solid #e7e5e4; text-align:left;
            }
            .la-tr { border-bottom:1px solid #f5f4f2; transition:background .1s; }
            .la-tr:last-child { border-bottom:none; }
            .la-tr:hover { background:#faf9f7; }
            .la-td { padding:10px 14px; font-size:12.5px; vertical-align:middle; }

            /* Avatar */
            .la-avatar {
                width:30px; height:30px; border-radius:8px;
                display:inline-flex; align-items:center; justify-content:center;
                font-size:12px; font-weight:700; flex-shrink:0;
                background:linear-gradient(135deg,#f5f3ff,#ede9fe);
                color:#5b21b6; border:1px solid #ddd6fe;
            }

            /* User cell */
            .la-user-cell { display:flex; align-items:center; gap:8px; }
            .la-user-name  { font-weight:600; color:#1c1917; font-size:12.5px; }
            .la-user-email { font-size:10.5px; color:#a8a29e; margin-top:1px; }

            /* Status badge */
            .la-status {
                display:inline-flex; align-items:center; gap:4px;
                padding:2px 8px; border-radius:100px;
                font-size:10.5px; font-weight:700; border:1px solid;
            }
            .la-status.success { background:#f0fdf4; color:#166534; border-color:#bbf7d0; }
            .la-status.failed  { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
            .la-status-dot { width:5px; height:5px; border-radius:50%; }
            .la-status.success .la-status-dot { background:#16a34a; }
            .la-status.failed  .la-status-dot { background:#dc2626; }

            /* Device badges */
            .la-chip {
                display:inline-flex; align-items:center; gap:3px;
                padding:2px 7px; border-radius:5px; font-size:10.5px; font-weight:500;
                background:#f5f4f2; color:#78716c; border:1px solid #e7e5e4;
                margin-right:3px;
            }

            /* IP */
            .la-ip { font-family:ui-monospace,monospace; font-size:11px; color:#a8a29e; }

            /* Time */
            .la-time-rel { font-size:11.5px; font-weight:600; color:#1c1917; }
            .la-time-abs { font-size:10px; color:#c4bfbb; margin-top:1px; }

            /* Load more */
            .la-load-more {
                display:flex; justify-content:center; padding:14px;
                border-top:1px solid #f5f4f2;
            }
            .la-load-btn {
                padding:7px 20px; border-radius:8px; font-size:12px; font-weight:600;
                background:#faf9f7; border:1px solid #e7e5e4; color:#78716c;
                cursor:pointer; transition:all .12s;
            }
            .la-load-btn:hover { background:#f0eeed; }

            /* Empty */
            .la-empty {
                display:flex; flex-direction:column; align-items:center;
                padding:40px 20px; text-align:center;
            }
            .la-empty-icon {
                width:44px; height:44px; border-radius:50%;
                background:#f5f3ff; border:1px solid #ddd6fe;
                display:flex; align-items:center; justify-content:center;
                margin-bottom:10px;
            }
            .la-empty p { margin:0; }
            .la-empty-title { font-size:13px; font-weight:600; color:#a8a29e; }
            .la-empty-sub   { font-size:11px; color:#d6d3d1; margin-top:3px !important; }

            /* Dark */
            @media (prefers-color-scheme: dark) {
                .la-table-wrap { border-color:#292524; }
                .la-th         { background:#1c1917; border-color:#292524; color:#78716c; }
                .la-tr         { border-color:#292524; }
                .la-tr:hover   { background:#1a1917; }
                .la-user-name  { color:#fafaf9; }
                .la-time-rel   { color:#fafaf9; }
                .la-chip       { background:#292524; color:#a8a29e; border-color:#44403c; }
                .la-btn        { background:#1c1917; border-color:#292524; color:#a8a29e; }
                .la-btn:hover  { background:#292524; }
                .la-stat.success { background:#052e16; border-color:#14532d; }
                .la-stat.failed  { background:#1a0505; border-color:#3f1d1d; }
                .la-stat.users   { background:#1e0a38; border-color:#4c1d95; }
                .la-load-btn   { background:#1c1917; border-color:#292524; color:#a8a29e; }
                .la-empty      { }
                .la-empty-icon { background:#1e0a38; border-color:#4c1d95; }
            }
        </style>

        {{-- ── Summary Stats ── --}}
        <div class="la-stats">
            <div class="la-stat success">
                <div class="la-stat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a"
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
                <div class="la-stat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626"
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
                <div class="la-stat-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7c3aed"
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
            {{-- Period --}}
            <div class="la-filter-group">
                @foreach(['today' => 'Hari Ini', 'week' => '7 Hari', 'month' => 'Bulan Ini'] as $key => $label)
                    <button wire:click="setPeriod('{{ $key }}')"
                            class="la-btn period {{ $filterPeriod === $key ? 'active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="la-divider-v"></div>

            {{-- Status --}}
            <div class="la-filter-group">
                @foreach(['all' => 'Semua', 'success' => 'Berhasil', 'failed' => 'Gagal'] as $key => $label)
                    <button wire:click="setFilter('{{ $key }}')"
                            class="la-btn {{ $filterStatus === $key ? 'active status-'.$key : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <span style="font-size:11px; color:#a8a29e; margin-left:auto; align-self:center;">
                {{ $total }} aktivitas
            </span>
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

                            {{-- User --}}
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

                            {{-- Status --}}
                            <td class="la-td">
                                <span class="la-status {{ $activity->status }}">
                                    <span class="la-status-dot"></span>
                                    {{ $activity->status === 'success' ? 'Berhasil' : 'Gagal' }}
                                </span>
                            </td>

                            {{-- Device --}}
                            <td class="la-td">
                                @if($activity->browser)
                                    <span class="la-chip">
                                        @if($activity->browser === 'Chrome')
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="4"/><path d="M12 2a10 10 0 0 1 8.66 5H12a5 5 0 0 0-4.33 2.5L5.34 5.5A10 10 0 0 1 12 2z" opacity=".3"/></svg>
                                        @endif
                                        {{ $activity->browser }}
                                    </span>
                                @endif
                                @if($activity->platform)
                                    <span class="la-chip">{{ $activity->platform }}</span>
                                @endif
                                @if($activity->device && $activity->device !== 'Desktop')
                                    <span class="la-chip">{{ $activity->device }}</span>
                                @endif
                            </td>

                            {{-- IP --}}
                            <td class="la-td">
                                <span class="la-ip">{{ $activity->ip_address ?? '—' }}</span>
                            </td>

                            {{-- Waktu --}}
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

            {{-- Load more --}}
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
