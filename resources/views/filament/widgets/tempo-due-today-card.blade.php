<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <span class="flex items-center gap-2">
                <span class="tm-heading-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
                <span class="tm-heading-text">Jadwal Perawatan</span>
                @if($tempos->count() > 0)
                    <span class="tm-heading-count">{{ $tempos->count() }}</span>
                @endif
            </span>
        </x-slot>

        <div class="tm-grid">
            @forelse ($tempos as $record)
            @php
                $dueDate       = \Carbon\Carbon::parse($record->jatuh_tempo);
                $daysRemaining = now()->diffInDays($dueDate, false);
                $isOverdue     = $daysRemaining < 0;
                $isCritical    = !$isOverdue && $daysRemaining <= 7;
                $isWarning     = !$isOverdue && $daysRemaining > 7 && $daysRemaining <= 15;
                $isSafe        = $daysRemaining > 15;

                // Urgency level
                if ($isOverdue)       { $level = 'overdue'; }
                elseif ($isCritical)  { $level = 'critical'; }
                elseif ($isWarning)   { $level = 'warning'; }
                else                  { $level = 'safe'; }

                $perawatanText = match($record->perawatan) {
                    'pajak'   => 'Pajak STNK',
                    'service' => 'Service Berkala',
                    default   => ucfirst($record->perawatan)
                };

                $perawatanIcon = $record->perawatan === 'pajak'
                    ? '<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/>'
                    : '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/>';
            @endphp

            <div class="tm-card tm-card--{{ $level }}">
                {{-- Top bar --}}
                <div class="tm-bar tm-bar--{{ $level }}"></div>

                <div class="tm-card-inner">

                    {{-- Header --}}
                    <div class="tm-card-header">
                        <div class="tm-car-icon tm-car-icon--{{ $level }}">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                                <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
                            </svg>
                        </div>
                        <div class="tm-car-info">
                            <p class="tm-car-name">
                                @if($record->car && $record->car->carModel)
                                    {{ $record->car->carModel->name }}
                                @else
                                    <span style="color:#9ca3af;font-style:italic">Mobil dihapus</span>
                                @endif
                            </p>
                            <span class="tm-nopol">{{ $record->car->nopol ?? '—' }}</span>
                        </div>
                        {{-- Urgency pill --}}
                        <span class="tm-urgency tm-urgency--{{ $level }}">
                            @if($isOverdue) Terlambat
                            @elseif($isCritical) Segera
                            @elseif($isWarning) Mendekati
                            @else Aman
                            @endif
                        </span>
                    </div>

                    {{-- Body rows --}}
                    <div class="tm-rows">
                        <div class="tm-row">
                            <span class="tm-row-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $perawatanIcon !!}
                                </svg>
                            </span>
                            <span class="tm-row-label">Jenis Perawatan</span>
                            <span class="tm-row-value tm-row-value--{{ $level }}">{{ $perawatanText }}</span>
                        </div>

                        <div class="tm-row">
                            <span class="tm-row-icon">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                            </span>
                            <span class="tm-row-label">Tanggal Jatuh Tempo</span>
                            <span class="tm-row-value">{{ $dueDate->format('d M Y') }}</span>
                        </div>
                    </div>

                    {{-- Due countdown --}}
                    <div class="tm-countdown tm-countdown--{{ $level }}">
                        <div class="tm-countdown-icon tm-countdown-icon--{{ $level }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                @if($isOverdue)
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                                @else
                                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                @endif
                            </svg>
                        </div>
                        <span class="tm-countdown-text tm-countdown-text--{{ $level }}">
                            {{ $dueDate->locale('id')->diffForHumans() }}
                        </span>
                        @if(!$isOverdue)
                        <span class="tm-days-badge tm-days-badge--{{ $level }}">
                            {{ (int)abs($daysRemaining) }} hari lagi
                        </span>
                        @endif
                    </div>

                    {{-- Action --}}
                    <button wire:click="selesaikanTempo({{ $record->id }})"
                        wire:loading.attr="disabled"
                        class="tm-btn tm-btn--{{ $level }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        Tandai Selesai
                    </button>

                </div>
            </div>
            @empty
            <div class="tm-empty">
                <div class="tm-empty-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <p class="tm-empty-title">Tidak ada jadwal perawatan</p>
                <p class="tm-empty-sub">Semua kendaraan dalam kondisi terjadwal baik.</p>
            </div>
            @endforelse
        </div>

    </x-filament::section>
