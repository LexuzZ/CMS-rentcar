<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="display:flex;align-items:center;justify-content:center;
                                width:38px;height:38px;border-radius:11px;
                                background:linear-gradient(135deg,#7c3aed,#6d28d9);
                                box-shadow:0 4px 12px rgba(124,58,237,.3);">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                             stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                    </div>
                    <div>
                        <p style="margin:0;font-size:14px;font-weight:700;color:inherit;">Login Activity</p>
                        <p style="margin:2px 0 0;font-size:11px;color:#a8a29e;font-weight:400;">
                            Riwayat aktivitas login pengguna
                        </p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:6px;
                            padding:4px 10px;border-radius:8px;
                            background:rgba(124,58,237,.08);border:1px solid rgba(124,58,237,.2);">
                    <span style="width:6px;height:6px;border-radius:50%;background:#7c3aed;
                                 display:inline-block;animation:la-pulse 2s infinite;"></span>
                    <span style="font-size:11px;font-weight:600;color:#7c3aed;">Live</span>
                </div>
            </div>
        </x-slot>

        <style>
            @keyframes la-pulse { 0%,100%{opacity:1} 50%{opacity:.3} }

            /* ── Stats ── */
            .la-stats {
                display: grid;
                grid-template-columns: repeat(3,1fr);
                gap: 12px;
                margin-bottom: 18px;
            }
            @media(max-width:640px){ .la-stats{grid-template-columns:1fr} }

            .la-stat {
                border-radius: 14px;
                padding: 16px;
                border: 1px solid;
                position: relative;
                overflow: hidden;
                transition: transform .15s, box-shadow .15s;
            }
            .la-stat:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(31,24,54,.09); }

            .la-stat.success { background:linear-gradient(165deg,#ffffff 0%,#f2fdf7 100%); border-color:#bbf7d0; box-shadow:0 1px 3px rgba(16,185,129,.07); }
            .la-stat.failed  { background:linear-gradient(165deg,#ffffff 0%,#fef4f4 100%); border-color:#fecaca; box-shadow:0 1px 3px rgba(239,68,68,.07); }
            .la-stat.users   { background:linear-gradient(165deg,#ffffff 0%,#f6f4ff 100%); border-color:#ddd6fe; box-shadow:0 1px 3px rgba(124,58,237,.07); }

            /* gradient accent top */
            .la-stat::before {
                content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:14px 14px 0 0;
            }
            .la-stat.success::before { background:linear-gradient(90deg,#10b981,#34d399); }
            .la-stat.failed::before  { background:linear-gradient(90deg,#ef4444,#f87171); }
            .la-stat.users::before   { background:linear-gradient(90deg,#7c3aed,#a78bfa); }

            /* bg icon decoration */
            .la-stat-bg-icon {
                position:absolute; right:-8px; bottom:-8px;
                opacity:.06; pointer-events:none;
            }

            .la-stat-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:12px; }
            .la-stat-icon-wrap {
                width:40px; height:40px; border-radius:11px;
                display:flex; align-items:center; justify-content:center;
                flex-shrink:0;
                box-shadow:0 1px 2px rgba(0,0,0,.04);
            }
            .la-stat.success .la-stat-icon-wrap { background:#dcfce8; }
            .la-stat.failed  .la-stat-icon-wrap { background:#fde3e3; }
            .la-stat.users   .la-stat-icon-wrap { background:#ece7fe; }

            .la-stat-trend {
                display:inline-flex; align-items:center; gap:3px;
                padding:2px 7px; border-radius:100px;
                font-size:10px; font-weight:700;
                background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;
            }

            .la-stat-val   { font-size:28px; font-weight:900; line-height:1; letter-spacing:-.03em; }
            .la-stat.success .la-stat-val { color:#047857; }
            .la-stat.failed  .la-stat-val { color:#b91c1c; }
            .la-stat.users   .la-stat-val { color:#6d28d9; }
            .la-stat-label { font-size:11px; color:#8a8695; font-weight:600; margin-top:4px; }

            /* ── Filter bar ── */
            .la-filter-bar {
                display:flex; align-items:center; gap:6px; margin-bottom:14px;
                padding:6px 8px; background:#f7f6fb;
                border:1px solid #e7e3f2; border-radius:12px; flex-wrap:wrap;
            }
            .la-filter-group { display:flex; gap:2px; background:#fff; border:1px solid #e6e1f0; border-radius:8px; padding:3px; box-shadow:0 1px 2px rgba(31,24,54,.04); }
            .la-fbtn {
                padding:4px 12px; border-radius:6px; font-size:11.5px; font-weight:600;
                border:none; background:transparent; color:#93909e; cursor:pointer; transition:all .12s;
            }
            .la-fbtn:hover { color:#3f3b4a; }
            .la-fbtn.fp-active { background:linear-gradient(135deg,#7c3aed,#6d28d9); color:#fff; box-shadow:0 2px 8px rgba(124,58,237,.25); }
            .la-fbtn.fs-active-all     { background:#111827; color:#fff; }
            .la-fbtn.fs-active-success { background:#059669; color:#fff; }
            .la-fbtn.fs-active-failed  { background:#dc2626; color:#fff; }

            .la-filter-divider { width:1px; height:22px; background:#e6e1f0; margin:0 2px; flex-shrink:0; }
            .la-count-badge {
                margin-left:auto; font-size:11px; font-weight:700;
                padding:4px 10px; border-radius:7px;
                background:#fff; border:1px solid #e6e1f0; color:#5b5768;
                box-shadow:0 1px 2px rgba(31,24,54,.04);
            }

            /* ── Table ── */
            .la-table-wrap { border:1px solid #ece8f5; border-radius:14px; overflow:hidden; box-shadow:0 1px 4px rgba(31,24,54,.04); }
            .la-table      { width:100%; border-collapse:collapse; }

            .la-thead-row { background:linear-gradient(180deg,#faf9fd,#f3f1fa); }
            .la-th {
                padding:10px 16px; font-size:9.5px; font-weight:800;
                text-transform:uppercase; letter-spacing:.1em;
                color:#928da0; border-bottom:1px solid #ece8f5; text-align:left;
            }

            .la-tr { border-bottom:1px solid #f2effa; transition:background .1s; }
            .la-tr:last-child { border-bottom:none; }
            .la-tr:hover { background:#faf9ff; }
            .la-tr:hover .la-td-action { opacity:1; }

            .la-td { padding:12px 16px; vertical-align:middle; }

            /* Avatar */
            .la-avatar {
                width:34px; height:34px; border-radius:10px; flex-shrink:0;
                display:inline-flex; align-items:center; justify-content:center;
                font-size:13px; font-weight:800;
                background:linear-gradient(135deg,#7c3aed,#6d28d9);
                color:#fff;
                box-shadow:0 2px 8px rgba(124,58,237,.25);
            }
            .la-user-cell  { display:flex; align-items:center; gap:10px; }
            .la-user-name  { font-size:13px; font-weight:700; color:#1c1a22; }
            .la-user-role  {
                display:inline-flex; align-items:center;
                font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
                padding:1px 6px; border-radius:4px; margin-left:4px; vertical-align:middle;
                background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb;
            }
            .la-user-email { font-size:11px; color:#a19cae; margin-top:1px; }

            /* Status */
            .la-status {
                display:inline-flex; align-items:center; gap:5px;
                padding:4px 10px; border-radius:100px;
                font-size:11px; font-weight:700; border:1px solid; white-space:nowrap;
            }
            .la-status.success { background:#ecfdf5; color:#065f46; border-color:#a7f3d0; }
            .la-status.failed  { background:#fef2f2; color:#991b1b; border-color:#fca5a5; }
            .la-status-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
            .la-status.success .la-status-dot { background:#10b981; box-shadow:0 0 0 2px rgba(16,185,129,.2); }
            .la-status.failed  .la-status-dot { background:#ef4444; box-shadow:0 0 0 2px rgba(239,68,68,.2); }

            /* Chips */
            .la-chips { display:flex; flex-wrap:wrap; gap:4px; }
            .la-chip {
                display:inline-flex; align-items:center; gap:4px;
                padding:3px 8px; border-radius:6px;
                font-size:10.5px; font-weight:600;
                background:#f3f1fa; color:#5b5768; border:1px solid #e6e1f0;
            }
            .la-chip svg { opacity:.65; }

            /* IP */
            .la-ip {
                font-family:ui-monospace,monospace; font-size:11px; font-weight:600;
                color:#374151; background:#f3f1fa;
                padding:4px 10px; border-radius:7px; border:1px solid #e6e1f0;
                display:inline-block; letter-spacing:.03em;
            }

            /* Time */
            .la-time-rel { font-size:12px; font-weight:700; color:#1c1a22; }
            .la-time-abs { font-size:10.5px; color:#a29dad; margin-top:2px; }

            /* Load more */
            .la-load-more {
                display:flex; justify-content:center; padding:14px;
                background:linear-gradient(180deg,#faf9fd,#f3f1fa);
                border-top:1px solid #ece8f5;
            }
            .la-load-btn {
                padding:8px 24px; border-radius:9px; font-size:12px; font-weight:700;
                background:#fff; border:1px solid #e6e1f0; color:#3f3b4a;
                cursor:pointer; transition:all .12s;
                box-shadow:0 1px 3px rgba(31,24,54,.06);
            }
            .la-load-btn:hover { background:#faf9ff; border-color:#c4b5fd; color:#5b21b6; box-shadow:0 3px 10px rgba(124,58,237,.15); }

            /* Empty */
            .la-empty { display:flex;flex-direction:column;align-items:center;padding:52px 20px;text-align:center; }
            .la-empty-icon {
                width:56px; height:56px; border-radius:16px;
                background:linear-gradient(135deg,#f5f3ff,#ede9fe);
                border:1px solid #c4b5fd;
                display:flex; align-items:center; justify-content:center; margin-bottom:14px;
                box-shadow:0 4px 16px rgba(124,58,237,.12);
            }
            .la-empty-title { font-size:14px; font-weight:700; color:#57536e; margin:0; }
            .la-empty-sub   { font-size:12px; color:#b0abbc; margin:5px 0 0; }

            /* Dark */
            @media (prefers-color-scheme: dark) {
                .la-stat.success,.la-stat.failed,.la-stat.users { background:#1c1917; }
                .la-stat.success { border-color:#14532d; }
                .la-stat.failed  { border-color:#3f1d1d; }
                .la-stat.users   { border-color:#2e1065; }
                .la-stat.success .la-stat-icon-wrap { background:#052e16; }
                .la-stat.failed  .la-stat-icon-wrap { background:#1a0505; }
                .la-stat.users   .la-stat-icon-wrap { background:#1e0a38; }
                .la-stat.success .la-stat-val { color:#4ade80; }
                .la-stat.failed  .la-stat-val { color:#f87171; }
                .la-stat.users   .la-stat-val { color:#c4b5fd; }
                .la-filter-bar   { background:#1c1917; border-color:#292524; }
                .la-filter-group { background:#111110; border-color:#292524; }
                .la-fbtn:hover   { color:#e5e7eb; }
                .la-count-badge  { background:#292524; border-color:#44403c; color:#9ca3af; }
                .la-filter-divider { background:#44403c; }
                .la-table-wrap   { border-color:#292524; }
                .la-thead-row    { background:#1c1917; }
                .la-th           { border-color:#292524; color:#6b7280; }
                .la-tr           { border-color:#1c1917; }
                .la-tr:hover     { background:#111110; }
                .la-user-name    { color:#f9fafb; }
                .la-time-rel     { color:#f9fafb; }
                .la-time-abs     { color:#57534e; }
                .la-ip           { background:#292524; color:#e5e7eb; border-color:#44403c; }
                .la-chip         { background:#292524; color:#a8a29e; border-color:#44403c; }
                .la-load-more    { background:#1c1917; border-color:#292524; }
                .la-load-btn     { background:#292524; border-color:#44403c; color:#e5e7eb; }
                .la-empty        { }
                .la-empty-icon   { background:#1e0a38; border-color:#4c1d95; }
            }
        </style>

        {{-- ── Stats ── --}}
        <div class="la-stats">

            {{-- Berhasil --}}
            <div class="la-stat success">
                <svg class="la-stat-bg-icon" width="80" height="80" viewBox="0 0 24 24" fill="#10b981">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <div class="la-stat-top">
                    <div class="la-stat-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                </div>
                <div class="la-stat-val">{{ $todaySuccess }}</div>
                <div class="la-stat-label">Login Berhasil Hari Ini</div>
            </div>

            {{-- Gagal --}}
            <div class="la-stat failed">
                <svg class="la-stat-bg-icon" width="80" height="80" viewBox="0 0 24 24" fill="#ef4444">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                <div class="la-stat-top">
                    <div class="la-stat-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </div>
                </div>
                <div class="la-stat-val">{{ $todayFailed }}</div>
                <div class="la-stat-label">Login Gagal Hari Ini</div>
            </div>

            {{-- User Unik --}}
            <div class="la-stat users">
                <svg class="la-stat-bg-icon" width="80" height="80" viewBox="0 0 24 24" fill="#7c3aed">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
                <div class="la-stat-top">
                    <div class="la-stat-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                </div>
                <div class="la-stat-val">{{ $uniqueUsers }}</div>
                <div class="la-stat-label">User Unik Hari Ini</div>
            </div>

        </div>

        {{-- ── Filter bar ── --}}
        <div class="la-filter-bar">
            {{-- Periode --}}
            <div class="la-filter-group">
                @foreach(['today' => 'Hari Ini', 'week' => '7 Hari', 'month' => 'Bulan Ini'] as $key => $label)
                    <button wire:click="setPeriod('{{ $key }}')"
                            class="la-fbtn {{ $filterPeriod === $key ? 'fp-active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="la-filter-divider"></div>

            {{-- Status --}}
            <div class="la-filter-group">
                @foreach(['all' => 'Semua', 'success' => 'Berhasil', 'failed' => 'Gagal'] as $key => $label)
                    <button wire:click="setFilter('{{ $key }}')"
                            class="la-fbtn {{ $filterStatus === $key ? 'fs-active-'.$key : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <span class="la-count-badge">{{ $total }} log</span>
        </div>

        {{-- ── Table ── --}}
        <div class="la-table-wrap">
            <table class="la-table">
                <thead>
                    <tr class="la-thead-row">
                        <th class="la-th">Pengguna</th>
                        <th class="la-th">Status</th>
                        <th class="la-th">Perangkat</th>
                        {{-- <th class="la-th">IP Address</th> --}}
                        <th class="la-th">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        <tr class="la-tr">

                            {{-- Pengguna --}}
                            <td class="la-td">
                                <div class="la-user-cell">
                                    <div class="la-avatar">
                                        {{ mb_strtoupper(mb_substr($activity->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="la-user-name">
                                            {{ $activity->user?->name ?? 'Pengguna Dihapus' }}
                                        </div>
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

                            {{-- Perangkat --}}
                            <td class="la-td">
                                <div class="la-chips">
                                    @if($activity->browser)
                                        <span class="la-chip">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                            {{ $activity->browser }}
                                        </span>
                                    @endif
                                    @if($activity->platform)
                                        <span class="la-chip">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                            {{ $activity->platform }}
                                        </span>
                                    @endif
                                    @if($activity->device && $activity->device !== 'Desktop')
                                        <span class="la-chip">{{ $activity->device }}</span>
                                    @endif
                                    @if(!$activity->browser && !$activity->platform)
                                        <span style="color:#d1d5db;font-size:13px;">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- IP --}}
                            {{-- <td class="la-td">
                                <span class="la-ip">{{ $activity->ip_address ?? '—' }}</span>
                            </td> --}}

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
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
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
                        <span wire:loading.remove wire:target="loadMore">
                            ↓ &nbsp;Tampilkan Lebih Banyak
                        </span>
                        <span wire:loading wire:target="loadMore">Memuat...</span>
                    </button>
                </div>
            @endif
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
