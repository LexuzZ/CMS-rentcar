<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="display:flex;align-items:center;justify-content:center;
                            width:36px;height:36px;border-radius:10px;
                            background:linear-gradient(135deg,#ef4444,#dc2626);
                            box-shadow:0 4px 12px rgba(220,38,38,0.35);">
                    <x-heroicon-s-exclamation-triangle style="width:16px;height:16px;color:#fff;" />
                </div>
                <div style="line-height:1.3;">
                    <p style="margin:0;font-size:14px;font-weight:700;color:inherit;">Tugas Terlambat</p>
                    <p style="margin:0;font-size:11px;color:#a8a29e;font-weight:400;margin-top:1px;">
                        Booking melewati jadwal · perlu tindakan segera
                    </p>
                </div>
                @php $total = $overduePickups->count() + $overdueReturns->count(); @endphp
                @if($total > 0)
                <div style="margin-left:auto;display:flex;align-items:center;gap:6px;
                            padding:5px 11px;border-radius:8px;
                            background:linear-gradient(135deg,#fef2f2,#fff1f2);
                            border:1px solid #fecaca;">
                    <span style="width:6px;height:6px;border-radius:50%;background:#dc2626;
                                 display:inline-block;animation:ow-blink 1.4s infinite;"></span>
                    <span style="font-size:12px;font-weight:700;color:#dc2626;">{{ $total }} aktif</span>
                </div>
                @endif
            </div>
        </x-slot>

        <div class="ow-grid">

            {{-- ══ Terlambat Pick Up ══ --}}
            <div class="ow-col">
                <div class="ow-col-header ow-col-header--warning">
                    <div class="ow-col-icon-wrap ow-col-icon-wrap--warning">
                        <x-heroicon-o-truck style="width:14px;height:14px;" />
                    </div>
                    <span class="ow-col-title">Terlambat Pick Up</span>
                    <span class="ow-count-pill ow-count-pill--warning">{{ $overduePickups->count() }}</span>
                </div>

                <div class="ow-cards">
                    @forelse ($overduePickups as $booking)
                    <div class="ow-card ow-card--warning">
                        <div class="ow-card-stripe ow-card-stripe--warning"></div>
                        <div class="ow-card-body">
                            <div class="ow-card-top">
                                <div class="ow-car-block">
                                    <div class="ow-car-icon ow-car-icon--warning">
                                        <x-heroicon-o-truck style="width:14px;height:14px;" />
                                    </div>
                                    <div>
                                        <p class="ow-car-name">
                                            {{ $booking->car->carModel->name }}
                                            <span class="ow-nopol">{{ $booking->car->nopol }}</span>
                                        </p>
                                        <p class="ow-customer">
                                            <x-heroicon-o-user style="width:10px;height:10px;flex-shrink:0;" />
                                            {{ $booking->customer->nama }}
                                        </p>
                                    </div>
                                </div>
                                <div class="ow-time-right">
                                    <p class="ow-time-rel ow-time-rel--warning">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->diffForHumans() }}
                                    </p>
                                    <p class="ow-time-abs">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            @if($canPerformActions)
                            <div class="ow-action-row">
                                <button wire:click="pickupOverdue({{ $booking->id }})" class="ow-btn ow-btn--warning">
                                    <x-heroicon-o-check style="width:12px;height:12px;" />
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
            <div class="ow-col">
                <div class="ow-col-header ow-col-header--danger">
                    <div class="ow-col-icon-wrap ow-col-icon-wrap--danger">
                        <x-heroicon-o-arrow-path style="width:14px;height:14px;" />
                    </div>
                    <span class="ow-col-title">Terlambat Selesaikan</span>
                    <span class="ow-count-pill ow-count-pill--danger">{{ $overdueReturns->count() }}</span>
                </div>

                <div class="ow-cards">
                    @forelse ($overdueReturns as $booking)
                    <div class="ow-card ow-card--danger">
                        <div class="ow-card-stripe ow-card-stripe--danger"></div>
                        <div class="ow-card-body">
                            <div class="ow-card-top">
                                <div class="ow-car-block">
                                    <div class="ow-car-icon ow-car-icon--danger">
                                        <x-heroicon-o-arrow-path style="width:14px;height:14px;" />
                                    </div>
                                    <div>
                                        <p class="ow-car-name">
                                            {{ $booking->car->carModel->name }}
                                            <span class="ow-nopol">{{ $booking->car->nopol }}</span>
                                        </p>
                                        <p class="ow-customer">
                                            <x-heroicon-o-user style="width:10px;height:10px;flex-shrink:0;" />
                                            {{ $booking->customer->nama }}
                                        </p>
                                    </div>
                                </div>
                                <div class="ow-time-right">
                                    <p class="ow-time-rel ow-time-rel--danger">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->diffForHumans() }}
                                    </p>
                                    <p class="ow-time-abs">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                            @if($canPerformActions)
                            <div class="ow-action-row">
                                <button wire:click="returnOverdue({{ $booking->id }})" class="ow-btn ow-btn--danger">
                                    <x-heroicon-o-check style="width:12px;height:12px;" />
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

