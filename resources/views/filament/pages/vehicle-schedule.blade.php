<x-filament-panels::page>
    <div x-data="{ scheduleData: @entangle('scheduleData') }">
        {{-- Filter Section --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Schedule Table Section --}}
        <x-filament::section class="mt-6">
            {{-- PERUBAHAN UTAMA DI BARIS INI --}}
            <!-- wrapper scroll -->
            <div class="overflow-auto max-h-[calc(100vh-22rem)] relative">
                <style>
                    /* sesuaikan lebar kolom kiri di sini */
                    :root {
                        --col1-width: 220px;
                        /* lebar kolom "Mobil" */
                        --col2-width: 130px;
                        /* lebar kolom "Nopol" */
                    }

                    /* header tanggal: sticky vertikal (top) */
                    .thead-sticky {
                        position: sticky;
                        top: 0;
                        z-index: 50;
                        /* di atas kolom kiri */
                        background: #f7fafc;
                        /* atau sesuaikan */
                    }

                    /* kolom kiri: sticky horizontal (left) — jangan beri top */
                    .sticky-left-1 {
                        position: sticky;
                        left: 0;
                        z-index: 40;
                        /* di bawah header tanggal */
                        background: #ffffff;
                        /* agar tidak transparan */
                    }

                    .sticky-left-2 {
                        position: sticky;
                        left: calc(var(--col1-width));
                        z-index: 40;
                        background: #ffffff;
                    }

                    /* lebar minimum agar left offsets pas */
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

                    /* agar tanggal tidak terlalu sempit */
                    .day-col {
                        min-width: 50px;
                        max-width: 70px;
                    }
                </style>

                <table class="w-full text-sm border-collapse table-auto">
                    <thead>
                        <tr>
                            {{-- Mobil (sticky horizontal only) --}}
                            <th class="border p-2 font-semibold text-left sticky-left-1 col1">
                                Mobil
                            </th>

                            {{-- Nopol (sticky horizontal only) --}}
                            <th class="border p-2 font-semibold text-left sticky-left-2 col2">
                                Nopol
                            </th>

                            {{-- Tanggal (sticky vertical only) --}}
                            <template x-for="day in scheduleData.daysInMonth">
                                <th class="border p-2 font-semibold text-center thead-sticky day-col" x-text="day"></th>
                            </template>
                        </tr>
                    </thead>

                    <tbody>
                        <template x-for="car in scheduleData.cars" :key="car.id">
                            <tr class="border-t">
                                {{-- cell Mobil: sticky horizontal, no top --}}
                                <td class="border p-2 whitespace-nowrap sticky-left-1 col1" x-text="car.model"></td>

                                {{-- cell Nopol: sticky horizontal, left offset = width kolom 1 --}}
                                <td class="border p-2 whitespace-nowrap sticky-left-2 col2" x-text="car.nopol"></td>

                                {{-- cell tanggal --}}
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