</x-filament-widgets::widget>

<style>
    /* Heading */
    .tm-heading-icon {
        width:26px; height:26px; border-radius:7px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        background:linear-gradient(135deg,#fef3c7,#fde68a);
        color:#d97706; border:1px solid #fde68a;
    }
    .dark .tm-heading-icon { background:#1c1007; border-color:#44321a; color:#fbbf24; }
    .tm-heading-text { font-size:15px; font-weight:700; color:#92400e; }
    .dark .tm-heading-text { color:#fcd34d; }
    .tm-heading-count {
        min-width:20px; height:20px; padding:0 6px;
        background:#f59e0b; color:#fff;
        font-size:11px; font-weight:700; border-radius:100px;
        display:inline-flex; align-items:center; justify-content:center;
    }

    /* Grid */
    .tm-grid {
        display:grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap:12px;
    }

    /* Card */
    .tm-card {
        border-radius:14px; overflow:hidden;
        transition:transform .18s, box-shadow .18s;
        border:1px solid;
    }
    .tm-card:hover { transform:translateY(-2px); }

    .tm-card--overdue  { background:#fff; border-color:#fecaca; box-shadow:0 2px 8px rgba(220,38,38,.08); }
    .tm-card--critical { background:#fff; border-color:#fecaca; box-shadow:0 2px 8px rgba(239,68,68,.07); }
    .tm-card--warning  { background:#fff; border-color:#fde68a; box-shadow:0 2px 8px rgba(245,158,11,.07); }
    .tm-card--safe     { background:#fff; border-color:#bbf7d0; box-shadow:0 2px 8px rgba(16,185,129,.07); }

    .tm-card--overdue:hover  { box-shadow:0 8px 24px rgba(220,38,38,.15); }
    .tm-card--critical:hover { box-shadow:0 8px 24px rgba(239,68,68,.14); }
    .tm-card--warning:hover  { box-shadow:0 8px 24px rgba(245,158,11,.14); }
    .tm-card--safe:hover     { box-shadow:0 8px 24px rgba(16,185,129,.14); }

    .dark .tm-card--overdue  { background:#1a0a0a; border-color:#7f1d1d; }
    .dark .tm-card--critical { background:#1a0a0a; border-color:#7f1d1d; }
    .dark .tm-card--warning  { background:#1c1007; border-color:#78350f; }
    .dark .tm-card--safe     { background:#052e16; border-color:#14532d; }

    /* Accent bar */
    .tm-bar { height:4px; }
    .tm-bar--overdue, .tm-bar--critical { background:linear-gradient(90deg,#f87171,#dc2626); }
    .tm-bar--warning  { background:linear-gradient(90deg,#fbbf24,#d97706); }
    .tm-bar--safe     { background:linear-gradient(90deg,#34d399,#059669); }

    .tm-card-inner { padding:14px 16px 16px; display:flex; flex-direction:column; gap:12px; }

    /* Header */
    .tm-card-header { display:flex; align-items:center; gap:10px; }

    .tm-car-icon {
        width:38px; height:38px; border-radius:10px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center; border:1px solid;
    }
    .tm-car-icon--overdue, .tm-car-icon--critical { background:#fff1f2; border-color:#fecaca; color:#dc2626; }
    .tm-car-icon--warning  { background:#fffbeb; border-color:#fde68a; color:#d97706; }
    .tm-car-icon--safe     { background:#f0fdf4; border-color:#bbf7d0; color:#059669; }
    .dark .tm-car-icon--overdue, .dark .tm-car-icon--critical { background:#450a0a; border-color:#7f1d1d; color:#f87171; }
    .dark .tm-car-icon--warning { background:#1c1007; border-color:#78350f; color:#fbbf24; }
    .dark .tm-car-icon--safe    { background:#052e16; border-color:#14532d; color:#4ade80; }

    .tm-car-info { flex:1; min-width:0; }
    .tm-car-name { font-size:13px; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin:0; }
    .dark .tm-car-name { color:#f1f5f9; }
    .tm-nopol {
        font-size:10px; font-weight:600; color:#6b7280;
        font-family:ui-monospace,monospace; letter-spacing:.05em;
        background:#f1f5f9; padding:1px 6px; border-radius:4px;
        margin-top:3px; display:inline-block;
    }
    .dark .tm-nopol { background:#374151; color:#9ca3af; }

    /* Urgency pill */
    .tm-urgency {
        font-size:10px; font-weight:700; padding:3px 8px;
        border-radius:100px; white-space:nowrap; border:1px solid; flex-shrink:0;
    }
    .tm-urgency--overdue, .tm-urgency--critical { background:#fff1f2; color:#b91c1c; border-color:#fecaca; }
    .tm-urgency--warning  { background:#fffbeb; color:#92400e; border-color:#fde68a; }
    .tm-urgency--safe     { background:#f0fdf4; color:#15803d; border-color:#bbf7d0; }
    .dark .tm-urgency--overdue, .dark .tm-urgency--critical { background:#450a0a; color:#f87171; border-color:#7f1d1d; }
    .dark .tm-urgency--warning { background:#1c1007; color:#fbbf24; border-color:#78350f; }
    .dark .tm-urgency--safe    { background:#052e16; color:#4ade80; border-color:#14532d; }

    /* Rows */
    .tm-rows {
        display:flex; flex-direction:column;
        border:1px solid #f1f5f9; border-radius:9px; overflow:hidden;
    }
    .dark .tm-rows { border-color:#374151; }
    .tm-row {
        display:flex; align-items:center; gap:7px;
        padding:8px 11px; border-bottom:1px solid #f8fafc;
        transition:background .12s;
    }
    .dark .tm-row { border-color:#374151; }
    .tm-row:last-child { border-bottom:none; }
    .tm-row:hover { background:#f8fafc; }
    .dark .tm-row:hover { background:#111827; }
    .tm-row-icon { color:#9ca3af; flex-shrink:0; display:flex; }
    .tm-row-label { font-size:11px; color:#9ca3af; flex:1; }
    .dark .tm-row-label { color:#6b7280; }
    .tm-row-value { font-size:12px; font-weight:600; color:#374151; }
    .dark .tm-row-value { color:#d1d5db; }
    .tm-row-value--overdue, .tm-row-value--critical { color:#dc2626 !important; }
    .tm-row-value--warning  { color:#d97706 !important; }
    .tm-row-value--safe     { color:#059669 !important; }
    .dark .tm-row-value--overdue, .dark .tm-row-value--critical { color:#f87171 !important; }
    .dark .tm-row-value--warning { color:#fbbf24 !important; }
    .dark .tm-row-value--safe    { color:#34d399 !important; }

    /* Countdown block */
    .tm-countdown {
        display:flex; align-items:center; gap:8px;
        padding:10px 12px; border-radius:10px; border:1px solid;
    }
    .tm-countdown--overdue, .tm-countdown--critical { background:linear-gradient(135deg,#fff1f2,#fee2e2); border-color:#fecaca; }
    .tm-countdown--warning  { background:linear-gradient(135deg,#fffbeb,#fef3c7); border-color:#fde68a; }
    .tm-countdown--safe     { background:linear-gradient(135deg,#f0fdf4,#dcfce7); border-color:#bbf7d0; }
    .dark .tm-countdown--overdue, .dark .tm-countdown--critical { background:#1a0a0a; border-color:#7f1d1d; }
    .dark .tm-countdown--warning { background:#1c1007; border-color:#78350f; }
    .dark .tm-countdown--safe    { background:#052e16; border-color:#14532d; }

    .tm-countdown-icon { flex-shrink:0; display:flex; }
    .tm-countdown-icon--overdue, .tm-countdown-icon--critical { color:#dc2626; }
    .tm-countdown-icon--warning  { color:#d97706; }
    .tm-countdown-icon--safe     { color:#059669; }
    .dark .tm-countdown-icon--overdue, .dark .tm-countdown-icon--critical { color:#f87171; }
    .dark .tm-countdown-icon--warning { color:#fbbf24; }
    .dark .tm-countdown-icon--safe    { color:#34d399; }

    .tm-countdown-text { font-size:13px; font-weight:700; flex:1; }
    .tm-countdown-text--overdue, .tm-countdown-text--critical { color:#b91c1c; }
    .tm-countdown-text--warning  { color:#92400e; }
    .tm-countdown-text--safe     { color:#15803d; }
    .dark .tm-countdown-text--overdue, .dark .tm-countdown-text--critical { color:#fca5a5; }
    .dark .tm-countdown-text--warning { color:#fcd34d; }
    .dark .tm-countdown-text--safe    { color:#4ade80; }

    .tm-days-badge {
        font-size:10px; font-weight:700; padding:2px 7px;
        border-radius:100px; white-space:nowrap;
    }
    .tm-days-badge--critical { background:#dc2626; color:#fff; }
    .tm-days-badge--warning  { background:#d97706; color:#fff; }
    .tm-days-badge--safe     { background:#059669; color:#fff; }

    /* Button */
    .tm-btn {
        width:100%; display:flex; align-items:center; justify-content:center; gap:7px;
        padding:9px; border:none; border-radius:10px;
        font-size:13px; font-weight:700; cursor:pointer;
        color:#fff; font-family:inherit;
        transition:transform .15s, filter .15s, box-shadow .15s;
    }
    .tm-btn:hover { transform:translateY(-1px); filter:brightness(1.06); }
    .tm-btn:active { transform:translateY(0); }
    .tm-btn--overdue, .tm-btn--critical {
        background:linear-gradient(135deg,#f87171,#dc2626);
        box-shadow:0 4px 12px rgba(220,38,38,.35);
    }
    .tm-btn--warning {
        background:linear-gradient(135deg,#fbbf24,#d97706);
        box-shadow:0 4px 12px rgba(217,119,6,.35);
    }
    .tm-btn--safe {
        background:linear-gradient(135deg,#34d399,#059669);
        box-shadow:0 4px 12px rgba(5,150,105,.35);
    }

    /* Empty */
    .tm-empty {
        grid-column:1/-1;
        display:flex; flex-direction:column; align-items:center; gap:10px;
        padding:44px 20px; text-align:center;
        border:1.5px dashed #d1fae5; border-radius:14px;
        background:linear-gradient(135deg,#f0fdf4,#dcfce7);
    }
    .dark .tm-empty { background:#052e16; border-color:#14532d; }
    .tm-empty-icon {
        width:56px; height:56px; border-radius:50%;
        background:#fff; border:1px solid #bbf7d0;
        box-shadow:0 2px 10px rgba(16,185,129,.18);
        display:flex; align-items:center; justify-content:center;
        color:#16a34a;
    }
    .dark .tm-empty-icon { background:#0a3d1f; border-color:#14532d; box-shadow:none; }
    .tm-empty-title { font-size:14px; font-weight:700; color:#15803d; margin:0; }
    .dark .tm-empty-title { color:#4ade80; }
    .tm-empty-sub  { font-size:12px; color:#86efac; margin:0; }
    .dark .tm-empty-sub { color:#166534; }
</style>
