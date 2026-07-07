@php
    $isKeluar = $type === 'keluar';
    $isToday  = $isToday ?? true;

    $accentColor  = $isKeluar ? '#d97706' : '#16a34a';
    $accentLight  = $isKeluar ? '#fffbeb' : '#f0fdf4';
    $accentBorder = $isKeluar ? '#fde68a' : '#bbf7d0';
    $accentText   = $isKeluar ? '#92400e' : '#15803d';

    // Warna tombol aksi:
    // Pick Up hari ini  = Hijau   | Pick Up besok   = Biru
    // Kembali hari ini  = Merah   | Kembali besok   = Orange
    if ($isKeluar && $isToday)       { $btnColor = '#16a34a'; $btnHover = '#15803d'; }
    elseif ($isKeluar && !$isToday)  { $btnColor = '#2563eb'; $btnHover = '#1d4ed8'; }
    elseif (!$isKeluar && $isToday)  { $btnColor = '#dc2626'; $btnHover = '#b91c1c'; }
    else                             { $btnColor = '#ea580c'; $btnHover = '#c2410c'; }

    $timeLabel  = $isKeluar ? 'Waktu Keluar'   : 'Waktu Kembali';
    $dateLabel  = $isKeluar ? 'Tanggal Keluar' : 'Tanggal Kembali';
    $timeValue  = $isKeluar ? $booking->waktu_keluar   : $booking->waktu_kembali;
    $dateValue  = $isKeluar ? $booking->tanggal_keluar : $booking->tanggal_kembali;

    $statusText = match($booking->status) {
        'booking' => 'Booking',
        'disewa'  => 'Disewa',
        default   => ucfirst($booking->status),
    };
@endphp

<div class="bc-card">

    {{-- Accent bar --}}
    <div class="bc-accent-bar" style="background:{{ $accentColor }};"></div>

    <div class="bc-inner">

        {{-- ── Header ── --}}
        <div class="bc-header">
            <div class="bc-car-icon" style="background:{{ $accentLight }};border-color:{{ $accentBorder }};color:{{ $accentColor }};">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                    <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
                </svg>
            </div>

            <div class="bc-car-info">
                <p class="bc-car-name">
                    {{ $booking->car->carModel->brand->name ?? '' }}
                    {{ $booking->car->carModel->name ?? '—' }}
                </p>
                <p class="bc-car-nopol">{{ $booking->car->nopol ?? '—' }}</p>
            </div>

            <span class="bc-badge" style="background:{{ $accentLight }};color:{{ $accentText }};border-color:{{ $accentBorder }};">
                {{ $statusText }}
            </span>
        </div>

        {{-- ── Rows ── --}}
        <div class="bc-rows">

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </span>
                <span class="bc-row-label">Penyewa</span>
                <span class="bc-row-value">{{ $booking->customer->nama ?? '—' }}</span>
            </div>

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.62 3.45 2 2 0 0 1 3.59 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.6a16 16 0 0 0 6.29 6.29l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </span>
                <span class="bc-row-label">Telepon</span>
                <span class="bc-row-value">{{ $booking->customer->no_telp ?? '—' }}</span>
            </div>

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        <circle cx="12" cy="9" r="2.5"/>
                    </svg>
                </span>
                <span class="bc-row-label">{{ $isKeluar ? 'Drop Point' : 'Pick Up Point' }}</span>
                <span class="bc-row-value">
                    {{ $isKeluar ? ($booking->lokasi_pengantaran ?? '—') : ($booking->lokasi_pengembalian ?? '—') }}
                </span>
            </div>

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </span>
                <span class="bc-row-label">Durasi</span>
                <span class="bc-row-value">
                    {{ $booking->total_hari ?? \Carbon\Carbon::parse($booking->tanggal_keluar)->diffInDays($booking->tanggal_kembali) }} hari
                    &nbsp;·&nbsp;
                    {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->format('d M') }}
                    →
                    {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->format('d M Y') }}
                </span>
            </div>

            @if($booking->car->garasi ?? null)
            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </span>
                <span class="bc-row-label">Vendor</span>
                <span class="bc-row-value">{{ $booking->car->garasi }}</span>
            </div>
            @endif

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>
                <span class="bc-row-label">Staff</span>
                <span class="bc-row-value">
                    @php $staffName = $booking->driverPengantaran->nama ?? $booking->driver->nama ?? null; @endphp
                    @if($staffName)
                        <span class="bc-row-value--staff" style="background:{{ $accentLight }};color:{{ $accentText }};border-color:{{ $accentBorder }};">
                            {{ $staffName }}
                        </span>
                    @else
                        <span style="color:#c4bfbb;">Belum ditugaskan</span>
                    @endif
                </span>
            </div>

        </div>

        {{-- ── Time block ── --}}
        <div class="bc-time-block" style="background:{{ $accentLight }};border-color:{{ $accentBorder }};">
            <div class="bc-time-item">
                <span class="bc-time-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    {{ $timeLabel }}
                </span>
                <span class="bc-time-value" style="color:{{ $accentColor }};">
                    {{ $timeValue ? \Carbon\Carbon::parse($timeValue)->format('H:i') . ' WITA' : '—' }}
                </span>
            </div>
            <div class="bc-time-divider"></div>
            <div class="bc-time-item">
                <span class="bc-time-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    {{ $dateLabel }}
                </span>
                <span class="bc-time-value" style="color:{{ $accentColor }};">
                    {{ $dateValue ? \Carbon\Carbon::parse($dateValue)->locale('id')->isoFormat('ddd, D MMM Y') : '—' }}
                </span>
            </div>
        </div>

        {{-- ── Actions ── --}}
        @if($canPerformActions)
        <div class="bc-actions">
            <button wire:click="editBooking({{ $booking->id }})" class="bc-btn bc-btn--ghost">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit
            </button>

            @if($isKeluar)
                <button wire:click="pickupBooking({{ $booking->id }})"
                        wire:loading.attr="disabled"
                        class="bc-btn bc-btn--primary"
                        style="background:{{ $btnColor }};"
                        onmouseover="this.style.background='{{ $btnHover }}'"
                        onmouseout="this.style.background='{{ $btnColor }}'">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 8 16 12 12 16"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    Pick Up
                </button>
            @else
                <button wire:click="selesaikanBooking({{ $booking->id }})"
                        wire:loading.attr="disabled"
                        class="bc-btn bc-btn--primary"
                        style="background:{{ $btnColor }};"
                        onmouseover="this.style.background='{{ $btnHover }}'"
                        onmouseout="this.style.background='{{ $btnColor }}'">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Selesaikan
                </button>
            @endif
        </div>
        @endif

    </div>
