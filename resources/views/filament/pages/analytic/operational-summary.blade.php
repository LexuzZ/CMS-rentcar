<x-filament-panels::page>
<div class="rp-page">

    {{-- ===== FILTER ===== --}}
    <x-filament::section class="rp-filter-section">
        {{ $this->form }}
    </x-filament::section>

    {{-- ===== STATISTICS ===== --}}
    <div class="rp-stats-grid">
        @foreach ($statistics as $stat)
        @php
            $isPos   = $stat['change'] >= 0;
            $color   = $stat['color'];
            $accent  = $color === 'success' ? 'emerald' : ($color === 'warning' ? 'amber' : 'rose');
            $icon    = $isPos ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        @endphp
        <div class="rp-stat rp-stat--{{ $accent }}">
            <div class="rp-stat-bar"></div>
            <div class="rp-stat-body">
                <p class="rp-stat-label">{{ $stat['label'] }}</p>
                <p class="rp-stat-value">Rp {{ number_format($stat['value'], 0, ',', '.') }}</p>
                <div class="rp-stat-badge rp-stat-badge--{{ $isPos ? 'up' : 'down' }}">
                    @if($isPos)
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    @else
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                    @endif
                    {{ number_format(abs($stat['change']), 1) }}%
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ===== RINGKASAN ===== --}}
    <div class="rp-report">

        {{-- Report Header --}}
        <div class="rp-report-header">
            <div class="rp-report-header-left">
                <div class="rp-report-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <div>
                    <h2 class="rp-report-title">Ringkasan Operasional</h2>
                    <p class="rp-report-period">Periode {{ $reportTitle }}</p>
                </div>
            </div>
            <button wire:click="downloadPdf" class="rp-export-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export PDF
            </button>
        </div>

        {{-- Tables wrapper --}}
        <div class="rp-tables">

            {{-- PENDAPATAN --}}
            <div class="rp-table-block">
                <div class="rp-table-head rp-table-head--income">
                    <span class="rp-table-head-dot"></span>
                    Rincian Pendapatan
                </div>
                <table class="rp-table">
                    <thead>
                        <tr>
                            <th class="rp-th rp-th--left">Keterangan</th>
                            <th class="rp-th rp-th--right">Jumlah</th>
                            <th class="rp-th rp-th--center">Perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rincianTableData as $row)
                        <tr class="rp-tr rp-tr--income">
                            <td class="rp-td rp-td--label">{{ $row['label'] }}</td>
                            <td class="rp-td rp-td--value rp-td--income-val">Rp {{ number_format($row['value'], 0, ',', '.') }}</td>
                            <td class="rp-td rp-td--center">
                                <span class="rp-change rp-change--up">
                                    +{{ number_format($row['change'], 1) }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PENGELUARAN --}}
            <div class="rp-table-block">
                <div class="rp-table-head rp-table-head--expense">
                    <span class="rp-table-head-dot rp-table-head-dot--expense"></span>
                    Rincian Pengeluaran
                </div>
                <table class="rp-table">
                    <thead>
                        <tr>
                            <th class="rp-th rp-th--left">Keterangan</th>
                            <th class="rp-th rp-th--right">Jumlah</th>
                            <th class="rp-th rp-th--center">Perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rincianCostTableData as $row)
                        <tr class="rp-tr rp-tr--expense">
                            <td class="rp-td rp-td--label">{{ $row['label'] }}</td>
                            <td class="rp-td rp-td--value rp-td--expense-val">Rp {{ number_format($row['value'], 0, ',', '.') }}</td>
                            <td class="rp-td rp-td--center">
                                <span class="rp-change rp-change--down">
                                    {{ number_format($row['change'], 1) }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- TOTAL --}}
            <div class="rp-table-block rp-table-block--total">
                <div class="rp-table-head rp-table-head--total">
                    <span class="rp-table-head-dot rp-table-head-dot--total"></span>
                    Total & Laba Bersih
                </div>
                <table class="rp-table">
                    <tbody>
                        @foreach ($summaryTableData as $row)
                        @php $rowPos = $row['change'] >= 0; @endphp
                        <tr class="rp-tr rp-tr--total">
                            <td class="rp-td rp-td--label rp-td--total-label">{{ $row['label'] }}</td>
                            <td class="rp-td rp-td--value rp-td--total-val">Rp {{ number_format($row['value'], 0, ',', '.') }}</td>
                            <td class="rp-td rp-td--center">
                                <span class="rp-change {{ $rowPos ? 'rp-change--up' : 'rp-change--down' }}">
                                    {{ $rowPos ? '+' : '' }}{{ number_format($row['change'], 1) }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>{{-- /rp-tables --}}
    </div>{{-- /rp-report --}}

</div>{{-- /rp-page --}}

<style>
    /* ===== PAGE ===== */
    .rp-page { display: flex; flex-direction: column; gap: 20px; }

    .rp-filter-section { }

    /* ===== STATS ===== */
    .rp-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    @media (min-width: 1280px) { .rp-stats-grid { grid-template-columns: repeat(4, 1fr); } }

    .rp-stat {
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #fff;
        overflow: hidden;
        transition: transform 0.18s, box-shadow 0.18s;
        position: relative;
    }
    .dark .rp-stat { background: #1f2937; border-color: #374151; }
    .rp-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }

    .rp-stat-bar { height: 4px; width: 100%; }
    .rp-stat--emerald .rp-stat-bar { background: linear-gradient(90deg, #10b981, #34d399); }
    .rp-stat--amber   .rp-stat-bar { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
    .rp-stat--rose    .rp-stat-bar { background: linear-gradient(90deg, #f43f5e, #fb7185); }

    .rp-stat-body { padding: 16px 18px 18px; }

    .rp-stat-label {
        font-size: 11px; font-weight: 700; letter-spacing: 0.07em;
        text-transform: uppercase; color: #6b7280; margin-bottom: 8px;
    }
    .dark .rp-stat-label { color: #9ca3af; }

    .rp-stat-value {
        font-size: 18px; font-weight: 700; color: #111827;
        font-variant-numeric: tabular-nums; margin-bottom: 10px;
        letter-spacing: -0.02em;
    }
    .dark .rp-stat-value { color: #f3f4f6; }

    .rp-stat-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 11px; font-weight: 700;
        padding: 3px 9px; border-radius: 100px;
    }
    .rp-stat-badge--up   { background: #dcfce7; color: #15803d; }
    .rp-stat-badge--down { background: #fee2e2; color: #b91c1c; }
    .dark .rp-stat-badge--up   { background: #064e3b; color: #4ade80; }
    .dark .rp-stat-badge--down { background: #450a0a; color: #f87171; }

    /* ===== REPORT CARD ===== */
    .rp-report {
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .dark .rp-report { background: #1f2937; border-color: #374151; }

    /* Report Header */
    .rp-report-header {
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafafa;
        flex-wrap: wrap;
    }
    .dark .rp-report-header { background: #111827; border-color: #374151; }

    .rp-report-header-left { display: flex; align-items: center; gap: 14px; }

    .rp-report-icon {
        width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        box-shadow: 0 4px 12px rgba(99,102,241,0.3);
    }

    .rp-report-title { font-size: 17px; font-weight: 700; color: #0f172a; }
    .dark .rp-report-title { color: #f1f5f9; }
    .rp-report-period { font-size: 13px; color: #64748b; margin-top: 2px; }
    .dark .rp-report-period { color: #94a3b8; }

    .rp-export-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px; font-weight: 600; color: #374151;
        background: #fff;
        cursor: pointer; font-family: inherit;
        transition: border-color 0.15s, background 0.15s, color 0.15s;
    }
    .dark .rp-export-btn { background: #1f2937; border-color: #4b5563; color: #d1d5db; }
    .rp-export-btn:hover { border-color: #6366f1; color: #4f46e5; background: #eef2ff; }
    .dark .rp-export-btn:hover { background: #312e81; color: #c7d2fe; border-color: #6366f1; }

    /* Tables container */
    .rp-tables { padding: 20px 24px; display: flex; flex-direction: column; gap: 16px; }

    .rp-table-block {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .dark .rp-table-block { border-color: #374151; }

    .rp-table-block--total { border-width: 2px; }

    .rp-table-head {
        display: flex; align-items: center; gap: 9px;
        padding: 11px 18px;
        font-size: 13px; font-weight: 700;
    }
    .rp-table-head--income  { background: #f0fdf4; color: #15803d; }
    .rp-table-head--expense { background: #fff1f2; color: #be123c; }
    .rp-table-head--total   { background: #eef2ff; color: #4338ca; }
    .dark .rp-table-head--income  { background: #052e16; color: #4ade80; }
    .dark .rp-table-head--expense { background: #4c0519; color: #fb7185; }
    .dark .rp-table-head--total   { background: #1e1b4b; color: #a5b4fc; }

    .rp-table-head-dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        background: #10b981;
    }
    .rp-table-head-dot--expense { background: #f43f5e; }
    .rp-table-head-dot--total   { background: #6366f1; }

    /* Table */
    .rp-table { width: 100%; border-collapse: collapse; }

    .rp-th {
        padding: 9px 18px;
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
        color: #9ca3af; background: #fafafa;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .rp-th { background: #111827; border-color: #374151; color: #6b7280; }
    .rp-th--left   { text-align: left; }
    .rp-th--right  { text-align: right; }
    .rp-th--center { text-align: center; }

    .rp-tr { border-top: 1px solid #f8fafc; transition: background 0.12s; }
    .dark .rp-tr { border-color: #374151; }
    .rp-tr--income:hover  { background: #f0fdf4; }
    .rp-tr--expense:hover { background: #fff1f2; }
    .rp-tr--total:hover   { background: #eef2ff; }
    .dark .rp-tr--income:hover  { background: #052e16; }
    .dark .rp-tr--expense:hover { background: #4c0519; }
    .dark .rp-tr--total:hover   { background: #1e1b4b; }

    .rp-td { padding: 13px 18px; font-size: 14px; }

    .rp-td--label { font-weight: 500; color: #374151; }
    .dark .rp-td--label { color: #d1d5db; }

    .rp-td--total-label { font-weight: 700; color: #1e40af; }
    .dark .rp-td--total-label { color: #a5b4fc; }

    .rp-td--value { text-align: right; font-variant-numeric: tabular-nums; font-weight: 600; }
    .rp-td--center { text-align: center; }

    .rp-td--income-val  { color: #15803d; }
    .rp-td--expense-val { color: #be123c; }
    .rp-td--total-val   { color: #1e40af; font-size: 15px; font-weight: 700; }
    .dark .rp-td--income-val  { color: #4ade80; }
    .dark .rp-td--expense-val { color: #fb7185; }
    .dark .rp-td--total-val   { color: #a5b4fc; }

    /* Change badges */
    .rp-change {
        display: inline-flex; align-items: center;
        font-size: 12px; font-weight: 700;
        padding: 3px 10px; border-radius: 100px;
    }
    .rp-change--up   { background: #dcfce7; color: #15803d; }
    .rp-change--down { background: #fee2e2; color: #b91c1c; }
    .dark .rp-change--up   { background: #064e3b; color: #4ade80; }
    .dark .rp-change--down { background: #450a0a; color: #f87171; }
</style>
</x-filament-panels::page>
