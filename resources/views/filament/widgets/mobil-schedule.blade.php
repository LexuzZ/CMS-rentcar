<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="display:flex; align-items:center; justify-content:center;
                            width:36px; height:36px; border-radius:10px;
                            background:#eff6ff; border:1px solid #bfdbfe;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13" rx="2"/>
                        <path d="M16 8h4l3 5v3h-7V8z"/>
                        <circle cx="5.5" cy="18.5" r="2.5"/>
                        <circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                </div>
                <div style="line-height:1.3;">
                    <p style="margin:0; font-size:14px; font-weight:600; color:inherit;">Jadwal Kendaraan</p>
                    <p style="margin:0; font-size:11px; color:#a8a29e; font-weight:400; margin-top:1px;">
                        Hari ini &amp; besok
                    </p>
                </div>

                {{-- Summary badges --}}
                <div style="margin-left:auto; display:flex; gap:6px; flex-wrap:wrap;">
                    <span style="font-size:11px; font-weight:600; padding:3px 9px; border-radius:6px;
                                 background:#fffbeb; border:1px solid #fde68a; color:#92400e;">
                        {{ $keluarHariIni->count() + $keluarBesok->count() }} keluar
                    </span>
                    <span style="font-size:11px; font-weight:600; padding:3px 9px; border-radius:6px;
                                 background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">
                        {{ $kembaliHariIni->count() + $kembaliBesok->count() }} kembali
                    </span>
                </div>
            </div>
        </x-slot>

        <style>
            .ms-grid   { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
            @media(max-width:768px) { .ms-grid { grid-template-columns:1fr; } }

            /* Section header */
            .ms-sec-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 10px;
                padding-bottom: 8px;
                border-bottom: 1px solid #f0eeed;
            }
            .ms-sec-icon {
                display: flex; align-items: center; justify-content: center;
                width: 26px; height: 26px; border-radius: 7px; flex-shrink: 0;
            }
            .ms-sec-icon.keluar  { background:#fffbeb; border:1px solid #fde68a; }
            .ms-sec-icon.kembali { background:#f0fdf4; border:1px solid #bbf7d0; }
            .ms-sec-title {
                font-size: 11px; font-weight: 700;
                text-transform: uppercase; letter-spacing: .07em; color: #a8a29e;
            }
            .ms-sec-badge {
                margin-left: auto;
                font-size: 11px; font-weight: 700;
                padding: 2px 8px; border-radius: 6px; border: 1px solid;
            }
            .ms-sec-badge.keluar  { background:#fffbeb; border-color:#fde68a; color:#92400e; }
            .ms-sec-badge.kembali { background:#f0fdf4; border-color:#bbf7d0; color:#166534; }

            /* Day label */
            .ms-day-label {
                font-size: 10px; font-weight: 600; text-transform: uppercase;
                letter-spacing: .08em; color: #c4bfbb;
                margin: 12px 0 6px; padding-left: 2px;
            }
            .ms-day-label:first-of-type { margin-top: 0; }

            /* Cards */
            .ms-cards { display: flex; flex-direction: column; gap: 7px; }

            .ms-card {
                position: relative;
                background: #faf9f7;
                border-radius: 10px;
                overflow: hidden;
                transition: box-shadow .15s;
            }
            .ms-card.keluar  { border: 1px solid #fde68a; }
            .ms-card.kembali { border: 1px solid #bbf7d0; }
            .ms-card.keluar:hover  { box-shadow: 0 2px 10px rgba(245,158,11,.12); }
            .ms-card.kembali:hover { box-shadow: 0 2px 10px rgba(34,197,94,.10); }

            .ms-card-bar { position:absolute; left:0; top:0; bottom:0; width:3px; border-radius:10px 0 0 10px; }
            .ms-card-bar.keluar  { background:#f59e0b; }
            .ms-card-bar.kembali { background:#22c55e; }

            .ms-card-body { padding: 10px 12px 10px 16px; }

            .ms-card-row  { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; }
            .ms-car-name  { margin:0; font-size:12.5px; font-weight:700; color:#1c1917; }
            .ms-nopol {
                display: inline-block; font-family: ui-monospace, monospace;
                font-size: 10px; font-weight: 500;
                background: #f5f4f2; color: #78716c;
                border: 1px solid #e7e5e4; border-radius: 4px;
                padding: 1px 5px; letter-spacing:.04em;
                margin-left: 5px; vertical-align: middle;
            }
            .ms-customer { display:flex; align-items:center; gap:4px; margin-top:4px; font-size:11px; color:#a8a29e; }

            .ms-time-right { text-align:right; flex-shrink:0; }
            .ms-time-main  { margin:0; font-size:12px; font-weight:700; }
            .ms-time-main.keluar  { color:#92400e; }
            .ms-time-main.kembali { color:#166534; }
            .ms-time-sub   { margin:2px 0 0; font-size:10px; color:#c4bfbb; }

            .ms-divider { border:none; border-top:1px solid #ede9e5; margin: 8px 0; }
            .ms-actions { display:flex; justify-content:flex-end; gap:6px; }

            .ms-btn {
                display: inline-flex; align-items: center; gap: 4px;
                padding: 4px 10px; border:none; border-radius:6px;
                font-size: 11px; font-weight: 600; cursor: pointer; color: #fff;
                transition: filter .15s, transform .1s;
            }
            .ms-btn:active { transform: scale(.96); }
            .ms-btn.pickup    { background: #f59e0b; }
            .ms-btn.pickup:hover { filter: brightness(.9); }
            .ms-btn.selesai   { background: #22c55e; }
            .ms-btn.selesai:hover { filter: brightness(.9); }
            .ms-btn.edit      { background: #f5f4f2; color: #78716c; border: 1px solid #e7e5e4; }
            .ms-btn.edit:hover { background: #ede9e5; }

            /* Empty */
            .ms-empty {
                display: flex; flex-direction: column; align-items: center;
                padding: 20px 12px; border: 1.5px dashed #e7e5e4;
                border-radius: 10px; background: #faf9f7; text-align: center;
            }
            .ms-empty p { margin:0; font-size:12px; color:#c4bfbb; }

            /* Dark */
            @media (prefers-color-scheme: dark) {
                .ms-card         { background:#1c1917; }
                .ms-card.keluar  { border-color:#44321a; }
                .ms-card.kembali { border-color:#14532d; }
                .ms-car-name     { color:#fafaf9; }
                .ms-nopol        { background:#292524; color:#a8a29e; border-color:#44403c; }
                .ms-empty        { background:#1c1917; border-color:#292524; }
                .ms-divider      { border-color:#292524; }
                .ms-sec-header   { border-color:#292524; }
                .ms-btn.edit     { background:#292524; color:#a8a29e; border-color:#44403c; }
            }
        </style>

        <div class="ms-grid">

            {{-- ══ KOLOM KIRI: KELUAR ══ --}}
            <div>
                <div class="ms-sec-header">
                    <div class="ms-sec-icon keluar">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                             stroke="#d97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </div>
                    <p class="ms-sec-title">Mobil Keluar</p>
                    <span class="ms-sec-badge keluar">
                        {{ $keluarHariIni->count() + $keluarBesok->count() }} booking
                    </span>
                </div>

                {{-- 1. Keluar Hari Ini --}}
                <p class="ms-day-label">📅 Hari ini · {{ $today }}</p>
                <div class="ms-cards">
                    @forelse ($keluarHariIni as $booking)
                        @include('filament.widgets.partials.ms-card', [
                            'booking' => $booking,
                            'type'    => 'keluar',
                            'timeField' => 'waktu_keluar',
                            'dateField' => 'tanggal_keluar',
                        ])
                    @empty
                        <div class="ms-empty"><p>Tidak ada mobil keluar hari ini.</p></div>
                    @endforelse
                </div>

                {{-- 3. Keluar Besok --}}
                <p class="ms-day-label" style="margin-top:16px;">🗓 Besok · {{ $tomorrow }}</p>
                <div class="ms-cards">
                    @forelse ($keluarBesok as $booking)
                        @include('filament.widgets.partials.ms-card', [
                            'booking' => $booking,
                            'type'    => 'keluar',
                            'timeField' => 'waktu_keluar',
                            'dateField' => 'tanggal_keluar',
                        ])
                    @empty
                        <div class="ms-empty"><p>Tidak ada mobil keluar besok.</p></div>
                    @endforelse
                </div>
            </div>

            {{-- ══ KOLOM KANAN: KEMBALI ══ --}}
            <div>
                <div class="ms-sec-header">
                    <div class="ms-sec-icon kembali">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                             stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"/>
                            <polyline points="12 19 5 12 12 5"/>
                        </svg>
                    </div>
                    <p class="ms-sec-title">Mobil Kembali</p>
                    <span class="ms-sec-badge kembali">
                        {{ $kembaliHariIni->count() + $kembaliBesok->count() }} booking
                    </span>
                </div>

                {{-- 2. Kembali Hari Ini --}}
                <p class="ms-day-label">📅 Hari ini · {{ $today }}</p>
                <div class="ms-cards">
                    @forelse ($kembaliHariIni as $booking)
                        @include('filament.widgets.partials.ms-card', [
                            'booking' => $booking,
                            'type'    => 'kembali',
                            'timeField' => 'waktu_kembali',
                            'dateField' => 'tanggal_kembali',
                        ])
                    @empty
                        <div class="ms-empty"><p>Tidak ada mobil kembali hari ini.</p></div>
                    @endforelse
                </div>

                {{-- 4. Kembali Besok --}}
                <p class="ms-day-label" style="margin-top:16px;">🗓 Besok · {{ $tomorrow }}</p>
                <div class="ms-cards">
                    @forelse ($kembaliBesok as $booking)
                        @include('filament.widgets.partials.ms-card', [
                            'booking' => $booking,
                            'type'    => 'kembali',
                            'timeField' => 'waktu_kembali',
                            'dateField' => 'tanggal_kembali',
                        ])
                    @empty
                        <div class="ms-empty"><p>Tidak ada mobil kembali besok.</p></div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
