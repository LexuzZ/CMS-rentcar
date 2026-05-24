<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <span class="flex items-center gap-2">
                <span class="od-heading-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </span>
                <span class="od-heading-text">Tugas Terlambat</span>
                @php $totalOverdue = $overduePickups->count() + $overdueReturns->count(); @endphp
                @if($totalOverdue > 0)
                    <span class="od-heading-count">{{ $totalOverdue }}</span>
                @endif
            </span>
        </x-slot>

        <div class="od-grid">

            {{-- ===== PICKUP ===== --}}
            <div class="od-col">
                <div class="od-col-header od-col-header--pickup">
                    <span class="od-col-dot"></span>
                    <span class="od-col-title">Terlambat Pick Up</span>
                    <span class="od-col-badge">{{ $overduePickups->count() }}</span>
                </div>

                <div class="od-list">
                    @forelse ($overduePickups as $booking)
                    <div class="od-item od-item--pickup">
                        <div class="od-item-top">
                            <div class="od-item-icon od-item-icon--pickup">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 17H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h10l4 4v4a2 2 0 0 1-2 2z"/>
                                    <circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/>
                                </svg>
                            </div>
                            <div class="od-item-info">
                                <p class="od-item-car">{{ $booking->car->carModel->name }}
                                    <span class="od-item-nopol">{{ $booking->car->nopol }}</span>
                                </p>
                                <p class="od-item-customer">{{ $booking->customer->nama }}</p>
                            </div>
                            <div class="od-item-time">
                                <p class="od-item-diff">
                                    {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->diffForHumans() }}
                                </p>
                                <p class="od-item-date">
                                    {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        @if($canPerformActions)
                        <div class="od-item-footer">
                            <button wire:click="pickupOverdue({{ $booking->id }})" class="od-btn od-btn--pickup">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 8 16 12 12 16"/>
                                    <line x1="8" y1="12" x2="16" y2="12"/>
                                </svg>
                                Pick Up Sekarang
                            </button>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="od-empty">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Tidak ada pick up terlewat</span>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- ===== RETURN ===== --}}
            <div class="od-col">
                <div class="od-col-header od-col-header--return">
                    <span class="od-col-dot od-col-dot--return"></span>
                    <span class="od-col-title">Terlambat Selesaikan</span>
                    <span class="od-col-badge od-col-badge--return">{{ $overdueReturns->count() }}</span>
                </div>

                <div class="od-list">
                    @forelse ($overdueReturns as $booking)
                    <div class="od-item od-item--return">
                        <div class="od-item-top">
                            <div class="od-item-icon od-item-icon--return">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="1 4 1 10 7 10"/>
                                    <path d="M3.51 15a9 9 0 1 0 .49-4.5"/>
                                    <polyline points="12 8 12 12 15 14"/>
                                </svg>
                            </div>
                            <div class="od-item-info">
                                <p class="od-item-car">{{ $booking->car->carModel->name }}
                                    <span class="od-item-nopol">{{ $booking->car->nopol }}</span>
                                </p>
                                <p class="od-item-customer">{{ $booking->customer->nama }}</p>
                            </div>
                            <div class="od-item-time">
                                <p class="od-item-diff od-item-diff--return">
                                    {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->diffForHumans() }}
                                </p>
                                <p class="od-item-date">
                                    {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                        @if($canPerformActions)
                        <div class="od-item-footer">
                            <button wire:click="returnOverdue({{ $booking->id }})" class="od-btn od-btn--return">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                Selesaikan
                            </button>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="od-empty">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Tidak ada pengembalian terlewat</span>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<style>
    /* Heading */
    .od-heading-icon {
        width: 26px; height: 26px; border-radius: 8px;
        background: #fee2e2; color: #dc2626;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .dark .od-heading-icon { background: #450a0a; color: #f87171; }

    .od-heading-text { font-weight: 700; color: #b91c1c; font-size: 15px; }
    .dark .od-heading-text { color: #f87171; }

    .od-heading-count {
        font-size: 11px; font-weight: 700;
        background: #ef4444; color: #fff;
        padding: 2px 7px; border-radius: 100px;
        animation: od-pulse 2s infinite;
    }
    @keyframes od-pulse {
        0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
        50%      { box-shadow: 0 0 0 5px rgba(239,68,68,0); }
    }

    /* Grid */
    .od-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }
    @media (min-width: 1024px) { .od-grid { grid-template-columns: 1fr 1fr; } }

    /* Column */
    .od-col { display: flex; flex-direction: column; gap: 10px; }

    .od-col-header {
        display: flex; align-items: center; gap: 8px;
        padding: 9px 14px;
        border-radius: 10px;
        font-size: 12px;
    }
    .od-col-header--pickup { background: #fff1f2; border: 1px solid #fecaca; }
    .od-col-header--return { background: #fff7ed; border: 1px solid #fed7aa; }
    .dark .od-col-header--pickup { background: #450a0a; border-color: #7f1d1d; }
    .dark .od-col-header--return { background: #431407; border-color: #7c2d12; }

    .od-col-dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        background: #ef4444;
    }
    .od-col-dot--return { background: #f97316; }

    .od-col-title { font-weight: 700; color: #374151; flex: 1; }
    .dark .od-col-title { color: #e5e7eb; }

    .od-col-badge {
        font-size: 11px; font-weight: 700;
        background: #ef4444; color: #fff;
        padding: 1px 8px; border-radius: 100px;
    }
    .od-col-badge--return { background: #f97316; }

    /* List */
    .od-list { display: flex; flex-direction: column; gap: 8px; }

    /* Item */
    .od-item {
        border-radius: 12px; border: 1px solid;
        overflow: hidden;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .od-item:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,0.07); }

    .od-item--pickup { border-color: #fecaca; background: #fff; }
    .od-item--return { border-color: #fed7aa; background: #fff; }
    .dark .od-item--pickup { background: #1f2937; border-color: #7f1d1d; }
    .dark .od-item--return { background: #1f2937; border-color: #7c2d12; }

    .od-item-top {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 14px;
    }

    .od-item-icon {
        width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .od-item-icon--pickup { background: #fee2e2; color: #dc2626; }
    .od-item-icon--return { background: #ffedd5; color: #ea580c; }
    .dark .od-item-icon--pickup { background: #450a0a; color: #f87171; }
    .dark .od-item-icon--return { background: #431407; color: #fb923c; }

    .od-item-info { flex: 1; min-width: 0; }
    .od-item-car {
        font-size: 13px; font-weight: 700; color: #111827;
        display: flex; align-items: center; gap: 6px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .dark .od-item-car { color: #f3f4f6; }
    .od-item-nopol {
        font-size: 10px; font-weight: 600; color: #6b7280;
        font-family: ui-monospace, monospace;
        background: #f3f4f6; padding: 1px 6px; border-radius: 4px;
    }
    .dark .od-item-nopol { background: #374151; color: #9ca3af; }

    .od-item-customer { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .dark .od-item-customer { color: #9ca3af; }

    .od-item-time { text-align: right; flex-shrink: 0; }
    .od-item-diff {
        font-size: 12px; font-weight: 700; color: #dc2626;
        white-space: nowrap;
    }
    .od-item-diff--return { color: #ea580c; }
    .dark .od-item-diff { color: #f87171; }
    .dark .od-item-diff--return { color: #fb923c; }

    .od-item-date { font-size: 11px; color: #9ca3af; margin-top: 2px; }

    /* Footer */
    .od-item-footer {
        padding: 0 14px 12px;
        display: flex; justify-content: flex-end;
    }

    .od-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px; border-radius: 8px; border: none;
        font-size: 12px; font-weight: 700; cursor: pointer; font-family: inherit;
        transition: opacity 0.15s, transform 0.15s;
        color: #fff;
    }
    .od-btn:hover { opacity: 0.88; transform: translateY(-1px); }
    .od-btn:active { transform: translateY(0); }
    .od-btn--pickup { background: #ef4444; box-shadow: 0 3px 8px rgba(239,68,68,0.3); }
    .od-btn--return { background: #f97316; box-shadow: 0 3px 8px rgba(249,115,22,0.3); }

    /* Empty */
    .od-empty {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        padding: 20px; border-radius: 10px;
        border: 1.5px dashed #e5e7eb;
        font-size: 13px; color: #9ca3af;
        background: #fafafa;
    }
    .dark .od-empty { background: #111827; border-color: #374151; }
</style>
