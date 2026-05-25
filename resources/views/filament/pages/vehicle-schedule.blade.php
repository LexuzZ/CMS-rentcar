<x-filament-panels::page>
    <div x-data="{ scheduleData: @entangle('scheduleData') }">

        {{-- Filter --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Schedule --}}
        <x-filament::section class="mt-6">

            {{-- Legend --}}
            <div class="sc-legend">
                <span class="sc-legend-item">
                    <span class="sc-dot sc-dot--booking"></span> Booking
                </span>
                <span class="sc-legend-item">
                    <span class="sc-dot sc-dot--disewa"></span> Disewa
                </span>
                <span class="sc-legend-item">
                    <span class="sc-dot sc-dot--selesai"></span> Selesai
                </span>
                <span class="sc-legend-item">
                    <span class="sc-dot sc-dot--batal"></span> Batal
                </span>
            </div>

            <div class="sc-wrap">
                <table class="sc-table">
                    <thead>
                        <tr>
                            <th class="sc-th sc-th--sticky sc-col-model">Mobil</th>
                            <th class="sc-th sc-th--sticky sc-col-nopol">Nopol</th>
                            <template x-for="day in scheduleData.daysInMonth">
                                <th class="sc-th sc-th--day" x-text="day"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="car in scheduleData.cars" :key="car.id">
                            <tr class="sc-tr">
                                <td class="sc-td sc-td--sticky sc-col-model">
                                    <span class="sc-car-model" x-text="car.model"></span>
                                </td>
                                <td class="sc-td sc-td--sticky sc-col-nopol">
                                    <span class="sc-nopol" x-text="car.nopol"></span>
                                </td>

                                <template x-for="day in scheduleData.daysInMonth">
                                    <td class="sc-td sc-td--cell"
                                        :class="car.schedule[day] ? 'sc-cell--' + car.schedule[day].status : ''">
                                        <template x-if="car.schedule[day]">
                                            <a :href="`/admin/bookings/${car.schedule[day].booking_id}`"
                                               target="_blank"
                                               class="sc-cell-link">
                                                <span x-text="car.schedule[day].display_text"></span>
                                            </a>
                                        </template>
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

    </div>

    <style>
        /* ── Legend ── */
        .sc-legend {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .sc-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 500;
            color: #78716c;
        }
        .dark .sc-legend-item { color: #a8a29e; }
        .sc-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .sc-dot--booking { background: #f59e0b; }
        .sc-dot--disewa  { background: #22c55e; }
        .sc-dot--selesai { background: #94a3b8; }
        .sc-dot--batal   { background: #f87171; }

        /* ── Scroll wrapper ── */
        .sc-wrap {
            overflow-x: auto;
            overflow-y: auto;
            max-height: calc(100vh - 22rem);
            border-radius: 12px;
            border: 1px solid #e7e5e4;
        }
        .dark .sc-wrap { border-color: #292524; }

        /* ── Table base ── */
        .sc-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        /* ── Header ── */
        .sc-th {
            position: sticky;
            top: 0;
            z-index: 10;
            padding: 10px 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #a8a29e;
            background: #faf9f7;
            border-bottom: 1px solid #e7e5e4;
            white-space: nowrap;
            text-align: center;
        }
        .dark .sc-th {
            background: #1c1917;
            border-color: #292524;
            color: #78716c;
        }

        /* Day number header */
        .sc-th--day {
            min-width: 42px;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
        }
        .dark .sc-th--day { color: #9ca3af; }

        /* Sticky left columns */
        .sc-col-model {
            position: sticky;
            left: 0;
            z-index: 20;
            text-align: left;
            min-width: 130px;
            padding-left: 14px;
        }
        .sc-col-nopol {
            position: sticky;
            left: 130px;
            z-index: 20;
            text-align: left;
            min-width: 96px;
            padding-left: 10px;
            border-right: 1px solid #e7e5e4;
        }
        .dark .sc-col-nopol { border-color: #292524; }

        /* ── Rows ── */
        .sc-tr {
            transition: background .1s;
        }
        .sc-tr:hover .sc-td { background: #f5f4f2 !important; }
        .dark .sc-tr:hover .sc-td { background: #1a1917 !important; }

        /* ── Cells ── */
        .sc-td {
            padding: 0;
            border-bottom: 1px solid #f0eeed;
            border-right: 1px solid #f0eeed;
            font-size: 12px;
            background: #fff;
            vertical-align: middle;
        }
        .dark .sc-td {
            background: #0c0a09;
            border-color: #1c1917;
        }

        /* Sticky data cells */
        .sc-td--sticky { padding: 10px 8px; }

        .sc-td.sc-col-model {
            position: sticky;
            left: 0;
            z-index: 5;
            padding-left: 14px;
        }
        .sc-td.sc-col-nopol {
            position: sticky;
            left: 130px;
            z-index: 5;
            padding-left: 10px;
            border-right: 1px solid #e7e5e4;
        }
        .dark .sc-td.sc-col-nopol { border-right-color: #292524; }

        .sc-car-model {
            font-size: 13px;
            font-weight: 600;
            color: #1c1917;
            white-space: nowrap;
        }
        .dark .sc-car-model { color: #fafaf9; }

        .sc-nopol {
            display: inline-block;
            font-family: ui-monospace, monospace;
            font-size: 11px;
            font-weight: 500;
            background: #f5f4f2;
            color: #78716c;
            border: 1px solid #e7e5e4;
            border-radius: 4px;
            padding: 2px 6px;
            letter-spacing: .04em;
            white-space: nowrap;
        }
        .dark .sc-nopol {
            background: #1c1917;
            color: #a8a29e;
            border-color: #292524;
        }

        /* ── Booking status cells ── */
        .sc-td--cell { text-align: center; }

        .sc-cell--booking { background: #fffbeb !important; }
        .sc-cell--disewa  { background: #f0fdf4 !important; }
        .sc-cell--selesai { background: #f8fafc !important; }
        .sc-cell--batal   { background: #fef2f2 !important; }

        .dark .sc-cell--booking { background: #1c1507 !important; }
        .dark .sc-cell--disewa  { background: #052e16 !important; }
        .dark .sc-cell--selesai { background: #0f172a !important; }
        .dark .sc-cell--batal   { background: #1a0505 !important; }

        .sc-cell-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            min-height: 36px;
            padding: 4px 3px;
            font-size: 10.5px;
            font-weight: 600;
            white-space: nowrap;
            text-decoration: none;
            transition: filter .1s;
        }
        .sc-cell-link:hover { filter: brightness(.88); text-decoration: underline; }

        .sc-cell--booking .sc-cell-link { color: #92400e; }
        .sc-cell--disewa  .sc-cell-link { color: #166534; }
        .sc-cell--selesai .sc-cell-link { color: #475569; }
        .sc-cell--batal   .sc-cell-link { color: #991b1b; }

        .dark .sc-cell--booking .sc-cell-link { color: #fbbf24; }
        .dark .sc-cell--disewa  .sc-cell-link { color: #4ade80; }
        .dark .sc-cell--selesai .sc-cell-link { color: #94a3b8; }
        .dark .sc-cell--batal   .sc-cell-link { color: #f87171; }
    </style>

</x-filament-panels::page>
