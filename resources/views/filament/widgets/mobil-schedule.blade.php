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
                    <p style="margin:0; font-size:11px; color:#a8a29e; font-weight:400; margin-top:1px;">Hari ini &amp; besok</p>
                </div>
                <div style="margin-left:auto; display:flex; gap:6px; flex-wrap:wrap;">
                    <span style="font-size:11px; font-weight:600; padding:3px 9px; border-radius:6px;
                                 background:#fffbeb; border:1px solid #fde68a; color:#92400e;">
                        ↑ {{ $keluarHariIni->count() + $keluarBesok->count() }} keluar
                    </span>
                    <span style="font-size:11px; font-weight:600; padding:3px 9px; border-radius:6px;
                                 background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">
                        ↓ {{ $kembaliHariIni->count() + $kembaliBesok->count() }} kembali
                    </span>
                </div>
            </div>
        </x-slot>

        <style>
            /* Day header */
            .ms-day-header {
                display: flex; align-items: center; gap: 10px;
                margin: 20px 0 12px;
            }
            .ms-day-header:first-of-type { margin-top: 0; }
            .ms-day-pill {
                display: flex; align-items: center; gap: 6px;
                padding: 4px 12px; border-radius: 8px;
                font-size: 12px; font-weight: 700;
                border: 1px solid;
            }
            .ms-day-pill.today   { background:#eff6ff; border-color:#bfdbfe; color:#1d4ed8; }
            .ms-day-pill.tomorrow{ background:#f5f3ff; border-color:#ddd6fe; color:#6d28d9; }
            .ms-day-line { flex:1; height:1px; background:#f0eeed; }
            .dark .ms-day-line { background:#292524; }

            /* 3-col grid for cards */
            .ms-cards-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 14px;
            }
            @media(max-width: 1100px) {
                .ms-cards-grid { grid-template-columns: repeat(2, 1fr); }
            }
            @media(max-width: 700px) {
                .ms-cards-grid { grid-template-columns: 1fr; }
            }

            /* Empty */
            .ms-empty {
                display: flex; flex-direction: column; align-items: center;
                padding: 24px 16px; border: 1.5px dashed #e7e5e4;
                border-radius: 12px; background: #faf9f7; text-align: center;
                grid-column: 1 / -1;
            }
            .dark .ms-empty { background:#1c1917; border-color:#292524; }
            .ms-empty p { margin:0; font-size:12px; color:#c4bfbb; }

            /* Section divider */
            .ms-section-divider {
                display: flex; align-items: center; gap: 10px;
                margin: 8px 0;
            }
            .ms-section-label {
                font-size: 10px; font-weight: 700; text-transform: uppercase;
                letter-spacing: .08em; white-space: nowrap;
                padding: 2px 8px; border-radius: 5px;
            }
            .ms-section-label.keluar  { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
            .ms-section-label.kembali { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
            .ms-section-line { flex:1; height:1px; background:#f0eeed; }
            .dark .ms-section-line { background:#292524; }
        </style>

        {{-- ════════════ HARI INI ════════════ --}}
        <div class="ms-day-header">
            <div class="ms-day-pill today">
                📅 Hari ini &nbsp;·&nbsp; {{ $today }}
            </div>
            <div class="ms-day-line"></div>
            <span style="font-size:11px; color:#a8a29e;">
                {{ $keluarHariIni->count() + $kembaliHariIni->count() }} booking
            </span>
        </div>

        {{-- 1. Keluar hari ini --}}
        <div class="ms-section-divider">
            <span class="ms-section-label keluar">↑ Keluar</span>
            <div class="ms-section-line"></div>
        </div>
        <div class="ms-cards-grid" style="margin-bottom:16px;">
            @forelse ($keluarHariIni as $booking)
                @include('filament.widgets.partials.ms-card', [
                    'booking' => $booking, 'type' => 'keluar', 'isToday' => true,
                ])
            @empty
                <div class="ms-empty"><p>Tidak ada mobil keluar hari ini.</p></div>
            @endforelse
        </div>

        {{-- 2. Kembali hari ini --}}
        <div class="ms-section-divider">
            <span class="ms-section-label kembali">↓ Kembali</span>
            <div class="ms-section-line"></div>
        </div>
        <div class="ms-cards-grid">
            @forelse ($kembaliHariIni as $booking)
                @include('filament.widgets.partials.ms-card', [
                    'booking' => $booking, 'type' => 'kembali', 'isToday' => true,
                ])
            @empty
                <div class="ms-empty"><p>Tidak ada mobil kembali hari ini.</p></div>
            @endforelse
        </div>

        {{-- ════════════ BESOK ════════════ --}}
        <div class="ms-day-header">
            <div class="ms-day-pill tomorrow">
                🗓 Besok &nbsp;·&nbsp; {{ $tomorrow }}
            </div>
            <div class="ms-day-line"></div>
            <span style="font-size:11px; color:#a8a29e;">
                {{ $keluarBesok->count() + $kembaliBesok->count() }} booking
            </span>
        </div>

        {{-- 3. Keluar besok --}}
        <div class="ms-section-divider">
            <span class="ms-section-label keluar">↑ Keluar</span>
            <div class="ms-section-line"></div>
        </div>
        <div class="ms-cards-grid" style="margin-bottom:16px;">
            @forelse ($keluarBesok as $booking)
                @include('filament.widgets.partials.ms-card', [
                    'booking' => $booking, 'type' => 'keluar', 'isToday' => false,
                ])
            @empty
                <div class="ms-empty"><p>Tidak ada mobil keluar besok.</p></div>
            @endforelse
        </div>

        {{-- 4. Kembali besok --}}
        <div class="ms-section-divider">
            <span class="ms-section-label kembali">↓ Kembali</span>
            <div class="ms-section-line"></div>
        </div>
        <div class="ms-cards-grid">
            @forelse ($kembaliBesok as $booking)
                @include('filament.widgets.partials.ms-card', [
                    'booking' => $booking, 'type' => 'kembali', 'isToday' => false,
                ])
            @empty
                <div class="ms-empty"><p>Tidak ada mobil kembali besok.</p></div>
            @endforelse
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
