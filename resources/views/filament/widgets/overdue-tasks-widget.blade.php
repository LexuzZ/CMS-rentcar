<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="display:flex; align-items:center; justify-content:center;
                            width:36px; height:36px; border-radius:10px;
                            background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.25);">
                    <x-heroicon-s-exclamation-triangle style="width:16px;height:16px;color:#ef4444;" />
                </div>
                <div style="line-height:1.3;">
                    <p style="margin:0; font-size:14px; font-weight:600; color:inherit;">Tugas Terlambat</p>
                    <p style="margin:0; font-size:11px; color:#9ca3af; font-weight:400; margin-top:2px;">
                        Booking melewati jadwal · perlu tindakan segera
                    </p>
                </div>
                <div style="margin-left:auto; display:flex; align-items:center; gap:6px;
                            padding:4px 10px; border-radius:8px;
                            background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.2);">
                    <span style="width:6px;height:6px;border-radius:50%;background:#ef4444;
                                 display:inline-block; animation:pulse 1.5s infinite;"></span>
                    <span style="font-size:12px;font-weight:600;color:#ef4444;">
                        {{ $overduePickups->count() + $overdueReturns->count() }} aktif
                    </span>
                </div>
            </div>
        </x-slot>

        <style>
            @keyframes pulse {
                0%,100% { opacity:1; }
                50%      { opacity:.35; }
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
                width: 24px;
                height: 24px;
                border-radius: 6px;
                flex-shrink: 0;
            }
            .ow-col-icon.warning {
                background: rgba(245,158,11,.1);
                border: 1px solid rgba(245,158,11,.25);
            }
            .ow-col-icon.danger {
                background: rgba(239,68,68,.1);
                border: 1px solid rgba(239,68,68,.25);
            }
            .ow-col-title {
                font-size: 11px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .07em;
                color: #9ca3af;
                margin: 0;
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
            .ow-badge.warning {
                background: rgba(245,158,11,.08);
                border: 1px solid rgba(245,158,11,.2);
                color: #d97706;
            }
            .ow-badge.danger {
                background: rgba(239,68,68,.08);
                border: 1px solid rgba(239,68,68,.2);
                color: #ef4444;
            }
            .ow-badge b { font-weight: 700; }
            .ow-cards { display: flex; flex-direction: column; gap: 8px; }
            .ow-card {
                position: relative;
                background: #fff;
                border: 1px solid #f0f0f0;
                border-radius: 12px;
                overflow: hidden;
                transition: border-color .15s, box-shadow .15s;
            }
            .ow-card:hover {
                box-shadow: 0 2px 10px rgba(0,0,0,.06);
            }
            .ow-card.warning:hover { border-color: rgba(245,158,11,.35); }
            .ow-card.danger:hover  { border-color: rgba(239,68,68,.35); }

            .ow-card-bar {
                position: absolute;
                left: 0; top: 0; bottom: 0;
                width: 3px;
                border-radius: 12px 0 0 12px;
            }
            .ow-card-bar.warning { background: #f59e0b; }
            .ow-card-bar.danger  { background: #ef4444; }

            .ow-card-body {
                padding: 14px 16px 12px 18px;
            }
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
                color: inherit;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .ow-nopol {
                display: inline-block;
                font-family: ui-monospace, monospace;
                font-size: 10px;
                font-weight: 500;
                background: #f3f4f6;
                color: #6b7280;
                border: 1px solid #e5e7eb;
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
                color: #9ca3af;
            }
            .ow-time-right { text-align: right; flex-shrink: 0; }
            .ow-time-rel {
                margin: 0;
                font-size: 12px;
                font-weight: 700;
            }
            .ow-time-rel.warning { color: #d97706; }
            .ow-time-rel.danger  { color: #ef4444; }
            .ow-time-abs {
                margin: 0;
                font-size: 11px;
                color: #d1d5db;
                margin-top: 2px;
                font-variant-numeric: tabular-nums;
            }
            .ow-divider {
                border: none;
                border-top: 1px solid #f3f4f6;
                margin: 10px 0 10px;
            }
            .ow-action-row {
                display: flex;
                justify-content: flex-end;
            }
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
                transition: background .15s, transform .1s;
                color: #fff;
            }
            .ow-btn:active { transform: scale(.96); }
            .ow-btn.warning { background: #f59e0b; }
            .ow-btn.warning:hover { background: #d97706; }
            .ow-btn.danger  { background: #ef4444; }
            .ow-btn.danger:hover  { background: #dc2626; }

            .ow-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 36px 16px;
                border: 1.5px dashed #e5e7eb;
                border-radius: 12px;
                background: #fafafa;
                text-align: center;
            }
            .ow-empty-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: rgba(34,197,94,.1);
                margin-bottom: 10px;
            }
            .ow-empty p { margin: 0; }
            .ow-empty-title { font-size: 13px; font-weight: 500; color: #9ca3af; }
            .ow-empty-sub   { font-size: 11px; color: #d1d5db; margin-top: 3px !important; }

            /* Dark mode */
            @media (prefers-color-scheme: dark) {
                .ow-card          { background: rgba(17,24,39,.8); border-color: #1f2937; }
                .ow-card.warning:hover { border-color: rgba(245,158,11,.3); }
                .ow-card.danger:hover  { border-color: rgba(239,68,68,.3); }
                .ow-nopol         { background: #1f2937; color: #9ca3af; border-color: #374151; }
                .ow-divider       { border-color: #1f2937; }
                .ow-empty         { background: rgba(17,24,39,.3); border-color: #1f2937; }
                .ow-time-abs      { color: #6b7280; }
            }
        </style>

        <div class="ow-grid">

            {{-- ══ Terlambat Pick Up ══ --}}
            <div>
                <div class="ow-col-header">
                    <div class="ow-col-icon warning">
                        <x-heroicon-o-truck style="width:14px;height:14px;color:#f59e0b;" />
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
                                <x-heroicon-o-check-circle style="width:20px;height:20px;color:#22c55e;" />
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
                        <x-heroicon-o-arrow-path style="width:14px;height:14px;color:#ef4444;" />
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
                                <x-heroicon-o-check-circle style="width:20px;height:20px;color:#22c55e;" />
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
