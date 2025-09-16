<x-filament-panels::page>
    <div x-data="{ scheduleData: @entangle('scheduleData') }">
        {{-- Filter Section --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Schedule Table Section --}}
        <x-filament::section class="mt-6">
            {{-- PERUBAHAN UTAMA DI BARIS INI --}}
            <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-22rem)] relative">
                <table class="w-full text-sm border-collapse">
                    {{-- HEADER --}}
                    <thead>
                        <tr>
                            {{-- Kolom Mobil --}}
                            <th class="border p-2 font-semibold text-left bg-gray-100 dark:bg-gray-800 z-30"
                                style="position: sticky; top: 0; left: 0; min-width:150px;">
                                Mobil
                            </th>

                            {{-- Kolom Nopol --}}
                            <th class="border p-2 font-semibold text-left bg-gray-100 dark:bg-gray-800 z-30"
                                style="position: sticky; top: 0; left: 150px; min-width:120px;">
                                Nopol
                            </th>

                            {{-- Kolom tanggal (sticky top saja) --}}
                            <template x-for="day in scheduleData.daysInMonth">
                                <th class="border p-2 font-semibold text-center min-w-[50px] bg-gray-100 dark:bg-gray-800 z-20"
                                    style="position: sticky; top: 0;" x-text="day">
                                </th>
                            </template>
                        </tr>
                    </thead>

                    {{-- BODY --}}
                    <tbody>
                        <template x-for="car in scheduleData.cars" :key="car.id">
                            <tr class="border-t">
                                {{-- Cell Mobil --}}
                                <td class="border p-2 whitespace-nowrap bg-white dark:bg-gray-900 z-20"
                                    style="position: sticky; left: 0; min-width:150px;" x-text="car.model">
                                </td>

                                {{-- Cell Nopol --}}
                                <td class="border p-2 whitespace-nowrap bg-white dark:bg-gray-900 z-20"
                                    style="position: sticky; left: 150px; min-width:120px;" x-text="car.nopol">
                                </td>

                                {{-- Cell tanggal --}}
                                <template x-for="day in scheduleData.daysInMonth">
                                    <td class="border p-0 text-center text-xs"
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
