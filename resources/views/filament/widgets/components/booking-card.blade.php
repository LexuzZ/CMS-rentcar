@php
    $isDanger  = ($theme ?? 'default') === 'danger';
    $isInfo    = ($theme ?? 'default') === 'info';

    $accentColor   = $isDanger ? '#16a34a' : ($isInfo ? '#0369a1' : '#6b7280');
    $accentLight   = $isDanger ? '#f0fdf4' : ($isInfo ? '#f0f9ff' : '#f9fafb');
    $accentBorder  = $isDanger ? '#bbf7d0' : ($isInfo ? '#bae6fd' : '#e5e7eb');
    $accentText    = $isDanger ? '#15803d' : ($isInfo ? '#0369a1' : '#374151');
    $buttonHover   = $isDanger ? '#15803d' : ($isInfo ? '#075985' : '#4b5563');

    $status     = $record->status;
    $statusText = match ($status) {
        'booking' => 'Booking',
        default   => ucfirst($status),
    };
@endphp

<div class="bc-card">

    {{-- Top accent bar --}}
    <div class="bc-accent-bar" style="background: {{ $accentColor }};"></div>

    <div class="bc-inner">

        {{-- ===== HEADER ===== --}}
        <div class="bc-header">
            <div class="bc-car-icon" style="background: {{ $accentLight }}; border-color: {{ $accentBorder }}; color: {{ $accentColor }};">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                    <circle cx="7.5" cy="17.5" r="1.5"/>
                    <circle cx="16.5" cy="17.5" r="1.5"/>
                </svg>
            </div>

            <div class="bc-car-info">
                <p class="bc-car-name">
                    {{ $record->car->carModel->brand->name }} {{ $record->car->carModel->name }}
                </p>
                <p class="bc-car-nopol">{{ $record->car->nopol }}</p>
            </div>

            <span class="bc-badge" style="background: {{ $accentLight }}; color: {{ $accentText }}; border-color: {{ $accentBorder }};">
                {{ $statusText }}
            </span>
        </div>

        {{-- ===== ROWS ===== --}}
        <div class="bc-rows">

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </span>
                <span class="bc-row-label">Penyewa</span>
                <span class="bc-row-value">{{ $record->customer->nama ?? '—' }}</span>
            </div>

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </span>
                <span class="bc-row-label">No. Telepon</span>
                <span class="bc-row-value">{{ $record->customer->no_telp ?? '—' }}</span>
            </div>

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/>
                    </svg>
                </span>
                <span class="bc-row-label">Drop Point</span>
                <span class="bc-row-value">{{ $record->lokasi_pengantaran ?? '—' }}</span>
            </div>

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3h2a2 2 0 0 1 2 2v2M8 3H6a2 2 0 0 0-2 2v2"/>
                    </svg>
                </span>
                <span class="bc-row-label">Vendor</span>
                <span class="bc-row-value">{{ $record->car->garasi ?? '—' }}</span>
            </div>

            <div class="bc-row">
                <span class="bc-row-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>
                <span class="bc-row-label">Staff</span>
                <span class="bc-row-value bc-row-value--staff">{{ $record->driverPengantaran->nama ?? '—' }}</span>
            </div>

        </div>

        {{-- ===== TIME BLOCK ===== --}}
        <div class="bc-time-block" style="background: {{ $accentLight }}; border-color: {{ $accentBorder }};">
            <div class="bc-time-item">
                <span class="bc-time-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Waktu Keluar
                </span>
                <span class="bc-time-value" style="color: {{ $accentColor }};">
                    {{ \Carbon\Carbon::parse($record->waktu_keluar)->locale('id')->format('H:i') }} WITA
                </span>
            </div>
            <div class="bc-time-divider"></div>
            <div class="bc-time-item">
                <span class="bc-time-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Tanggal Keluar
                </span>
                <span class="bc-time-value" style="color: {{ $accentColor }};">
                    {{ \Carbon\Carbon::parse($record->tanggal_keluar)->locale('id')->isoFormat('ddd, D MMM Y') }}
                </span>
            </div>
        </div>

        {{-- ===== ACTIONS ===== --}}
        @if ($canPerformActions)
        <div class="bc-actions">
            <a href="{{ \App\Filament\Resources\BookingResource::getUrl('edit', ['record' => $record->id]) }}"
               class="bc-btn bc-btn--ghost">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit
            </a>

            <button wire:click="pickupBooking({{ $record->id }})" wire:loading.attr="disabled"
                class="bc-btn bc-btn--primary"
                style="background: {{ $accentColor }};"
                onmouseover="this.style.background='{{ $buttonHover }}'"
                onmouseout="this.style.background='{{ $accentColor }}'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 8 16 12 12 16"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                Pick Up
            </button>
        </div>
        @endif

    </div>{{-- /bc-inner --}}
