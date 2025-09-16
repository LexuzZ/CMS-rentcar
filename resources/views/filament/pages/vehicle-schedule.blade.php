<x-filament-panels::page>
    <div x-data="{ scheduleData: @entangle('scheduleData') }">
        {{-- Filter Section --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Schedule Table Section --}}
        <x-filament::section class="mt-6">
            {{-- Container untuk scrolling vertikal dan horizontal --}}
            <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-22rem)]">
                <table class="w-full text-sm border-collapse">
                    {{--
                        MODIFIKASI:
                        - Thead dibuat sticky dengan `top: 0` dan z-index yang lebih tinggi (z-20)
                          agar selalu berada di atas body tabel saat scroll vertikal.
                    --}}
                    <thead class="sticky top-0 z-20">
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            {{--
                                MODIFIKASI:
                                - Kolom "Mobil" dibuat sticky dengan `left: 0`.
                                - Diberi `min-width` agar lebarnya konsisten.
                                - z-index tertinggi (z-30) agar berada di pojok kiri atas.
                            --}}
                            <th class="border p-2 font-semibold text-left bg-gray-100 dark:bg-gray-800 z-30"
                                style="position: sticky; left: 0; min-width: 160px;">Mobil</th>

                            {{--
                                MODIFIKASI KUNCI:
                                - Kolom "Nopol" dibuat sticky.
                                - `left: 160px;` adalah kuncinya. Nilai ini harus sama dengan `min-width` kolom pertama ("Mobil").
                                - z-index tertinggi (z-30) agar header tetap di atas.
                            --}}
                            <th class="border p-2 font-semibold text-left bg-gray-100 dark:bg-gray-800 z-30"
                                style="position: sticky; left: 160px; min-width: 120px;">Nopol</th>

                            <template x-for="day in scheduleData.daysInMonth">
                                <th class="border p-2 font-semibold text-center min-w-[50px]" x-text="day"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="car in scheduleData.cars" :key="car.id">
                            <tr class="border-t">
                                {{--
                                    MODIFIKASI:
                                    - Sel data "Mobil" dibuat sticky dengan `left: 0`.
                                    - Diberi background agar konten di belakangnya tidak tembus.
                                    - z-index (z-10) lebih rendah dari header tapi lebih tinggi dari sel biasa.
                                --}}
                                <td class="border p-2 whitespace-nowrap bg-white dark:bg-gray-900 z-10"
                                    style="position: sticky; left: 0;" x-text="car.model"></td>

                                {{--
                                    MODIFIKASI KUNCI:
                                    - Sel data "Nopol" dibuat sticky.
                                    - `left: 160px;` sama seperti di header, menggesernya ke sebelah kolom pertama.
                                --}}
                                <td class="border p-2 whitespace-nowrap bg-white dark:bg-gray-900 z-10"
                                     style="position: sticky; left: 160px;" x-text="car.nopol"></td>

                                {{-- Sisa kode tidak perlu diubah --}}
                                <template x-for="day in scheduleData.daysInMonth">
                                    <td class="border p-0 text-center text-xs"
                                        :style="car.schedule[day] ? {
                                            'booking': 'background-color: #fed7d7;',
                                            'disewa': 'background-color: #c6f6d5;',
                                            'selesai': 'background-color: #e2e8f0;',
                                            'batal': 'background-color: #fed7d7;'
                                        }[car.schedule[day].status] || '' : ''">
                                        <template x-if="car.schedule[day]">
                                            <a :href="`/admin/bookings/${car.schedule[day].booking_id}`" target="_blank"
                                                class="w-full h-full flex items-center justify-center p-1 hover:underline"
                                                :style="car.schedule[day] ? {
                                                    'booking': 'color: #9b2c2c;',
                                                    'disewa': 'color: #22543d;',
                                                    'selesai': 'color: #4a5568;',
                                                    'batal': 'color: #9b2c2c;'
                                                }[car.schedule[day].status] || '' : ''">
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