</div>

<style>
    .bc-card {
        border-radius: 14px; border: 1px solid #e5e7eb;
        background: #ffffff; overflow: hidden;
        transition: box-shadow .18s, transform .18s;
    }
    .dark .bc-card { background: #1f2937; border-color: #374151; }
    .bc-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); transform: translateY(-1px); }

    .bc-accent-bar { height: 3px; width: 100%; }
    .bc-inner { padding: 16px; display: flex; flex-direction: column; gap: 14px; }

    .bc-header { display: flex; align-items: center; gap: 10px; }
    .bc-car-icon {
        width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
        border: 1px solid; display: flex; align-items: center; justify-content: center;
    }
    .bc-car-info { flex: 1; min-width: 0; }
    .bc-car-name {
        font-size: 13.5px; font-weight: 700; color: #0f172a;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .dark .bc-car-name { color: #f1f5f9; }
    .bc-car-nopol {
        font-size: 11px; font-weight: 600; color: #6b7280;
        font-family: ui-monospace, monospace; letter-spacing: .05em; margin-top: 1px;
    }
    .dark .bc-car-nopol { color: #9ca3af; }
    .bc-badge {
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 100px; border: 1px solid; white-space: nowrap; flex-shrink: 0;
    }

    .bc-rows {
        display: flex; flex-direction: column;
        border: 1px solid #f1f5f9; border-radius: 10px; overflow: hidden;
    }
    .dark .bc-rows { border-color: #374151; }
    .bc-row {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 12px; border-bottom: 1px solid #f8fafc; transition: background .12s;
    }
    .dark .bc-row { border-color: #374151; }
    .bc-row:last-child { border-bottom: none; }
    .bc-row:hover { background: #f8fafc; }
    .dark .bc-row:hover { background: #111827; }
    .bc-row-icon { color: #9ca3af; flex-shrink: 0; display: flex; }
    .bc-row-label { font-size: 11.5px; color: #9ca3af; flex: 1; }
    .dark .bc-row-label { color: #6b7280; }
    .bc-row-value {
        font-size: 12px; font-weight: 600; color: #111827;
        text-align: right; max-width: 60%; word-break: break-word; line-height: 1.4;
    }
    .dark .bc-row-value { color: #e5e7eb; }
    .bc-row-value--staff {
        display: inline-flex; align-items: center;
        padding: 2px 8px; border-radius: 100px; font-size: 11px; border: 1px solid;
    }

    .bc-time-block {
        display: flex; align-items: stretch;
        border: 1px solid; border-radius: 10px; overflow: hidden;
    }
    .bc-time-item { flex: 1; padding: 10px 12px; display: flex; flex-direction: column; gap: 4px; }
    .bc-time-label {
        display: flex; align-items: center; gap: 4px;
        font-size: 10.5px; font-weight: 600; color: #9ca3af;
        text-transform: uppercase; letter-spacing: .04em;
    }
    .bc-time-value { font-size: 13px; font-weight: 700; }
    .bc-time-divider { width: 1px; background: currentColor; opacity: .15; align-self: stretch; }

    .bc-actions { display: flex; gap: 8px; }
    .bc-btn {
        flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 9px 14px; border-radius: 9px; font-size: 13px; font-weight: 600;
        cursor: pointer; border: none; font-family: inherit;
        transition: opacity .15s, transform .15s; text-decoration: none;
    }
    .bc-btn:hover { transform: translateY(-1px); opacity: .9; }
    .bc-btn:active { transform: translateY(0); }
    .bc-btn--ghost { background: #f1f5f9; color: #374151; border: 1px solid #e2e8f0; }
    .dark .bc-btn--ghost { background: #374151; color: #d1d5db; border-color: #4b5563; }
    .bc-btn--ghost:hover { background: #e2e8f0; }
    .dark .bc-btn--ghost:hover { background: #4b5563; }
    .bc-btn--primary { color: #ffffff; box-shadow: 0 3px 10px rgba(0,0,0,.2); }
</style>
