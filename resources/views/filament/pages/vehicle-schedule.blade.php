<x-filament-panels::page>
    <div x-data="{ scheduleData: @entangle('scheduleData') }">
        {{-- Filter Section --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Schedule Table Section --}}
        <x-filament::section class="mt-6">
            {{--
                KUNCI UTAMA #1: KONTENER SCROLL
                - `overflow-y-auto`: Memunculkan scrollbar vertikal jika konten di dalamnya lebih tinggi dari `max-h`.
                - `overflow-x-auto`: Memunculkan scrollbar horizontal jika konten lebih lebar.
                - `max-h-[calc(100vh-22rem)]`: Menetapkan tinggi maksimum untuk kontainer ini.
                  thead akan menempel pada batas atas dari kontainer ini.
            --}}
            <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-22rem)] border dark:border-gray-700 rounded-lg">
                <table class="w-full text-sm border-collapse">
                    {{--
                        KUNCI UTAMA #2: HEADER STICKY
                        - `sticky top-0`: Membuat seluruh bagian thead menempel di bagian paling atas (top: 0)
                          dari kontainer scrollable terdekat.
                        - `z-10`: Memastikan thead berada di atas tbody saat di-scroll.
                    --}}
                    <thead class="sticky top-0 z-10">
                        {{-- Warna latar belakang ini penting agar konten di bawah tidak tembus --}}
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            {{--
                                KUNCI UTAMA #3: KOLOM STICKY ( Pojok Kiri Atas )
                                - `sticky left-0`: Membuat kolom ini menempel di sisi kiri.
                                - Karena parent `thead` sudah `sticky top-0`, kolom ini akan otomatis
                                  menempel di atas dan di kiri.
                                - `z-20`: z-index harus lebih tinggi dari thead (z-10) dan body td (z-10)
                                  agar selalu berada di paling depan.
                            --}}
                            <th class="border p-2 font-semibold text-left bg-gray-100 dark:bg-gray-800 sticky left-0 z-20"
                                style="min-width: 150px;">Mobil</th>
                            <th class="border p-2 font-semibold text-left bg-gray-100 dark:bg-gray-800 sticky left-0 z-20"
                                style="min-width: 120px;">Nopol</th>

                            {{-- Kolom tanggal biasa (hanya sticky di atas) --}}
                            <template x-for="day in scheduleData.daysInMonth">
                                <th class="border p-2 font-semibold text-center min-w-[50px]" x-text="day"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Sisa kode Anda tidak perlu diubah --}}
                        <template x-for="car in scheduleData.cars" :key="car.id">
                            <tr class="border-t dark:border-gray-700">
                                {{--
                                    KUNCI UTAMA #4: SEL BODY STICKY
                                    - `sticky left-0`: Membuat sel ini menempel di sisi kiri saat scroll horizontal.
                                    - `bg-white dark:bg-gray-900`: Latar belakang solid wajib ada.
                                    - `z-10`: Cukup z-10 agar berada di atas sel body lainnya.
                                --}}
                                <td class="border p-2 whitespace-nowrap bg-white dark:bg-gray-900 sticky left-0 z-10"
                                    x-text="car.model"></td>
                                <td class="border p-2 whitespace-nowrap bg-white dark:bg-gray-900 sticky left-0 z-10"
                                    x-text="car.nopol"></td>

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
