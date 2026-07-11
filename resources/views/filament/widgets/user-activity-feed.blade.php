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
                        <p style="margin:0;font-size:14px;font-weight:700;color:var(--fi-color-gray-950,#111827);">Aktivitas Pengguna</p>
                        <p style="margin:2px 0 0;font-size:11px;color:var(--fi-color-gray-500,#6b7280);font-weight:400;">
                            Log perubahan data oleh pengguna
                        </p>
                    </div>
                </div>
                <span style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:8px;
                             background:rgba(14,165,233,.1);border:1px solid rgba(14,165,233,.25);color:#0284c7;">
                    {{ $total }} aktivitas
                </span>
            </div>
        </x-slot>

        <style>
            /* ── Stats ── */
            .af-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:16px; }
            @media(max-width:640px){ .af-stats{grid-template-columns:1fr} }

            .af-stat {
                padding:16px; border-radius:14px; border:1px solid;
                position:relative; overflow:hidden;
                transition:transform .15s, box-shadow .15s;
            }
            .af-stat:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.1); }
            .af-stat::before {
                content:''; position:absolute; top:0; left:0; right:0;
                height:3px; border-radius:14px 14px 0 0;
            }

            /* Light mode stat cards */
            .af-stat.created { background:linear-gradient(160deg,#f0fdf4,#dcfce7); border-color:#86efac; }
            .af-stat.updated { background:linear-gradient(160deg,#eff6ff,#dbeafe); border-color:#93c5fd; }
            .af-stat.deleted { background:linear-gradient(160deg,#fff1f2,#fecdd3); border-color:#fca5a5; }
            .af-stat.created::before { background:linear-gradient(90deg,#10b981,#34d399); }
            .af-stat.updated::before { background:linear-gradient(90deg,#3b82f6,#60a5fa); }
            .af-stat.deleted::before { background:linear-gradient(90deg,#ef4444,#f87171); }

            .af-stat-icon {
                width:38px; height:38px; border-radius:10px;
                display:flex; align-items:center; justify-content:center; margin-bottom:12px;
            }
            .af-stat.created .af-stat-icon { background:rgba(16,185,129,.15); }
            .af-stat.updated .af-stat-icon { background:rgba(59,130,246,.12); }
            .af-stat.deleted .af-stat-icon { background:rgba(239,68,68,.12); }

            .af-stat-val { font-size:28px; font-weight:900; letter-spacing:-.03em; line-height:1; }
            .af-stat.created .af-stat-val { color:#047857; }
            .af-stat.updated .af-stat-val { color:#1e40af; }
            .af-stat.deleted .af-stat-val { color:#b91c1c; }
            .af-stat-label { font-size:11px; color:#4b5563; margin-top:4px; font-weight:600; }

            /* Dark stat cards */
            .dark .af-stat.created { background:linear-gradient(160deg,#052e16,#14532d); border-color:#166534; }
            .dark .af-stat.updated { background:linear-gradient(160deg,#0c1a33,#1e3a5f); border-color:#1d4ed8; }
            .dark .af-stat.deleted { background:linear-gradient(160deg,#1a0505,#3f1d1d); border-color:#7f1d1d; }
            .dark .af-stat.created .af-stat-icon { background:rgba(16,185,129,.2); }
            .dark .af-stat.updated .af-stat-icon { background:rgba(59,130,246,.2); }
            .dark .af-stat.deleted .af-stat-icon { background:rgba(239,68,68,.2); }
            .dark .af-stat.created .af-stat-val { color:#4ade80; }
            .dark .af-stat.updated .af-stat-val { color:#60a5fa; }
            .dark .af-stat.deleted .af-stat-val { color:#f87171; }
            .dark .af-stat-label { color:#a8a29e; }

            /* ── Filter bar ── */
            .af-filter-bar {
                display:flex; align-items:center; gap:6px; margin-bottom:16px;
                padding:6px 8px; border-radius:12px; flex-wrap:wrap;
                background:#f3f4f6; border:1px solid #e5e7eb;
            }
            .dark .af-filter-bar { background:#1c1917; border-color:#292524; }

            .af-filter-group {
                display:flex; gap:2px; border-radius:8px; padding:3px;
                background:#fff; border:1px solid #e5e7eb;
                box-shadow:0 1px 2px rgba(0,0,0,.04);
            }
            .dark .af-filter-group { background:#111110; border-color:#292524; box-shadow:none; }

            .af-fbtn {
                padding:4px 12px; border-radius:6px; font-size:11.5px; font-weight:600;
                border:none; background:transparent; cursor:pointer; transition:all .12s;
                color:#4b5563;
            }
            .dark .af-fbtn { color:#a8a29e; }
            .af-fbtn:hover { color:#111827; background:#f9fafb; }
            .dark .af-fbtn:hover { color:#e5e7eb; background:transparent; }
            .af-fbtn.fp-active        { background:linear-gradient(135deg,#0ea5e9,#0284c7); color:#fff !important; box-shadow:0 2px 8px rgba(14,165,233,.3); }
            .af-fbtn.fe-active-all    { background:#1f2937; color:#fff !important; }
            .af-fbtn.fe-active-created{ background:#047857; color:#fff !important; }
            .af-fbtn.fe-active-updated{ background:#1e40af; color:#fff !important; }
            .af-fbtn.fe-active-deleted{ background:#b91c1c; color:#fff !important; }

            .af-filter-div { width:1px; height:22px; background:#e5e7eb; margin:0 2px; flex-shrink:0; }
            .dark .af-filter-div { background:#44403c; }

            /* ── Timeline ── */
            .af-timeline { position:relative; }
            .af-timeline::before {
                content:''; position:absolute; left:19px; top:0; bottom:0;
                width:2px; background:linear-gradient(180deg,#e5e7eb,transparent);
                border-radius:2px;
            }
            .dark .af-timeline::before { background:linear-gradient(180deg,#292524,transparent); }

            .af-item {
                display:flex; gap:14px; padding:12px 0;
                border-bottom:1px solid #f3f4f6; position:relative;
            }
            .dark .af-item { border-color:#1c1917; }
            .af-item:last-child { border-bottom:none; }

            .af-avatar-col { flex-shrink:0; position:relative; z-index:1; }
            .af-avatar {
                width:38px; height:38px; border-radius:11px;
                display:flex; align-items:center; justify-content:center;
                font-size:14px; font-weight:800; color:#fff !important;
                box-shadow:0 2px 8px rgba(0,0,0,.15);
                background:linear-gradient(135deg,#0ea5e9,#0284c7);
            }

            .af-event-dot {
                position:absolute; bottom:-2px; right:-2px;
                width:14px; height:14px; border-radius:50%;
                display:flex; align-items:center; justify-content:center;
                border:2px solid #fff;
            }
            .dark .af-event-dot { border-color:#0c0a09; }
            .af-event-dot.created { background:#10b981; }
            .af-event-dot.updated { background:#3b82f6; }
            .af-event-dot.deleted { background:#ef4444; }
            .af-event-dot.other   { background:#8b5cf6; }

            /* Card */
            .af-item-card {
                flex:1; min-width:0; padding:10px 12px;
                background:#f9fafb; border:1px solid #e5e7eb;
                border-radius:11px; transition:background .12s;
            }
            .dark .af-item-card { background:#1c1917; border-color:#292524; }
            .af-item:hover .af-item-card { background:#f3f4f6; }
            .dark .af-item:hover .af-item-card { background:#111110; }

            .af-item-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:5px; }

            .af-sentence { font-size:13px; color:#111827 !important; line-height:1.5; margin:0; }
            .dark .af-sentence { color:#f5f5f4 !important; }
            .af-sentence .af-who  { color:#0284c7 !important; font-weight:700; }
            .dark .af-sentence .af-who { color:#38bdf8 !important; }

            .af-model {
                display:inline-flex; align-items:center; gap:3px;
                padding:1px 7px; border-radius:5px; font-size:11px; font-weight:700;
                border:1px solid; vertical-align:middle; margin:0 2px;
            }
            .af-model.created { background:#ecfdf5; color:#065f46 !important; border-color:#a7f3d0; }
            .af-model.updated { background:#eff6ff; color:#1e40af !important; border-color:#bfdbfe; }
            .af-model.deleted { background:#fef2f2; color:#991b1b !important; border-color:#fca5a5; }
            .af-model.other   { background:#f5f3ff; color:#5b21b6 !important; border-color:#c4b5fd; }

            .af-id-chip {
                display:inline-flex; font-family:ui-monospace,monospace;
                padding:1px 6px; border-radius:4px; font-size:10.5px; font-weight:700;
                background:#f3f4f6; color:#6b7280 !important; border:1px solid #e5e7eb;
                vertical-align:middle; margin-left:2px;
            }
            .dark .af-id-chip { background:#292524; color:#a8a29e !important; border-color:#44403c; }

            .af-time { font-size:11px; font-weight:600; color:#6b7280 !important; white-space:nowrap; flex-shrink:0; }
            .dark .af-time { color:#78716c !important; }
            .af-time-abs { font-size:10px; color:#9ca3af !important; display:block; margin-top:1px; text-align:right; }
            .dark .af-time-abs { color:#57534e !important; }

            /* Changed fields */
            .af-fields { display:flex; flex-wrap:wrap; gap:4px; margin-top:7px; align-items:center; }
            .af-fields-label { font-size:10px; color:#9ca3af !important; font-weight:600; margin-right:2px; }
            .dark .af-fields-label { color:#78716c !important; }
            .af-field-chip {
                display:inline-flex; align-items:center; gap:3px;
                padding:2px 8px; border-radius:5px; font-size:10.5px; font-weight:500;
                background:#fff; color:#374151 !important; border:1px solid #e5e7eb;
            }
            .dark .af-field-chip { background:#292524; color:#d4d0cb !important; border-color:#44403c; }

            /* Day separator */
            .af-day-sep { display:flex; align-items:center; gap:10px; padding:8px 0; margin:4px 0; }
            .af-day-label {
                font-size:10px; font-weight:800; text-transform:uppercase;
                letter-spacing:.1em; color:#6b7280 !important; white-space:nowrap; padding-left:52px;
            }
            .dark .af-day-label { color:#78716c !important; }
            .af-day-line { flex:1; height:1px; background:#e5e7eb; }
            .dark .af-day-line { background:#292524; }

            /* Load more */
            .af-load-more { display:flex; justify-content:center; padding:16px 0 4px; }
            .af-load-btn {
                padding:8px 24px; border-radius:9px; font-size:12px; font-weight:700;
                background:#fff; border:1px solid #e5e7eb; color:#374151 !important;
                cursor:pointer; transition:all .12s; box-shadow:0 1px 3px rgba(0,0,0,.06);
            }
            .dark .af-load-btn { background:#292524; border-color:#44403c; color:#e5e7eb !important; }
            .af-load-btn:hover { border-color:#7dd3fc; color:#0284c7 !important; box-shadow:0 2px 8px rgba(14,165,233,.15); }
            .dark .af-load-btn:hover { background:#3c3836; border-color:#7dd3fc; color:#38bdf8 !important; }

            /* Empty */
            .af-empty { display:flex;flex-direction:column;align-items:center;padding:52px 20px;text-align:center; }
            .af-empty-icon {
                width:56px;height:56px;border-radius:16px;
                background:linear-gradient(135deg,#eff6ff,#dbeafe);
                border:1px solid #bfdbfe;
                display:flex;align-items:center;justify-content:center;margin-bottom:14px;
                box-shadow:0 4px 16px rgba(14,165,233,.1);
            }
            .dark .af-empty-icon { background:#0c1a33; border-color:#1e3a5f; }
            .af-empty-title { font-size:14px;font-weight:700;color:#374151 !important;margin:0; }
            .dark .af-empty-title { color:#d4d0cb !important; }
            .af-empty-sub { font-size:12px;color:#9ca3af !important;margin:5px 0 0; }
            .dark .af-empty-sub { color:#78716c !important; }
        </style>

        {{-- ── Stats ── --}}
        <div class="af-stats">
            <div class="af-stat created">
                <div class="af-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </div>
                <div class="af-stat-val">{{ $todayCreated }}</div>
                <div class="af-stat-label">Data Ditambahkan</div>
            </div>
            <div class="af-stat updated">
                <div class="af-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <div class="af-stat-val">{{ $todayUpdated }}</div>
                <div class="af-stat-label">Data Diubah</div>
            </div>
            <div class="af-stat deleted">
                <div class="af-stat-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
                        <div class="af-avatar-col">
                            <div class="af-avatar">{{ $activity['initial'] }}</div>
                            <div class="af-event-dot {{ $eventClass }}">
                                <svg width="7" height="7" viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round">{!! $dotIcon !!}</svg>
                            </div>
                        </div>

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

                            @if(!empty($activity['changedFields']))
                                <div class="af-fields">
                                    <span class="af-fields-label">Field:</span>
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
