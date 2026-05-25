<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <span class="flex items-center gap-2">
                <span class="tp-heading-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
                <span class="tp-heading-text">Jadwal Perawatan</span>
                @if($tempos->count() > 0)
                <span class="tp-heading-count">{{ $tempos->count() }}</span>
                @endif
            </span>
        </x-slot>

        <div class="tp-grid">
            @forelse ($tempos as $record)
            @php
                $dueDate       = \Carbon\Carbon::parse($record->jatuh_tempo);
                $daysRemaining = now()->diffInDays($dueDate, false);

                if ($daysRemaining < 0) {
                    $urgency      = 'overdue';
                    $accentColor  = '#dc2626';
                    $accentLight  = '#fff1f2';
                    $accentBorder = '#fecaca';
                    $accentText   = '#991b1b';
                    $badgeBg      = '#fee2e2';
                    $badgeText    = '#991b1b';
                    $label        = 'Lewat Jatuh Tempo';
                } elseif ($daysRemaining <= 7) {
                    $urgency      = 'critical';
                    $accentColor  = '#ef4444';
                    $accentLight  = '#fff1f2';
                    $accentBorder = '#fecaca';
                    $accentText   = '#b91c1c';
                    $badgeBg      = '#fee2e2';
                    $badgeText    = '#b91c1c';
                    $label        = '≤ 7 hari';
                } elseif ($daysRemaining <= 15) {
                    $urgency      = 'warning';
                    $accentColor  = '#f59e0b';
                    $accentLight  = '#fffbeb';
                    $accentBorder = '#fde68a';
                    $accentText   = '#92400e';
                    $badgeBg      = '#fef3c7';
                    $badgeText    = '#92400e';
                    $label        = '≤ 15 hari';
                } elseif ($daysRemaining <= 30) {
                    $urgency      = 'soon';
                    $accentColor  = '#10b981';
                    $accentLight  = '#f0fdf4';
                    $accentBorder = '#bbf7d0';
                    $accentText   = '#065f46';
                    $badgeBg      = '#dcfce7';
                    $badgeText    = '#065f46';
                    $label        = '≤ 30 hari';
                } else {
                    $urgency      = 'ok';
                    $accentColor  = '#10b981';
                    $accentLight  = '#f0fdf4';
                    $accentBorder = '#bbf7d0';
                    $accentText   = '#065f46';
                    $badgeBg      = '#dcfce7';
                    $badgeText    = '#065f46';
                    $label        = 'Aman';
                }

                $perawatanText = match($record->perawatan) {
                    'pajak'   => 'Pajak STNK',
                    'service' => 'Service Berkala',
                    default   => ucfirst($record->perawatan)
                };

                $perawatanIcon = match($record->perawatan) {
                    'pajak'   => 'doc',
                    'service' => 'wrench',
                    default   => 'wrench',
                };
            @endphp

            <div class="tp-card" style="border-color:{{ $accentBorder }};">
                {{-- Bar --}}
                <div class="tp-bar" style="background:{{ $accentColor }};"></div>

                {{-- Subtle tint --}}
                <div class="tp-tint" style="background:linear-gradient(135deg,{{ $accentLight }},transparent 60%);"></div>

                <div class="tp-body">

                    {{-- Header --}}
                    <div class="tp-header">
                        <div class="tp-car-icon" style="background:{{ $accentLight }};border-color:{{ $accentBorder }};color:{{ $accentColor }};">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                                <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
                            </svg>
                        </div>
                        <div class="tp-car-info">
                            <p class="tp-car-name">
                                @if($record->car && $record->car->carModel)
                                    {{ $record->car->carModel->name }}
                                @else
                                    <span style="color:#9ca3af;font-style:italic">Mobil dihapus</span>
                                @endif
                            </p>
                            <span class="tp-nopol">{{ $record->car->nopol ?? '—' }}</span>
                        </div>
                        <span class="tp-urgency-badge" style="background:{{ $badgeBg }};color:{{ $badgeText }};border-color:{{ $accentBorder }};">
                            {{ $label }}
                        </span>
                    </div>

                    {{-- Divider --}}
                    <div class="tp-divider" style="border-color:{{ $accentBorder }};opacity:.5;"></div>

                    {{-- Detail rows --}}
                    <div class="tp-rows">
                        <div class="tp-row">
                            <span class="tp-row-icon" style="color:{{ $accentColor }};">
                                @if($perawatanIcon === 'doc')
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                                @else
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                </svg>
                                @endif
                            </span>
                            <span class="tp-row-label">Jenis Perawatan</span>
                            <span class="tp-row-value" style="color:{{ $accentColor }};">{{ $perawatanText }}</span>
                        </div>

                        <div class="tp-row">
                            <span class="tp-row-icon" style="color:{{ $accentColor }};">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                            </span>
                            <span class="tp-row-label">Jatuh Tempo</span>
                            <span class="tp-row-value">{{ $dueDate->locale('id')->isoFormat('D MMM Y') }}</span>
                        </div>

                        <div class="tp-row">
                            <span class="tp-row-icon" style="color:{{ $accentColor }};">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </span>
                            <span class="tp-row-label">Sisa Waktu</span>
                            <span class="tp-row-value" style="color:{{ $accentColor }};font-weight:700;">
                                {{ $dueDate->locale('id')->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    {{-- Action --}}
                    <button wire:click="selesaikanTempo({{ $record->id }})" wire:loading.attr="disabled"
                        class="tp-btn"
                        style="background:{{ $accentColor }};"
                        onmouseover="this.style.filter='brightness(.88)'"
                        onmouseout="this.style.filter='brightness(1)'">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Tandai Selesai
                    </button>

                </div>
            </div>
            @empty
            <div class="tp-empty">
                <div class="tp-empty-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <p class="tp-empty-title">Semua perawatan terjadwal</p>
                <p class="tp-empty-sub">Tidak ada jadwal perawatan yang mendekati jatuh tempo.</p>
            </div>
            @endforelse
        </div>

        <style>
            /* Heading */
            .tp-heading-icon {
                display:flex;align-items:center;justify-content:center;
                width:26px;height:26px;border-radius:7px;
                background:linear-gradient(135deg,#fef2f2,#fee2e2);
                border:1px solid #fecaca; color:#dc2626;
            }
            .dark .tp-heading-icon { background:#450a0a; border-color:#7f1d1d; color:#f87171; }
            .tp-heading-text { font-size:15px; font-weight:700; color:inherit; }
            .tp-heading-count {
                display:inline-flex;align-items:center;justify-content:center;
                min-width:20px;height:20px;padding:0 6px;
                background:#ef4444;color:#fff;
                font-size:11px;font-weight:700;border-radius:100px;
            }

            /* Grid */
            .tp-grid {
                display:grid;
                grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
                gap:12px;
            }

            /* Card */
            .tp-card {
                position:relative;border-radius:14px;
                border:1px solid;background:#fff;overflow:hidden;
                transition:transform .18s,box-shadow .18s;
            }
            .dark .tp-card { background:#1f2937; }
            .tp-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.1); }

            .tp-bar { height:4px;width:100%; }
            .tp-tint {
                position:absolute;inset:0;pointer-events:none;opacity:.35;
            }
            .dark .tp-tint { opacity:.1; }

            .tp-body { position:relative;z-index:1;padding:16px; display:flex;flex-direction:column;gap:12px; }

            /* Header */
            .tp-header { display:flex;align-items:center;gap:10px; }
            .tp-car-icon {
                width:38px;height:38px;border-radius:10px;flex-shrink:0;
                border:1px solid;display:flex;align-items:center;justify-content:center;
            }
            .tp-car-info { flex:1;min-width:0; }
            .tp-car-name {
                font-size:13px;font-weight:700;color:#0f172a;
                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
            }
            .dark .tp-car-name { color:#f1f5f9; }
            .tp-nopol {
                font-size:10px;font-weight:600;color:#6b7280;
                font-family:ui-monospace,monospace;letter-spacing:.05em;
            }
            .dark .tp-nopol { color:#9ca3af; }
            .tp-urgency-badge {
                font-size:10px;font-weight:700;
                padding:3px 9px;border-radius:100px;
                border:1px solid;white-space:nowrap;flex-shrink:0;
            }

            /* Divider */
            .tp-divider { border-top:1px dashed; }

            /* Rows */
            .tp-rows { display:flex;flex-direction:column;gap:0; }
            .tp-row {
                display:flex;align-items:center;gap:8px;
                padding:7px 0;border-bottom:1px solid #f8fafc;
            }
            .dark .tp-row { border-color:#374151; }
            .tp-row:last-child { border-bottom:none;padding-bottom:0; }
            .tp-row-icon { display:flex;flex-shrink:0; }
            .tp-row-label { font-size:11.5px;color:#9ca3af;flex:1; }
            .dark .tp-row-label { color:#6b7280; }
            .tp-row-value { font-size:12px;font-weight:600;color:#111827;text-align:right; }
            .dark .tp-row-value { color:#e5e7eb; }

            /* Button */
            .tp-btn {
                display:flex;align-items:center;justify-content:center;gap:6px;
                width:100%;padding:9px;border-radius:10px;
                border:none;font-size:13px;font-weight:700;
                color:#fff;cursor:pointer;font-family:inherit;
                transition:transform .15s,filter .15s;
                box-shadow:0 3px 10px rgba(0,0,0,.18);
            }
            .tp-btn:hover { transform:translateY(-1px); }
            .tp-btn:active { transform:translateY(0); }

            /* Empty */
            .tp-empty {
                grid-column:1/-1;
                display:flex;flex-direction:column;align-items:center;gap:10px;
                padding:40px 20px;border-radius:14px;
                border:1.5px dashed #d1fae5;
                background:linear-gradient(135deg,#f0fdf4,#dcfce7);
                text-align:center;
            }
            .dark .tp-empty { background:#052e16;border-color:#14532d; }
            .tp-empty-icon {
                width:52px;height:52px;border-radius:50%;
                background:#fff;border:1px solid #bbf7d0;
                display:flex;align-items:center;justify-content:center;
                color:#16a34a;box-shadow:0 2px 10px rgba(16,185,129,.15);
            }
            .dark .tp-empty-icon { background:#0a3d1f;border-color:#14532d; }
            .tp-empty-title { font-size:14px;font-weight:600;color:#15803d; }
            .dark .tp-empty-title { color:#4ade80; }
            .tp-empty-sub { font-size:12px;color:#86efac;max-width:280px; }
            .dark .tp-empty-sub { color:#166534; }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
