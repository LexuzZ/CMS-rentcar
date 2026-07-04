<div class="ms-card {{ $type }}">
    <div class="ms-card-bar {{ $type }}"></div>
    <div class="ms-card-body">
        <div class="ms-card-row">
            <div style="min-width:0; flex:1;">
                <p class="ms-car-name">
                    {{ $booking->car->carModel->name ?? '—' }}
                    <span class="ms-nopol">{{ $booking->car->nopol ?? '—' }}</span>
                </p>
                <p class="ms-customer">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    {{ $booking->customer->nama ?? '—' }}
                    @if($booking->driver)
                        &nbsp;·&nbsp;
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="10" r="3"/>
                            <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/>
                        </svg>
                        {{ $booking->driver->nama ?? '' }}
                    @endif
                </p>
            </div>
            <div class="ms-time-right">
                <p class="ms-time-main {{ $type }}">
                    {{ $booking->$timeField ? \Carbon\Carbon::parse($booking->$timeField)->format('H:i') : '—' }}
                </p>
                <p class="ms-time-sub">
                    {{ \Carbon\Carbon::parse($booking->$dateField)->locale('id')->format('d M Y') }}
                </p>
            </div>
        </div>

        @if($canPerformActions)
            <hr class="ms-divider">
            <div class="ms-actions">
                <button wire:click="editBooking({{ $booking->id }})" class="ms-btn edit">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </button>

                @if($type === 'keluar')
                    <button wire:click="pickupBooking({{ $booking->id }})" class="ms-btn pickup">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Pick Up
                    </button>
                @else
                    <button wire:click="selesaikanBooking({{ $booking->id }})" class="ms-btn selesai">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Selesaikan
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
