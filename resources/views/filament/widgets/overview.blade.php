<x-filament-widgets::widget>

    <style>
        /* ── Grid ── */
        .do-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
        }
        @media (max-width: 1280px) { .do-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px)  { .do-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px)  { .do-grid { grid-template-columns: 1fr; } }

        /* ── Card ── */
        .do-card {
            position: relative;
            background: #fff;
            border: 1px solid #e7e5e4;
            border-radius: 14px;
            padding: 16px;
            overflow: hidden;
            transition: box-shadow .18s, transform .18s;
        }
        .dark .do-card { background: #1c1917; border-color: #292524; }
        .do-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.07); transform: translateY(-2px); }

        /* Top accent line */
        .do-card-bar {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 14px 14px 0 0;
        }

        /* Subtle bg glow */
        .do-card-glow {
            position: absolute;
            top: -20px; right: -20px;
            width: 80px; height: 80px;
            border-radius: 50%;
            opacity: .07;
            pointer-events: none;
        }

        /* Icon */
        .do-icon-wrap {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-bottom: 12px;
            border: 1px solid;
        }

        /* Label */
        .do-label {
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #a8a29e;
            margin-bottom: 5px;
        }
        .dark .do-label { color: #78716c; }

        /* Value */
        .do-value {
            font-size: 20px;
            font-weight: 800;
            color: #1c1917;
            line-height: 1.1;
            letter-spacing: -.02em;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dark .do-value { color: #fafaf9; }

        /* Footer row */
        .do-footer {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Trend pill */
        .do-trend {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 7px;
            border-radius: 100px;
            font-size: 10.5px;
            font-weight: 700;
            border: 1px solid;
        }
        .do-trend.up-good    { background:#f0fdf4; color:#166534; border-color:#bbf7d0; }
        .do-trend.down-good  { background:#f0fdf4; color:#166534; border-color:#bbf7d0; }
        .do-trend.up-bad     { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
        .do-trend.down-bad   { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
        .do-trend.neutral    { background:#f5f4f2; color:#78716c; border-color:#e7e5e4; }

        .do-vs {
            font-size: 10px;
            color: #c4bfbb;
        }

        .do-description {
            font-size: 10.5px;
            color: #a8a29e;
        }

        /* Color palettes */
        .do-blue   { --c: #3b82f6; --cl: #eff6ff; --cb: #bfdbfe; }
        .do-teal   { --c: #0d9488; --cl: #f0fdfa; --cb: #99f6e4; }
        .do-green  { --c: #16a34a; --cl: #f0fdf4; --cb: #bbf7d0; }
        .do-rose   { --c: #e11d48; --cl: #fff1f2; --cb: #fecdd3; }
        .do-amber  { --c: #d97706; --cl: #fffbeb; --cb: #fde68a; }
        .do-violet { --c: #7c3aed; --cl: #f5f3ff; --cb: #ddd6fe; }

        .do-card-bar  { background: var(--c); }
        .do-card-glow { background: var(--c); }
        .do-icon-wrap { background: var(--cl); border-color: var(--cb); color: var(--c); }

        /* Dark palette overrides */
        .dark .do-blue   { --cl: #1e3a5f; --cb: #1d4ed844; }
        .dark .do-teal   { --cl: #042f2e; --cb: #0d948844; }
        .dark .do-green  { --cl: #052e16; --cb: #16a34a44; }
        .dark .do-rose   { --cl: #1a0505; --cb: #e11d4844; }
        .dark .do-amber  { --cl: #1c1507; --cb: #d9770644; }
        .dark .do-violet { --cl: #1e0a38; --cb: #7c3aed44; }

        /* Header row */
        .do-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .do-header-left { display: flex; align-items: center; gap: 10px; }
        .do-header-icon {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 9px;
            background: #eff6ff; border: 1px solid #bfdbfe;
        }
        .do-header-title { font-size: 14px; font-weight: 600; color: inherit; margin: 0; }
        .do-header-sub   { font-size: 11px; color: #a8a29e; margin: 2px 0 0; }
        .do-header-bulan {
            font-size: 11px; font-weight: 600;
            padding: 3px 10px; border-radius: 7px;
            background: #faf9f7; border: 1px solid #e7e5e4;
            color: #78716c;
        }
        .dark .do-header-bulan { background: #1c1917; border-color: #292524; color: #a8a29e; }
    </style>

    {{-- ── Header ── --}}
    <div class="do-header">
        <div class="do-header-left">
            <div class="do-header-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
            </div>
            <div>
                <p class="do-header-title">Ringkasan Keuangan</p>
                <p class="do-header-sub">Performa bulan berjalan vs bulan lalu</p>
            </div>
        </div>
        <div class="do-header-bulan">📅 {{ $bulan }}</div>
    </div>

    {{-- ── Cards ── --}}
    <div class="do-grid">
        @foreach ($stats as $stat)
        @php
            $pct     = $stat['pct'];
            $good    = $stat['good']; // 'up' or 'down'
            $isUp    = $pct !== null && $pct >= 0;
            $arrow   = $isUp ? '↑' : '↓';

            if ($pct === null) {
                $trendClass = 'neutral';
            } elseif (($good === 'up' && $isUp) || ($good === 'down' && !$isUp)) {
                $trendClass = $isUp ? 'up-good' : 'down-good';
            } else {
                $trendClass = $isUp ? 'up-bad' : 'down-bad';
            }
        @endphp
        <div class="do-card do-{{ $stat['color'] }}">
            <div class="do-card-bar"></div>
            <div class="do-card-glow"></div>

            {{-- Icon --}}
            <div class="do-icon-wrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $stat['icon'] }}"/>
                </svg>
            </div>

            {{-- Label --}}
            <p class="do-label">{{ $stat['label'] }}</p>

            {{-- Value --}}
            <p class="do-value">{{ $stat['value'] }}</p>

            {{-- Footer --}}
            <div class="do-footer">
                @if($pct !== null)
                    <span class="do-trend {{ $trendClass }}">
                        {{ $arrow }} {{ number_format(abs($pct), 1) }}%
                    </span>
                    <span class="do-vs">vs bln lalu</span>
                @elseif(isset($stat['description']))
                    <span class="do-description">{{ $stat['description'] }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</x-filament-widgets::widget>