<style>
    @keyframes ow-blink {
        0%,100% { opacity:1; transform:scale(1); }
        50%      { opacity:.4; transform:scale(.85); }
    }

    /* Grid */
    .ow-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:4px; }
    @media(max-width:768px) { .ow-grid { grid-template-columns:1fr; } }

    /* Column */
    .ow-col { display:flex; flex-direction:column; gap:10px; }

    /* Column header */
    .ow-col-header {
        display:flex; align-items:center; gap:8px;
        padding:10px 14px; border-radius:10px;
        border: 1px solid;
    }
    .ow-col-header--warning {
        background: linear-gradient(135deg,#fffbeb,#fef3c7);
        border-color: #fde68a;
    }
    .ow-col-header--danger {
        background: linear-gradient(135deg,#fff1f2,#ffe4e6);
        border-color: #fecaca;
    }
    .dark .ow-col-header--warning { background:linear-gradient(135deg,#1c1007,#271508); border-color:#44321a; }
    .dark .ow-col-header--danger  { background:linear-gradient(135deg,#1a0a0a,#230d0d); border-color:#3f1d1d; }

    .ow-col-icon-wrap {
        width:26px; height:26px; border-radius:7px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
    }
    .ow-col-icon-wrap--warning { background:#fef3c7; color:#d97706; border:1px solid #fde68a; }
    .ow-col-icon-wrap--danger  { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
    .dark .ow-col-icon-wrap--warning { background:#292100; color:#fbbf24; border-color:#44321a; }
    .dark .ow-col-icon-wrap--danger  { background:#2a0000; color:#f87171; border-color:#3f1d1d; }

    .ow-col-title {
        font-size:11.5px; font-weight:700;
        letter-spacing:.04em; flex:1;
    }
    .ow-col-header--warning .ow-col-title { color:#92400e; }
    .ow-col-header--danger  .ow-col-title { color:#991b1b; }
    .dark .ow-col-header--warning .ow-col-title { color:#fbbf24; }
    .dark .ow-col-header--danger  .ow-col-title { color:#f87171; }

    .ow-count-pill {
        font-size:11px; font-weight:800;
        padding:2px 9px; border-radius:100px; color:#fff;
    }
    .ow-count-pill--warning { background:linear-gradient(135deg,#f59e0b,#d97706); }
    .ow-count-pill--danger  { background:linear-gradient(135deg,#ef4444,#dc2626); }

    /* Cards */
    .ow-cards { display:flex; flex-direction:column; gap:8px; }

    .ow-card {
        position:relative; border-radius:12px; overflow:hidden;
        border:1px solid; transition:transform .15s, box-shadow .15s;
    }
    .ow-card--warning {
        background:linear-gradient(160deg,#fffdf5 0%,#ffffff 100%);
        border-color:#fde68a;
    }
    .ow-card--danger {
        background:linear-gradient(160deg,#fff8f8 0%,#ffffff 100%);
        border-color:#fecaca;
    }
    .ow-card--warning:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(245,158,11,.15); }
    .ow-card--danger:hover  { transform:translateY(-2px); box-shadow:0 6px 20px rgba(239,68,68,.15); }

    .dark .ow-card--warning { background:linear-gradient(160deg,#1c1500 0%,#1c1917 100%); border-color:#44321a; }
    .dark .ow-card--danger  { background:linear-gradient(160deg,#1a0808 0%,#1c1917 100%); border-color:#3f1d1d; }

    /* Stripe */
    .ow-card-stripe {
        position:absolute; left:0; top:0; bottom:0; width:3px;
    }
    .ow-card-stripe--warning { background:linear-gradient(180deg,#f59e0b,#d97706); }
    .ow-card-stripe--danger  { background:linear-gradient(180deg,#ef4444,#dc2626); }

    .ow-card-body { padding:13px 14px 12px 17px; }

    /* Card top row */
    .ow-card-top {
        display:flex; align-items:flex-start;
        justify-content:space-between; gap:10px;
    }

    /* Car block */
    .ow-car-block { display:flex; align-items:flex-start; gap:9px; flex:1; min-width:0; }

    .ow-car-icon {
        width:32px; height:32px; border-radius:8px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
    }
    .ow-car-icon--warning { background:#fef3c7; color:#d97706; border:1px solid #fde68a; }
    .ow-car-icon--danger  { background:#fee2e2; color:#dc2626; border:1px solid #fecaca; }
    .dark .ow-car-icon--warning { background:#292100; color:#fbbf24; border-color:#44321a; }
    .dark .ow-car-icon--danger  { background:#2a0000; color:#f87171; border-color:#3f1d1d; }

    .ow-car-name {
        margin:0; font-size:13px; font-weight:700; color:#1c1917;
        display:flex; align-items:center; gap:5px; flex-wrap:wrap;
    }
    .dark .ow-car-name { color:#fafaf9; }

    .ow-nopol {
        font-family:ui-monospace,monospace; font-size:10px; font-weight:600;
        padding:1px 6px; border-radius:4px; letter-spacing:.06em;
        background:#f5f4f2; color:#78716c; border:1px solid #e7e5e4;
    }
    .dark .ow-nopol { background:#292524; color:#a8a29e; border-color:#44403c; }

    .ow-customer {
        display:flex; align-items:center; gap:4px;
        margin-top:4px; font-size:11.5px; color:#78716c;
    }
    .dark .ow-customer { color:#57534e; }

    /* Time right */
    .ow-time-right { text-align:right; flex-shrink:0; }
    .ow-time-rel {
        margin:0; font-size:12px; font-weight:700;
        padding:2px 8px; border-radius:6px; display:inline-block;
    }
    .ow-time-rel--warning { background:#fef3c7; color:#92400e; }
    .ow-time-rel--danger  { background:#fee2e2; color:#991b1b; }
    .dark .ow-time-rel--warning { background:#292100; color:#fbbf24; }
    .dark .ow-time-rel--danger  { background:#2a0000; color:#f87171; }
    .ow-time-abs { margin:4px 0 0; font-size:10.5px; color:#c4bfbb; }

    /* Action row */
    .ow-action-row {
        display:flex; justify-content:flex-end;
        margin-top:10px; padding-top:10px;
        border-top:1px solid #f5f0eb;
    }
    .dark .ow-action-row { border-color:#292524; }

    .ow-btn {
        display:inline-flex; align-items:center; gap:5px;
        padding:6px 14px; border:none; border-radius:8px;
        font-size:12px; font-weight:700; cursor:pointer; font-family:inherit;
        color:#fff; transition:filter .15s, transform .12s;
    }
    .ow-btn:hover { filter:brightness(.9); transform:translateY(-1px); }
    .ow-btn:active { transform:translateY(0); }
    .ow-btn--warning {
        background:linear-gradient(135deg,#f59e0b,#d97706);
        box-shadow:0 3px 10px rgba(245,158,11,.35);
    }
    .ow-btn--danger {
        background:linear-gradient(135deg,#ef4444,#dc2626);
        box-shadow:0 3px 10px rgba(239,68,68,.35);
    }

    /* Empty */
    .ow-empty {
        display:flex; flex-direction:column; align-items:center;
        padding:32px 16px; text-align:center;
        border:1.5px dashed #d1fae5; border-radius:12px;
        background:linear-gradient(160deg,#f0fdf4,#f7fef9);
    }
    .dark .ow-empty { background:linear-gradient(160deg,#052e16,#071f10); border-color:#14532d; }

    .ow-empty-icon {
        width:44px; height:44px; border-radius:50%;
        background:#dcfce7; border:1px solid #bbf7d0;
        display:flex; align-items:center; justify-content:center; margin-bottom:10px;
    }
    .dark .ow-empty-icon { background:#052e16; border-color:#14532d; }

    .ow-empty-title { margin:0; font-size:13px; font-weight:600; color:#374151; }
    .dark .ow-empty-title { color:#6b7280; }
    .ow-empty-sub { margin:3px 0 0; font-size:11px; color:#9ca3af; }
    .dark .ow-empty-sub { color:#44403c; }
</style>
