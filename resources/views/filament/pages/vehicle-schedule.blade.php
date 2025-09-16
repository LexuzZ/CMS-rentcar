<x-filament-panels::page>
    <div x-data="{ scheduleData: @entangle('scheduleData') }">
        {{-- Filter Section --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Schedule Table Section --}}
        <x-filament::section class="mt-6">
            <!-- wrapper scroll dua arah -->
            <div class="overflow-auto max-h-[calc(100vh-22rem)] relative">
                <style>
                    :root {
                        --col1-width: 220px;
                        --col2-width: 130px;
                    }
                    /* header tanggal + header Mobil/Nopol: sticky top */
                    th.thead-sticky {
                        position: sticky;
                        top: 0;
                        background: #f7fafc; /* ganti sesuai theme */
                        z-index: 60;
                    }
                    /* kolom Mobil & Nopol: sticky horizontal */
                    td.sticky-left-1, th.sticky-left-1 {
                        position: sticky;
                        left: 0;
                        background: #fff;
                        z-index: 50;
                    }
                    td.sticky-left-2, th.sticky-left-2 {
                        position: sticky;
                        left: var(--col1-width);
                        background: #fff;
                        z-index: 50;
                    }
                    .col1 {
                        min-width: var(--col1-width);
                        max-width: var(--col1-width);
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .col2 {
                        min-width: var(--col2-width);
                        max-width: var(--col2-width);
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .day-col {
                        min-width: 50px;
                        max-width: 70px;
                    }
                </style>

                <table class="w-full text-sm border-collapse table-auto">
                    <thead>
                        <tr>
                            <th class="border p-2 font-semibold text-left sticky-left-1 col1 thead-sticky">
                                Mobil
                            </th>
                            <th class="border p-2 font-semibold text-left sticky-left-2 col2 thead-sticky">
                                Nopol
                            </th>

                            <template x-for="day in scheduleData.daysInMonth">
                                <th class="border p-2 font-semibold text-center thead-sticky day-col" x-text="day"></th>
                            </template>
                        </tr>
                    </thead>

                    <tbody>
                        <template x-for="car in scheduleData.cars" :key="car.id">
                            <tr class="border-t">
                                <td class="border p-2 whitespace-nowrap sticky-left-1 col1">
                                    <span x-text="car.model"></span>
                                </td>
                                <td class="border p-2 whitespace-nowrap sticky-left-2 col2">
                                    <span x-text="car.nopol"></span>
                                </td>

                                <template x-for="day in scheduleData.daysInMonth">
                                    <td class="border p-0 text-center text-xs day-col"
                                        :style="car.schedule[day] ? {
                                            'booking': 'background-color: #fed7d7;',
                                            'disewa': 'background-color: #c6f6d5;',
                                            'selesai': 'background-color: #e2e8f0;',
                                            'batal': 'background-color: #fed7d7;'
                                        } [car.schedule[day].status] || '' : ''">
                                        <template x-if="car.schedule[day]">
                                            <a :href="`/admin/bookings/${car.schedule[day].booking_id}`" target="_blank"
                                               class="w-full h-full flex items-center justify-center p-1 hover:underline"
                                               :style="car.schedule[day] ? {
                                                   'booking': 'color: #9b2c2c;',
                                                   'disewa': 'color: #22543d;',
                                                   'selesai': 'color: #4a5568;',
                                                   'batal': 'color: #9b2c2c;'
                                               } [car.schedule[day].status] || '' : ''">
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
</x-filament-panels::page>