</div>{{-- /bc-card --}}

<style>
    .bc-card {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        overflow: hidden;
        transition: box-shadow 0.18s, transform 0.18s;
    }
    .dark .bc-card { background: #1f2937; border-color: #374151; }
    .bc-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.08); transform: translateY(-1px); }

    .bc-accent-bar { height: 3px; width: 100%; }

    .bc-inner { padding: 16px; display: flex; flex-direction: column; gap: 14px; }

    /* Header */
    .bc-header { display: flex; align-items: center; gap: 10px; }

    .bc-car-icon {
        width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
        border: 1px solid;
        display: flex; align-items: center; justify-content: center;
    }

    .bc-car-info { flex: 1; min-width: 0; }
    .bc-car-name {
        font-size: 13.5px; font-weight: 700; color: #0f172a;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .dark .bc-car-name { color: #f1f5f9; }
    .bc-car-nopol {
        font-size: 11px; font-weight: 600; color: #6b7280;
        font-family: ui-monospace, monospace; letter-spacing: 0.05em;
        margin-top: 1px;
    }
    .dark .bc-car-nopol { color: #9ca3af; }

    .bc-badge {
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 100px;
        border: 1px solid; white-space: nowrap; flex-shrink: 0;
    }

    /* Rows */
    .bc-rows {
        display: flex; flex-direction: column; gap: 0;
        border: 1px solid #f1f5f9; border-radius: 10px; overflow: hidden;
    }
    .dark .bc-rows { border-color: #374151; }

    .bc-row {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 12px;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.12s;
    }
    .dark .bc-row { border-color: #374151; }
    .bc-row:last-child { border-bottom: none; }
    .bc-row:hover { background: #f8fafc; }
    .dark .bc-row:hover { background: #111827; }

    .bc-row-icon { color: #9ca3af; flex-shrink: 0; display: flex; }
    .bc-row-label { font-size: 11.5px; color: #9ca3af; flex: 1; }
    .dark .bc-row-label { color: #6b7280; }
    .bc-row-value {
        font-size: 12px; font-weight: 600; color: #111827; text-align: right;
        max-width: 55%; word-break: break-word; line-height: 1.4;
    }
    .dark .bc-row-value { color: #e5e7eb; }
    .bc-row-value--staff {
        display: inline-flex; align-items: center;
        background: #f0fdf4; color: #15803d;
        padding: 2px 8px; border-radius: 100px;
        font-size: 11px; border: 1px solid #bbf7d0;
    }
    .dark .bc-row-value--staff { background: #052e16; color: #4ade80; border-color: #14532d; }

    /* Time block */
    .bc-time-block {
        display: flex; align-items: stretch;
        border: 1px solid; border-radius: 10px; overflow: hidden;
    }
    .bc-time-item {
        flex: 1; padding: 10px 12px;
        display: flex; flex-direction: column; gap: 4px;
    }
    .bc-time-label {
        display: flex; align-items: center; gap: 4px;
        font-size: 10.5px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .bc-time-value { font-size: 13px; font-weight: 700; }
    .bc-time-divider { width: 1px; background: currentColor; opacity: 0.15; align-self: stretch; }

    /* Actions */
    .bc-actions { display: flex; gap: 8px; }

    .bc-btn {
        flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 9px 14px; border-radius: 9px;
        font-size: 13px; font-weight: 600; cursor: pointer;
        border: none; font-family: inherit;
        transition: opacity 0.15s, transform 0.15s;
        text-decoration: none;
    }
    .bc-btn:hover { transform: translateY(-1px); opacity: 0.9; }
    .bc-btn:active { transform: translateY(0); }

    .bc-btn--ghost {
        background: #f1f5f9; color: #374151; border: 1px solid #e2e8f0;
    }
    .dark .bc-btn--ghost { background: #374151; color: #d1d5db; border-color: #4b5563; }
    .bc-btn--ghost:hover { background: #e2e8f0; }
    .dark .bc-btn--ghost:hover { background: #4b5563; }

    .bc-btn--primary { color: #ffffff; box-shadow: 0 3px 10px rgba(0,0,0,0.2); }
</style>
