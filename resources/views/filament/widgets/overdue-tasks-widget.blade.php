<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="display:flex; align-items:center; justify-content:center;
                            width:36px; height:36px; border-radius:10px;
                            background:linear-gradient(135deg,#fef2f2,#fee2e2);
                            border:1px solid #fecaca;
                            box-shadow:0 2px 8px rgba(220,38,38,.15);">
                    <x-heroicon-s-exclamation-triangle style="width:16px;height:16px;color:#dc2626;" />
                </div>
                <div style="line-height:1.3;">
                    <p style="margin:0; font-size:14px; font-weight:700; color:inherit;">Tugas Terlambat</p>
                    <p style="margin:0; font-size:11px; color:#a8a29e; font-weight:400; margin-top:2px;">
                        Booking melewati jadwal · perlu tindakan segera
                    </p>
                </div>
                <div style="margin-left:auto; display:flex; align-items:center; gap:6px;
                            padding:5px 12px; border-radius:8px;
                            background:linear-gradient(135deg,#fef2f2,#fee2e2);
                            border:1px solid #fecaca;
                            box-shadow:0 1px 4px rgba(220,38,38,.12);">
                    <span style="width:7px;height:7px;border-radius:50%;background:#dc2626;
                                 display:inline-block; animation:ow-pulse 1.5s infinite;"></span>
                    <span style="font-size:12px;font-weight:700;color:#dc2626;">
                        {{ $overduePickups->count() + $overdueReturns->count() }} aktif
                    </span>
                </div>
            </div>
        </x-slot>

        <style>
            @keyframes ow-pulse {
                0%,100% { opacity:1; transform:scale(1); }
                50%      { opacity:.4; transform:scale(.8); }
            }

            .ow-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-top: 4px;
            }
            @media (max-width: 768px) { .ow-grid { grid-template-columns: 1fr; } }

            /* ── Column header ── */
            .ow-col-header {
                display: flex; align-items: center; gap: 8px;
                margin-bottom: 10px;
                padding: 10px 14px;
                border-radius: 10px;
            }
            .ow-col-header.warning {
                background: linear-gradient(135deg, #fffbeb, #fef3c7);
                border: 1px solid #fde68a;
            }
            .ow-col-header.danger {
                background: linear-gradient(135deg, #fff1f2, #fee2e2);
                border: 1px solid #fecaca;
            }

            .ow-col-icon {
                display: flex; align-items: center; justify-content: center;
                width: 26px; height: 26px; border-radius: 7px; flex-shrink: 0;
            }
            .ow-col-icon.warning { background:#fff7ed; border:1px solid #fed7aa; }
            .ow-col-icon.danger  { background:#fef2f2; border:1px solid #fecaca; }

            .ow-col-title {
                margin: 0; font-size: 11px; font-weight: 700;
                text-transform: uppercase; letter-spacing: .07em;
            }
            .ow-col-header.warning .ow-col-title { color:#92400e; }
            .ow-col-header.danger  .ow-col-title { color:#991b1b; }

            .ow-badge {
                margin-left: auto; display: flex; align-items: center; gap: 4px;
                padding: 3px 9px; border-radius: 100px; font-size: 11px; font-weight: 700;
            }
            .ow-badge.warning { background:#fff7ed; border:1px solid #fed7aa; color:#92400e; }
            .ow-badge.danger  { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

            /* ── Cards ── */
            .ow-cards { display:flex; flex-direction:column; gap:8px; }

            .ow-card {
                position: relative;
                border-radius: 12px; overflow: hidden;
                transition: box-shadow .18s, transform .18s;
            }
            .ow-card.warning {
                background: #ffffff;
                border: 1px solid #fde68a;
                box-shadow: 0 1px 4px rgba(245,158,11,.08), inset 0 0 0 0 transparent;
            }
            .ow-card.danger {
                background: #ffffff;
                border: 1px solid #fecaca;
                box-shadow: 0 1px 4px rgba(239,68,68,.08);
            }
            .ow-card.warning:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(245,158,11,.15);
            }
            .ow-card.danger:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(239,68,68,.15);
            }

            /* Left accent bar */
            .ow-card-bar {
                position: absolute; left:0; top:0; bottom:0; width:4px;
            }
            .ow-card-bar.warning { background: linear-gradient(180deg,#f59e0b,#d97706); }
            .ow-card-bar.danger  { background: linear-gradient(180deg,#f87171,#dc2626); }

            /* Tinted strip behind bar for depth */
            .ow-card::before {
                content:''; position:absolute; left:0; top:0; bottom:0; width:48px;
                pointer-events:none;
            }
            .ow-card.warning::before { background: linear-gradient(90deg, rgba(251,191,36,.06), transparent); }
            .ow-card.danger::before  { background: linear-gradient(90deg, rgba(248,113,113,.06), transparent); }

            .ow-card-body { padding:14px 16px 12px 20px; }

            .ow-card-row {
                display: flex; justify-content: space-between;
                align-items: flex-start; gap: 12px;
            }

            .ow-car-name {
                margin: 0; font-size: 13.5px; font-weight: 700; color: #1c1917;
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            }
            .ow-nopol {
                display: inline-block;
                font-family: ui-monospace, monospace;
                font-size: 10px; font-weight: 600;
                background: #f5f5f4; color: #57534e;
                border: 1px solid #e7e5e4; border-radius: 4px;
                padding: 1px 6px; letter-spacing: .05em;
                margin-left: 5px; vertical-align: middle;
            }
            .ow-customer {
                display: flex; align-items: center; gap: 4px;
                margin-top: 5px; font-size: 12px; color: #a8a29e;
            }

            .ow-time-right { text-align:right; flex-shrink:0; }
            .ow-time-rel   { margin:0; font-size:12px; font-weight:700; }
            .ow-time-rel.warning { color:#b45309; }
            .ow-time-rel.danger  { color:#dc2626; }
            .ow-time-abs {
                margin:2px 0 0; font-size:11px; color:#c4bfbb;
                font-variant-numeric:tabular-nums;
            }

            .ow-divider {
                border:none; border-top:1px dashed #f5f5f4; margin:10px 0;
            }
            .ow-card.warning .ow-divider { border-color:#fef3c7; }
            .ow-card.danger  .ow-divider { border-color:#fee2e2; }

            .ow-action-row { display:flex; justify-content:flex-end; }
            .ow-btn {
                display: inline-flex; align-items: center; gap: 5px;
                padding: 6px 14px; border:none; border-radius: 8px;
                font-size: 12px; font-weight: 700;
                cursor: pointer; color: #fff;
                transition: filter .15s, transform .1s, box-shadow .15s;
            }
            .ow-btn:active { transform: scale(.96); }
            .ow-btn.warning {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                box-shadow: 0 3px 10px rgba(217,119,6,.35);
            }
            .ow-btn.warning:hover { filter:brightness(.92); box-shadow:0 5px 14px rgba(217,119,6,.4); }
            .ow-btn.danger {
                background: linear-gradient(135deg, #f87171, #dc2626);
                box-shadow: 0 3px 10px rgba(220,38,38,.35);
            }
            .ow-btn.danger:hover { filter:brightness(.92); box-shadow:0 5px 14px rgba(220,38,38,.4); }

            /* ── Empty ── */
            .ow-empty {
                display: flex; flex-direction: column; align-items: center;
                justify-content: center; padding: 36px 16px;
                border: 1.5px dashed #d1fae5; border-radius: 12px;
                background: linear-gradient(135deg, #f0fdf4, #dcfce7);
                text-align: center;
            }
            .ow-empty-icon {
                display: flex; align-items: center; justify-content: center;
                width: 44px; height: 44px; border-radius: 50%;
                background: #fff; border: 1px solid #bbf7d0;
                box-shadow: 0 2px 8px rgba(16,185,129,.15);
                margin-bottom: 10px;
            }
            .ow-empty p { margin:0; }
            .ow-empty-title { font-size:13px; font-weight:600; color:#15803d; }
            .ow-empty-sub   { font-size:11px; color:#86efac; margin-top:3px !important; }

            /* ── Dark mode ── */
            .dark .ow-card              { background:#1c1917; }
            .dark .ow-card.warning      { border-color:#44321a; box-shadow:none; }
            .dark .ow-card.danger       { border-color:#3f1d1d; box-shadow:none; }
            .dark .ow-card.warning:hover{ box-shadow:0 4px 16px rgba(180,83,9,.2); }
            .dark .ow-card.danger:hover { box-shadow:0 4px 16px rgba(185,28,28,.2); }
            .dark .ow-car-name          { color:#fafaf9; }
            .dark .ow-nopol             { background:#292524; color:#a8a29e; border-color:#44403c; }
            .dark .ow-customer          { color:#78716c; }
            .dark .ow-time-rel.warning  { color:#fbbf24; }
            .dark .ow-time-rel.danger   { color:#f87171; }
            .dark .ow-time-abs          { color:#57534e; }
            .dark .ow-divider           { border-color:#292524; }
            .dark .ow-col-header.warning{ background:#1c1007; border-color:#44321a; }
            .dark .ow-col-header.danger { background:#1a0a0a; border-color:#3f1d1d; }
            .dark .ow-col-header.warning .ow-col-title { color:#fbbf24; }
            .dark .ow-col-header.danger  .ow-col-title { color:#f87171; }
            .dark .ow-badge.warning     { background:#1c1007; border-color:#44321a; color:#fbbf24; }
            .dark .ow-badge.danger      { background:#1a0a0a; border-color:#3f1d1d; color:#f87171; }
            .dark .ow-col-icon.warning  { background:#1c1007; border-color:#44321a; }
            .dark .ow-col-icon.danger   { background:#1a0a0a; border-color:#3f1d1d; }
            .dark .ow-empty             { background:#052e16; border-color:#14532d; }
            .dark .ow-empty-title       { color:#4ade80; }
            .dark .ow-empty-sub         { color:#166534; }
            .dark .ow-empty-icon        { background:#0a3d1f; border-color:#14532d; box-shadow:none; }
        </style>

        <div class="ow-grid">

            {{-- ══ Terlambat Pick Up ══ --}}
            <div>
                <div class="ow-col-header warning">
                    <div class="ow-col-icon warning">
                        <x-heroicon-o-truck style="width:14px;height:14px;color:#d97706;" />
                    </div>
                    <p class="ow-col-title">Pick Up</p>
                    <div class="ow-badge warning">
                        {{ $overduePickups->count() }} booking
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
                                            {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->format('d M Y') }}
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
                                <x-heroicon-o-check-circle style="width:22px;height:22px;color:#16a34a;" />
                            </div>
                            <p class="ow-empty-title">Semua jadwal terpenuhi</p>
                            <p class="ow-empty-sub">Tidak ada pick up yang terlewat</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ══ Terlambat Selesaikan ══ --}}
            <div>
                <div class="ow-col-header danger">
                    <div class="ow-col-icon danger">
                        <x-heroicon-o-arrow-path style="width:14px;height:14px;color:#dc2626;" />
                    </div>
                    <p class="ow-col-title">Pengembalian</p>
                    <div class="ow-badge danger">
                        {{ $overdueReturns->count() }} booking
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
                                            {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->format('d M Y') }}
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
                                <x-heroicon-o-check-circle style="width:22px;height:22px;color:#16a34a;" />
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
