<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="display:flex; align-items:center; justify-content:center;
                            width:36px; height:36px; border-radius:10px;
                            background:#fef2f2; border:1px solid #fecaca;">
                    <x-heroicon-s-exclamation-triangle style="width:16px;height:16px;color:#dc2626;" />
                </div>
                <div style="line-height:1.3;">
                    <p style="margin:0; font-size:14px; font-weight:600; color:inherit;">Tugas Terlambat</p>
                    <p style="margin:0; font-size:11px; color:#a8a29e; font-weight:400; margin-top:2px;">
                        Booking melewati jadwal · perlu tindakan segera
                    </p>
                </div>
                <div style="margin-left:auto; display:flex; align-items:center; gap:6px;
                            padding:4px 10px; border-radius:8px;
                            background:#fef2f2; border:1px solid #fecaca;">
                    <span style="width:6px;height:6px;border-radius:50%;background:#dc2626;
                                 display:inline-block; animation:ow-pulse 1.5s infinite;"></span>
                    <span style="font-size:12px;font-weight:600;color:#dc2626;">
                        {{ $overduePickups->count() + $overdueReturns->count() }} aktif
                    </span>
                </div>
            </div>
        </x-slot>

        <style>
            @keyframes ow-pulse {
                0%,100% { opacity:1; }
                50%      { opacity:.3; }
            }
            .ow-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
                margin-top: 4px;
            }
            @media (max-width: 768px) {
                .ow-grid { grid-template-columns: 1fr; }
            }

            /* ── Column header ── */
            .ow-col-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 10px;
                padding: 0 2px;
            }
            .ow-col-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 24px; height: 24px;
                border-radius: 6px;
                flex-shrink: 0;
            }
            .ow-col-icon.warning { background:#fffbeb; border:1px solid #fde68a; }
            .ow-col-icon.danger  { background:#fef2f2; border:1px solid #fecaca; }

            .ow-col-title {
                margin: 0;
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .07em;
                color: #a8a29e;
            }
            .ow-badge {
                margin-left: auto;
                display: flex;
                align-items: center;
                gap: 4px;
                padding: 2px 8px;
                border-radius: 6px;
                font-size: 11px;
            }
            .ow-badge.warning { background:#fffbeb; border:1px solid #fde68a; color:#92400e; }
            .ow-badge.danger  { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
            .ow-badge b { font-weight: 700; }

            /* ── Cards ── */
            .ow-cards { display:flex; flex-direction:column; gap:8px; }

            .ow-card {
                position: relative;
                background: #faf9f7;
                border-radius: 12px;
                overflow: hidden;
                transition: box-shadow .15s;
            }
            .ow-card.warning { border:1px solid #fde68a; }
            .ow-card.danger  { border:1px solid #fecaca; }
            .ow-card.warning:hover { box-shadow: 0 2px 12px rgba(180,83,9,.1); }
            .ow-card.danger:hover  { box-shadow: 0 2px 12px rgba(185,28,28,.1); }

            .ow-card-bar {
                position: absolute;
                left:0; top:0; bottom:0;
                width: 3px;
                border-radius: 12px 0 0 12px;
            }
            .ow-card-bar.warning { background:#f59e0b; }
            .ow-card-bar.danger  { background:#ef4444; }

            .ow-card-body { padding:14px 16px 12px 18px; }

            .ow-card-row {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 12px;
            }
            .ow-car-name {
                margin: 0;
                font-size: 13.5px;
                font-weight: 600;
                color: #1c1917;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .ow-nopol {
                display: inline-block;
                font-family: ui-monospace, monospace;
                font-size: 10px;
                font-weight: 500;
                background: #f5f4f2;
                color: #78716c;
                border: 1px solid #e7e5e4;
                border-radius: 4px;
                padding: 1px 5px;
                letter-spacing: .05em;
                margin-left: 4px;
                vertical-align: middle;
            }
            .ow-customer {
                display: flex;
                align-items: center;
                gap: 4px;
                margin-top: 5px;
                font-size: 12px;
                color: #a8a29e;
            }
            .ow-time-right { text-align:right; flex-shrink:0; }
            .ow-time-rel { margin:0; font-size:12px; font-weight:700; }
            .ow-time-rel.warning { color:#92400e; }
            .ow-time-rel.danger  { color:#991b1b; }
            .ow-time-abs {
                margin: 2px 0 0;
                font-size: 11px;
                color: #c4bfbb;
                font-variant-numeric: tabular-nums;
            }

            .ow-divider {
                border: none;
                border-top: 1px solid #ede9e5;
                margin: 10px 0;
            }
            .ow-action-row { display:flex; justify-content:flex-end; }
            .ow-btn {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 5px 12px;
                border: none;
                border-radius: 7px;
                font-size: 12px;
                font-weight: 600;
                cursor: pointer;
                color: #fff;
                transition: filter .15s, transform .1s;
            }
            .ow-btn:active { transform: scale(.96); }
            .ow-btn.warning { background:#d97706; }
            .ow-btn.warning:hover { filter: brightness(.92); }
            .ow-btn.danger  { background:#dc2626; }
            .ow-btn.danger:hover  { filter: brightness(.92); }

            /* ── Empty state ── */
            .ow-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 36px 16px;
                border: 1.5px dashed #e7e5e4;
                border-radius: 12px;
                background: #faf9f7;
                text-align: center;
            }
            .ow-empty-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px; height: 40px;
                border-radius: 50%;
                background: #f0fdf4;
                border: 1px solid #bbf7d0;
                margin-bottom: 10px;
            }
            .ow-empty p { margin:0; }
            .ow-empty-title { font-size:13px; font-weight:500; color:#a8a29e; }
            .ow-empty-sub   { font-size:11px; color:#d6d3d1; margin-top:3px !important; }

            /* ── Dark mode ── */
            @media (prefers-color-scheme: dark) {
                .ow-card              { background:#1c1917; }
                .ow-card.warning      { border-color:#44321a; }
                .ow-card.danger       { border-color:#3f1d1d; }
                .ow-card.warning:hover{ box-shadow:0 2px 12px rgba(180,83,9,.2); }
                .ow-card.danger:hover { box-shadow:0 2px 12px rgba(185,28,28,.2); }
                .ow-car-name          { color:#fafaf9; }
                .ow-nopol             { background:#292524; color:#a8a29e; border-color:#44403c; }
                .ow-customer          { color:#78716c; }
                .ow-time-rel.warning  { color:#fbbf24; }
                .ow-time-rel.danger   { color:#f87171; }
                .ow-time-abs          { color:#57534e; }
                .ow-divider           { border-color:#292524; }
                .ow-col-title         { color:#78716c; }
                .ow-badge.warning     { background:#1c1007; border-color:#44321a; color:#fbbf24; }
                .ow-badge.danger      { background:#1a0a0a; border-color:#3f1d1d; color:#f87171; }
                .ow-col-icon.warning  { background:#1c1007; border-color:#44321a; }
                .ow-col-icon.danger   { background:#1a0a0a; border-color:#3f1d1d; }
                .ow-empty             { background:#1c1917; border-color:#292524; }
                .ow-empty-title       { color:#78716c; }
                .ow-empty-sub         { color:#44403c; }
                .ow-empty-icon        { background:#052e16; border-color:#14532d; }
            }
        </style>

        <div class="ow-grid">

            {{-- ══ Terlambat Pick Up ══ --}}
            <div>
                <div class="ow-col-header">
                    <div class="ow-col-icon warning">
                        <x-heroicon-o-truck style="width:14px;height:14px;color:#d97706;" />
                    </div>
                    <p class="ow-col-title">Pick Up</p>
                    <div class="ow-badge warning">
                        <b>{{ $overduePickups->count() }}</b> booking
                    </div>
                </div>

                <div class="ow-cards">
                    @forelse ($overduePickups as $booking)
                        <div class="ow-card warning">
                            <div class="ow-card-bar warning"></div>
                            <div class="ow-card-body">
                                <div class="ow-card-row">
                                    <div style="min-width:0;flex:1;">
                                        <p class="ow-car-name">
                                            {{ $booking->car->carModel->name }}
                                            <span class="ow-nopol">{{ $booking->car->nopol }}</span>
                                        </p>
                                        <p class="ow-customer">
                                            <x-heroicon-o-user style="width:11px;height:11px;flex-shrink:0;" />
                                            {{ $booking->customer->nama }}
                                        </p>
                                    </div>
                                    <div class="ow-time-right">
                                        <p class="ow-time-rel warning">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->diffForHumans() }}
                                        </p>
                                        <p class="ow-time-abs">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                @if($canPerformActions)
                                    <hr class="ow-divider">
                                    <div class="ow-action-row">
                                        <button wire:click="pickupOverdue({{ $booking->id }})" class="ow-btn warning">
                                            <x-heroicon-o-check style="width:13px;height:13px;" />
                                            Pick Up
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="ow-empty">
                            <div class="ow-empty-icon">
                                <x-heroicon-o-check-circle style="width:20px;height:20px;color:#16a34a;" />
                            </div>
                            <p class="ow-empty-title">Semua jadwal terpenuhi</p>
                            <p class="ow-empty-sub">Tidak ada pick up yang terlewat</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ══ Terlambat Selesaikan ══ --}}
            <div>
                <div class="ow-col-header">
                    <div class="ow-col-icon danger">
                        <x-heroicon-o-arrow-path style="width:14px;height:14px;color:#dc2626;" />
                    </div>
                    <p class="ow-col-title">Pengembalian</p>
                    <div class="ow-badge danger">
                        <b>{{ $overdueReturns->count() }}</b> booking
                    </div>
                </div>

                <div class="ow-cards">
                    @forelse ($overdueReturns as $booking)
                        <div class="ow-card danger">
                            <div class="ow-card-bar danger"></div>
                            <div class="ow-card-body">
                                <div class="ow-card-row">
                                    <div style="min-width:0;flex:1;">
                                        <p class="ow-car-name">
                                            {{ $booking->car->carModel->name }}
                                            <span class="ow-nopol">{{ $booking->car->nopol }}</span>
                                        </p>
                                        <p class="ow-customer">
                                            <x-heroicon-o-user style="width:11px;height:11px;flex-shrink:0;" />
                                            {{ $booking->customer->nama }}
                                        </p>
                                    </div>
                                    <div class="ow-time-right">
                                        <p class="ow-time-rel danger">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->diffForHumans() }}
                                        </p>
                                        <p class="ow-time-abs">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                @if($canPerformActions)
                                    <hr class="ow-divider">
                                    <div class="ow-action-row">
                                        <button wire:click="returnOverdue({{ $booking->id }})" class="ow-btn danger">
                                            <x-heroicon-o-check style="width:13px;height:13px;" />
                                            Selesaikan
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="ow-empty">
                            <div class="ow-empty-icon">
                                <x-heroicon-o-check-circle style="width:20px;height:20px;color:#16a34a;" />
                            </div>
                            <p class="ow-empty-title">Semua kendaraan kembali</p>
                            <p class="ow-empty-sub">Tidak ada pengembalian yang terlewat</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
