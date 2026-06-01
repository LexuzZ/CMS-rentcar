<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="display:flex; align-items:center; justify-content:center;
                            width:36px; height:36px; border-radius:10px;
                            background:#eff6ff; border:1px solid #bfdbfe;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div style="line-height:1.3;">
                    <p style="margin:0; font-size:14px; font-weight:600; color:inherit;">Aktivitas Pengguna</p>
                    <p style="margin:0; font-size:11px; color:#a8a29e; font-weight:400; margin-top:2px;">
                        Login terakhir &amp; perubahan data terkini
                    </p>
                </div>
                <div style="margin-left:auto; display:flex; align-items:center; gap:6px;
                            padding:4px 10px; border-radius:8px;
                            background:#eff6ff; border:1px solid #bfdbfe;">
                    <span style="width:6px;height:6px;border-radius:50%;background:#3b82f6;
                                 display:inline-block; animation:ua-pulse 2s infinite;"></span>
                    <span style="font-size:12px;font-weight:600;color:#2563eb;">Live</span>
                </div>
            </div>
        </x-slot>

        <style>
            @keyframes ua-pulse {
                0%,100% { opacity:1; }
                50%      { opacity:.3; }
            }
            .ua-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }
            @media (max-width: 768px) { .ua-grid { grid-template-columns: 1fr; } }

            /* ── Column header ── */
            .ua-col-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 10px;
                padding: 0 2px;
            }
            .ua-col-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 24px; height: 24px;
                border-radius: 6px;
                flex-shrink: 0;
            }
            .ua-col-icon.blue   { background:#eff6ff; border:1px solid #bfdbfe; }
            .ua-col-icon.violet { background:#f5f3ff; border:1px solid #ddd6fe; }
            .ua-col-title {
                margin: 0;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .07em;
                color: #a8a29e;
            }
            .ua-badge {
                margin-left: auto;
                padding: 2px 8px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 600;
                border: 1px solid;
            }
            .ua-badge.blue   { background:#eff6ff; border-color:#bfdbfe; color:#1d4ed8; }
            .ua-badge.violet { background:#f5f3ff; border-color:#ddd6fe; color:#6d28d9; }

            /* ── Cards list ── */
            .ua-list { display: flex; flex-direction: column; gap: 6px; }

            .ua-card {
                position: relative;
                background: #faf9f7;
                border-radius: 10px;
                overflow: hidden;
                transition: box-shadow .15s;
            }
            .ua-card.blue:hover   { box-shadow: 0 2px 10px rgba(59,130,246,.1); }
            .ua-card.violet:hover { box-shadow: 0 2px 10px rgba(109,40,217,.1); }
            .ua-card.blue   { border: 1px solid #e0effe; }
            .ua-card.violet { border: 1px solid #ede9fe; }

            .ua-card-bar {
                position: absolute;
                left: 0; top: 0; bottom: 0;
                width: 3px;
                border-radius: 10px 0 0 10px;
            }
            .ua-card-bar.blue   { background: #3b82f6; }
            .ua-card-bar.violet { background: #8b5cf6; }

            .ua-card-body {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px 10px 16px;
            }

            /* Avatar */
            .ua-avatar {
                width: 34px; height: 34px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                font-weight: 700;
                flex-shrink: 0;
                border: 1.5px solid;
            }
            .ua-avatar.blue {
                background: linear-gradient(135deg,#eff6ff,#dbeafe);
                color: #1d4ed8;
                border-color: #bfdbfe;
            }
            .ua-avatar.violet {
                background: linear-gradient(135deg,#f5f3ff,#ede9fe);
                color: #6d28d9;
                border-color: #ddd6fe;
            }

            /* Text */
            .ua-info { flex: 1; min-width: 0; }
            .ua-name {
                margin: 0;
                font-size: 12.5px;
                font-weight: 600;
                color: #1c1917;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .ua-sub {
                margin: 2px 0 0;
                font-size: 11px;
                color: #a8a29e;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Time */
            .ua-time {
                text-align: right;
                flex-shrink: 0;
            }
            .ua-time-rel {
                margin: 0;
                font-size: 11px;
                font-weight: 600;
                color: #78716c;
            }
            .ua-time-abs {
                margin: 2px 0 0;
                font-size: 10px;
                color: #c4bfbb;
                font-variant-numeric: tabular-nums;
            }

            /* Action badge */
            .ua-action-pill {
                display: inline-flex;
                align-items: center;
                padding: 2px 7px;
                border-radius: 100px;
                font-size: 10px;
                font-weight: 600;
                border: 1px solid;
                text-transform: capitalize;
            }
            .ua-action-pill.created { background:#f0fdf4; color:#166534; border-color:#bbf7d0; }
            .ua-action-pill.updated { background:#fffbeb; color:#92400e; border-color:#fde68a; }
            .ua-action-pill.deleted { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
            .ua-action-pill.default { background:#f5f3ff; color:#6d28d9; border-color:#ddd6fe; }

            /* Empty */
            .ua-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 28px 16px;
                border: 1.5px dashed #e7e5e4;
                border-radius: 10px;
                background: #faf9f7;
                text-align: center;
            }
            .ua-empty p { margin: 0; }
            .ua-empty-title { font-size: 12.5px; font-weight: 500; color: #a8a29e; }
            .ua-empty-sub   { font-size: 11px; color: #d6d3d1; margin-top: 3px !important; }

            /* Dark mode */
            @media (prefers-color-scheme: dark) {
                .ua-card         { background: #1c1917; }
                .ua-card.blue    { border-color: #1e3a5f; }
                .ua-card.violet  { border-color: #2e1065; }
                .ua-name         { color: #fafaf9; }
                .ua-sub          { color: #78716c; }
                .ua-time-rel     { color: #a8a29e; }
                .ua-time-abs     { color: #57534e; }
                .ua-col-icon.blue   { background:#1e3a5f; border-color:#1d4ed833; }
                .ua-col-icon.violet { background:#2e1065; border-color:#6d28d933; }
                .ua-badge.blue   { background:#1e3a5f; border-color:#1d4ed833; color:#93c5fd; }
                .ua-badge.violet { background:#2e1065; border-color:#6d28d933; color:#c4b5fd; }
                .ua-avatar.blue  { background:linear-gradient(135deg,#1e3a5f,#1d3461); border-color:#1d4ed844; }
                .ua-avatar.violet{ background:linear-gradient(135deg,#2e1065,#3b0764); border-color:#6d28d944; }
                .ua-empty        { background:#1c1917; border-color:#292524; }
                .ua-action-pill.created { background:#052e16; border-color:#14532d; }
                .ua-action-pill.updated { background:#1c1507; border-color:#44321a; }
                .ua-action-pill.deleted { background:#1a0505; border-color:#3f1d1d; }
                .ua-action-pill.default { background:#2e1065; border-color:#6d28d933; }
            }
        </style>

        <div class="ua-grid">

            {{-- ══ Kolom Login Terakhir ══ --}}
            <div>
                <div class="ua-col-header">
                    <div class="ua-col-icon blue">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                             stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                    </div>
                    <p class="ua-col-title">Login Terakhir</p>
                    <span class="ua-badge blue">{{ $recentLogins->count() }} user</span>
                </div>

                <div class="ua-list">
                    @forelse ($recentLogins as $user)
                        <div class="ua-card blue">
                            <div class="ua-card-bar blue"></div>
                            <div class="ua-card-body">
                                <div class="ua-avatar blue">
                                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ua-info">
                                    <p class="ua-name">{{ $user->name }}</p>
                                    <p class="ua-sub">
                                        {{ $user->email }}
                                        @if($user->last_login_ip)
                                            &nbsp;·&nbsp; {{ $user->last_login_ip }}
                                        @endif
                                    </p>
                                </div>
                                <div class="ua-time">
                                    <p class="ua-time-rel">
                                        {{ \Carbon\Carbon::parse($user->last_login_at)->locale('id')->diffForHumans() }}
                                    </p>
                                    <p class="ua-time-abs">
                                        {{ \Carbon\Carbon::parse($user->last_login_at)->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="ua-empty">
                            <p class="ua-empty-title">Belum ada data login</p>
                            <p class="ua-empty-sub">Kolom last_login_at belum tersedia</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ══ Kolom Perubahan Terkini ══ --}}
            <div>
                <div class="ua-col-header">
                    <div class="ua-col-icon violet">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                             stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </div>
                    <p class="ua-col-title">Perubahan Terkini</p>
                    <span class="ua-badge violet">{{ $recentActivities->count() }} log</span>
                </div>

                <div class="ua-list">
                    @forelse ($recentActivities as $activity)
                        @php
                            $causer      = $activity->causer;
                            $causerName  = $causer?->name ?? 'Sistem';
                            $initial     = mb_strtoupper(mb_substr($causerName, 0, 1));
                            $event       = $activity->event ?? $activity->description ?? 'updated';
                            $pillClass   = in_array($event, ['created','updated','deleted']) ? $event : 'default';
                            $subject     = $activity->subject_type
                                ? class_basename($activity->subject_type)
                                : '—';
                        @endphp
                        <div class="ua-card violet">
                            <div class="ua-card-bar violet"></div>
                            <div class="ua-card-body">
                                <div class="ua-avatar violet">{{ $initial }}</div>
                                <div class="ua-info">
                                    <p class="ua-name">{{ $causerName }}</p>
                                    <p class="ua-sub" style="display:flex;align-items:center;gap:5px;">
                                        <span class="ua-action-pill {{ $pillClass }}">{{ $event }}</span>
                                        <span>{{ $subject }}</span>
                                        @if($activity->subject_id)
                                            <span style="color:#d6d3d1;">#{{ $activity->subject_id }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="ua-time">
                                    <p class="ua-time-rel">
                                        {{ $activity->created_at->locale('id')->diffForHumans() }}
                                    </p>
                                    <p class="ua-time-abs">
                                        {{ $activity->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="ua-empty">
                            <p class="ua-empty-title">Belum ada log aktivitas</p>
                            <p class="ua-empty-sub">Install spatie/laravel-activitylog</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
