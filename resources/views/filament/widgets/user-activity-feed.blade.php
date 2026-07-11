<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="display:flex;align-items:center;justify-content:center;
                                width:38px;height:38px;border-radius:11px;
                                background:linear-gradient(135deg,#0ea5e9,#0284c7);
                                box-shadow:0 4px 12px rgba(14,165,233,.3);">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                             stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="margin:0;font-size:14px;font-weight:700;color:inherit;">Aktivitas Pengguna</p>
                        <p style="margin:2px 0 0;font-size:11px;color:#a8a29e;font-weight:400;">
                            Log perubahan data oleh pengguna
                        </p>
                    </div>
                </div>
                <span style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:8px;
                             background:rgba(14,165,233,.08);border:1px solid rgba(14,165,233,.2);color:#0284c7;">
                    {{ $total }} aktivitas
                </span>
            </div>
        </x-slot>

        <style>
            /* ── Stats ── */
            .af-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:16px; }
            @media(max-width:640px){ .af-stats{grid-template-columns:1fr} }

            .af-stat {
                padding:14px 16px; border-radius:13px; border:1px solid;
                position:relative; overflow:hidden;
                transition:transform .15s, box-shadow .15s;
                background:#fff;
            }
            .af-stat:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.07); }
            .af-stat::before {
                content:''; position:absolute; top:0; left:0; right:0;
                height:3px; border-radius:13px 13px 0 0;
            }
            .af-stat.created { border-color:#d1fae5; }
            .af-stat.updated { border-color:#dbeafe; }
            .af-stat.deleted { border-color:#fee2e2; }
            .af-stat.created::before { background:linear-gradient(90deg,#10b981,#34d399); }
            .af-stat.updated::before { background:linear-gradient(90deg,#3b82f6,#60a5fa); }
            .af-stat.deleted::before { background:linear-gradient(90deg,#ef4444,#f87171); }

            .af-stat-icon {
                width:36px; height:36px; border-radius:9px;
                display:flex; align-items:center; justify-content:center;
                margin-bottom:10px;
            }
            .af-stat.created .af-stat-icon { background:#ecfdf5; }
            .af-stat.updated .af-stat-icon { background:#eff6ff; }
            .af-stat.deleted .af-stat-icon { background:#fef2f2; }

            .af-stat-val   { font-size:26px; font-weight:900; letter-spacing:-.03em; line-height:1; }
            .af-stat.created .af-stat-val { color:#065f46; }
            .af-stat.updated .af-stat-val { color:#1e40af; }
            .af-stat.deleted .af-stat-val { color:#991b1b; }
            .af-stat-label { font-size:11px; color:#9ca3af; margin-top:4px; font-weight:500; }

            /* ── Filter bar ── */
            .af-filter-bar {
                display:flex; align-items:center; gap:6px; margin-bottom:16px;
                padding:6px 8px; background:#f9f8f7;
                border:1px solid #ede9e4; border-radius:12px; flex-wrap:wrap;
            }
            .af-filter-group { display:flex; gap:2px; background:#fff; border:1px solid #e9e4de; border-radius:8px; padding:3px; }
            .af-fbtn {
                padding:4px 12px; border-radius:6px; font-size:11.5px; font-weight:600;
                border:none; background:transparent; color:#9ca3af; cursor:pointer; transition:all .12s;
            }
            .af-fbtn:hover { color:#374151; }
            .af-fbtn.fp-active  { background:linear-gradient(135deg,#0ea5e9,#0284c7); color:#fff; box-shadow:0 2px 8px rgba(14,165,233,.3); }
            .af-fbtn.fe-all     { }
            .af-fbtn.fe-active-all     { background:#111827; color:#fff; }
            .af-fbtn.fe-active-created { background:#065f46; color:#fff; }
            .af-fbtn.fe-active-updated { background:#1e40af; color:#fff; }
            .af-fbtn.fe-active-deleted { background:#991b1b; color:#fff; }
            .af-filter-div { width:1px; height:22px; background:#e9e4de; margin:0 2px; flex-shrink:0; }

            /* ── Timeline ── */
            .af-timeline { position:relative; }
            .af-timeline::before {
                content:''; position:absolute; left:19px; top:0; bottom:0;
                width:2px; background:linear-gradient(180deg,#e9e4de,transparent);
                border-radius:2px;
            }

            .af-item {
                display:flex; gap:14px; padding:12px 0;
                border-bottom:1px solid #f5f2ef; position:relative;
            }
            .af-item:last-child { border-bottom:none; }
            .af-item:hover .af-item-card { background:#fdfaf8; }

            /* Avatar column */
            .af-avatar-col { flex-shrink:0; position:relative; z-index:1; }
            .af-avatar {
                width:38px; height:38px; border-radius:11px;
                display:flex; align-items:center; justify-content:center;
                font-size:14px; font-weight:800; color:#fff;
                box-shadow:0 2px 8px rgba(0,0,0,.15);
                background:linear-gradient(135deg,#0ea5e9,#0284c7);
            }

            /* Event dot */
            .af-event-dot {
                position:absolute; bottom:-2px; right:-2px;
                width:14px; height:14px; border-radius:50%;
                display:flex; align-items:center; justify-content:center;
                border:2px solid #fff;
            }
            .af-event-dot.created { background:#10b981; }
            .af-event-dot.updated { background:#3b82f6; }
            .af-event-dot.deleted { background:#ef4444; }
            .af-event-dot.other   { background:#8b5cf6; }

            /* Card */
            .af-item-card {
                flex:1; min-width:0; padding:10px 12px;
                background:#faf9f7; border:1px solid #ede9e4;
                border-radius:11px; transition:background .12s;
            }

            .af-item-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:5px; }

            .af-sentence { font-size:13px; color:#111827; line-height:1.5; }
            .af-sentence strong { font-weight:700; }
            .af-sentence .af-who  { color:#0284c7; font-weight:700; }
            .af-sentence .af-what { font-weight:600; }
            .af-sentence .af-model {
                display:inline-flex; align-items:center; gap:3px;
                padding:1px 7px; border-radius:5px; font-size:11px; font-weight:700;
                border:1px solid; vertical-align:middle; margin:0 2px;
            }
            .af-model.created { background:#ecfdf5; color:#065f46; border-color:#a7f3d0; }
            .af-model.updated { background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
            .af-model.deleted { background:#fef2f2; color:#991b1b; border-color:#fca5a5; }
            .af-model.other   { background:#f5f3ff; color:#5b21b6; border-color:#c4b5fd; }

            .af-id-chip {
                display:inline-flex; font-family:ui-monospace,monospace;
                padding:1px 6px; border-radius:4px; font-size:10.5px; font-weight:700;
                background:#f5f3f0; color:#6b7280; border:1px solid #e9e4de;
                vertical-align:middle; margin-left:2px;
            }

            .af-time {
                font-size:11px; font-weight:600; color:#9ca3af; white-space:nowrap; flex-shrink:0;
            }
            .af-time-abs { font-size:10px; color:#c4bfbb; display:block; margin-top:1px; text-align:right; }

            /* Changed fields */
            .af-fields { display:flex; flex-wrap:wrap; gap:4px; margin-top:7px; }
            .af-field-chip {
                display:inline-flex; align-items:center; gap:3px;
                padding:2px 8px; border-radius:5px; font-size:10.5px; font-weight:500;
                background:#fff; color:#374151; border:1px solid #e9e4de;
            }
            .af-field-chip svg { color:#9ca3af; }
            .af-field-more { color:#9ca3af; font-size:10.5px; padding:2px 6px; }

            /* Day separator */
            .af-day-sep {
                display:flex; align-items:center; gap:10px;
                padding:8px 0; margin:4px 0;
            }
            .af-day-label {
                font-size:10px; font-weight:800; text-transform:uppercase;
                letter-spacing:.1em; color:#9ca3af; white-space:nowrap; padding-left:52px;
            }
            .af-day-line { flex:1; height:1px; background:#f0eeed; }

            /* Load more */
            .af-load-more { display:flex; justify-content:center; padding:16px 0 4px; }
            .af-load-btn {
                padding:8px 24px; border-radius:9px; font-size:12px; font-weight:700;
                background:#fff; border:1px solid #e9e4de; color:#374151;
                cursor:pointer; transition:all .12s; box-shadow:0 1px 3px rgba(0,0,0,.06);
            }
            .af-load-btn:hover { border-color:#7dd3fc; color:#0284c7; box-shadow:0 2px 8px rgba(14,165,233,.12); }

            /* Empty */
            .af-empty { display:flex;flex-direction:column;align-items:center;padding:52px 20px;text-align:center; }
            .af-empty-icon {
                width:56px;height:56px;border-radius:16px;
                background:linear-gradient(135deg,#eff6ff,#dbeafe);
                border:1px solid #bfdbfe;
                display:flex;align-items:center;justify-content:center;margin-bottom:14px;
                box-shadow:0 4px 16px rgba(14,165,233,.1);
            }
            .af-empty-title { font-size:14px;font-weight:700;color:#6b7280;margin:0; }
            .af-empty-sub   { font-size:12px;color:#c4c0bb;margin:5px 0 0; }

            /* Dark */
            @media(prefers-color-scheme:dark){
                .af-stat { background:#1c1917; }
                .af-stat.created { border-color:#14532d; }
                .af-stat.updated { border-color:#1e3a5f; }
                .af-stat.deleted { border-color:#3f1d1d; }
                .af-stat.created .af-stat-icon { background:#052e16; }
                .af-stat.updated .af-stat-icon { background:#0c1a33; }
                .af-stat.deleted .af-stat-icon { background:#1a0505; }
                .af-stat.created .af-stat-val { color:#4ade80; }
                .af-stat.updated .af-stat-val { color:#60a5fa; }
                .af-stat.deleted .af-stat-val { color:#f87171; }
                .af-filter-bar  { background:#1c1917; border-color:#292524; }
                .af-filter-group{ background:#111110; border-color:#292524; }
                .af-filter-div  { background:#44403c; }
                .af-fbtn:hover  { color:#e5e7eb; }
                .af-timeline::before { background:linear-gradient(180deg,#292524,transparent); }
                .af-item        { border-color:#1c1917; }
                .af-item-card   { background:#1c1917; border-color:#292524; }
                .af-item:hover .af-item-card { background:#111110; }
                .af-sentence    { color:#f9fafb; }
                .af-event-dot   { border-color:#0c0a09; }
                .af-field-chip  { background:#292524; color:#e5e7eb; border-color:#44403c; }
                .af-id-chip     { background:#292524; color:#a8a29e; border-color:#44403c; }
                .af-day-line    { background:#292524; }
                .af-load-btn    { background:#292524; border-color:#44403c; color:#e5e7eb; }
                .af-empty-icon  { background:#0c1a33; border-color:#1e3a5f; }
            }
        </style>

        {{-- ── Stats ── --}}
        <div class="af-stats">
            <div class="af-stat created">
                <div class="af-stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </div>
                <div class="af-stat-val">{{ $todayCreated }}</div>
                <div class="af-stat-label">Data Ditambahkan</div>
            </div>
            <div class="af-stat updated">
                <div class="af-stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <div class="af-stat-val">{{ $todayUpdated }}</div>
                <div class="af-stat-label">Data Diubah</div>
            </div>
            <div class="af-stat deleted">
                <div class="af-stat-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        <path d="M10 11v6"/><path d="M14 11v6"/>
                    </svg>
                </div>
                <div class="af-stat-val">{{ $todayDeleted }}</div>
                <div class="af-stat-label">Data Dihapus</div>
            </div>
        </div>

        {{-- ── Filter bar ── --}}
        <div class="af-filter-bar">
            <div class="af-filter-group">
                @foreach(['today' => 'Hari Ini', 'week' => '7 Hari', 'month' => 'Bulan Ini'] as $key => $label)
                    <button wire:click="setPeriod('{{ $key }}')"
                            class="af-fbtn {{ $filterPeriod === $key ? 'fp-active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="af-filter-div"></div>
            <div class="af-filter-group">
                @foreach(['all' => 'Semua', 'created' => 'Tambah', 'updated' => 'Ubah', 'deleted' => 'Hapus'] as $key => $label)
                    <button wire:click="setEvent('{{ $key }}')"
                            class="af-fbtn {{ $filterEvent === $key ? 'fe-active-'.$key : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── Timeline ── --}}
        @if($activities->isEmpty())
            <div class="af-empty">
                <div class="af-empty-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6"
                         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                    </svg>
                </div>
                <p class="af-empty-title">Belum ada aktivitas</p>
                <p class="af-empty-sub">Tidak ada perubahan data pada periode yang dipilih.</p>
            </div>
        @else
            <div class="af-timeline">
                @php $lastDate = null; @endphp

                @foreach ($activities as $activity)
                    @php
                        $currentDate = \Carbon\Carbon::parse($activity['timeAbs'])->format('Y-m-d');
                        $eventClass  = in_array($activity['event'], ['created','updated','deleted']) ? $activity['event'] : 'other';
                        $dotIcon = match($activity['event']) {
                            'created' => '<line x1="12" y1="8" x2="12" y2="12"/><line x1="10" y1="10" x2="14" y2="10"/>',
                            'updated' => '<path d="M11 4H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3"/>',
                            'deleted' => '<polyline points="3 6 5 6 11 6"/><path d="M10 9v6"/>',
                            default   => '<circle cx="6" cy="6" r="2"/>',
                        };
                    @endphp

                    {{-- Day separator --}}
                    @if($currentDate !== $lastDate)
                        @php $lastDate = $currentDate; @endphp
                        <div class="af-day-sep">
                            <span class="af-day-label">
                                @if($activity['isToday'])
                                    📅 Hari ini
                                @else
                                    {{ \Carbon\Carbon::parse($currentDate)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                @endif
                            </span>
                            <div class="af-day-line"></div>
                        </div>
                    @endif

                    <div class="af-item">
                        {{-- Avatar + dot --}}
                        <div class="af-avatar-col">
                            <div class="af-avatar">{{ $activity['initial'] }}</div>
                            <div class="af-event-dot {{ $eventClass }}">
                                <svg width="7" height="7" viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round">{!! $dotIcon !!}</svg>
                            </div>
                        </div>

                        {{-- Card --}}
                        <div class="af-item-card">
                            <div class="af-item-top">
                                <p class="af-sentence">
                                    <span class="af-who">{{ $activity['causerName'] }}</span>
                                    {{ $activity['eventText'] }} data
                                    @if($activity['subjectLabel'])
                                        <span class="af-model {{ $eventClass }}">{{ $activity['subjectLabel'] }}</span>
                                    @endif
                                    @if($activity['subjectId'])
                                        <span class="af-id-chip">#{{ $activity['subjectId'] }}</span>
                                    @endif
                                </p>
                                <div style="text-align:right;flex-shrink:0;">
                                    <span class="af-time">{{ $activity['timeRel'] }}</span>
                                    <span class="af-time-abs">{{ \Carbon\Carbon::parse($activity['timeAbs'])->format('H:i') }}</span>
                                </div>
                            </div>

                            {{-- Changed fields --}}
                            @if(!empty($activity['changedFields']))
                                <div class="af-fields">
                                    <span style="font-size:10px;color:#9ca3af;font-weight:600;align-self:center;margin-right:2px;">Field:</span>
                                    @foreach($activity['changedFields'] as $field)
                                        <span class="af-field-chip">
                                            <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4L16.5 3.5z"/></svg>
                                            {{ str_replace('_', ' ', $field) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Load more --}}
            @if($hasMore)
                <div class="af-load-more">
                    <button wire:click="loadMore" class="af-load-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="loadMore">↓ &nbsp;Tampilkan Lebih Banyak</span>
                        <span wire:loading wire:target="loadMore">Memuat...</span>
                    </button>
                </div>
            @endif
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
