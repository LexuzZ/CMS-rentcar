<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($bookings as $record)
            <div class="bg-white p-6 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                {{-- Header Kartu --}}
                <div class="flex items-start justify-between">
                    <div>
                        {{-- Menampilkan Merek & Model Mobil --}}
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ $record->car->carModel->brand->name ?? '' }}
                            {{ $record->car->carModel->name ?? '' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $record->car->nopol ?? '-' }}
                        </p>
                        <span class="text-xs font-medium py-0.5 rounded-full">
                            {{-- Hari keluar: Hari ini atau Besok --}}
                            @php
                                $today = \Carbon\Carbon::today();
                                $tomorrow = \Carbon\Carbon::tomorrow();
                                $tglKeluar = \Carbon\Carbon::parse($record->tanggal_kembali);

                                $labelHari = match (true) {
                                    $tglKeluar->isSameDay($today) => 'Mobil Kembali Hari Ini',
                                    $tglKeluar->isSameDay($tomorrow) => 'Mobil Kembali Besok',
                                    default => 'Mobil Keluar'
                                };
                            @endphp
                            {{ $labelHari }}
                        </span>
                    </div>

                    {{-- Badge Status --}}
                    @php
                        $status = $record->status;
                        $statusText = match($status) {
                            'booking' => 'Booking',
                            default => ucfirst($status)
                        };
                    @endphp
                    <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-primary-500 text-white">
                        {{ $statusText }}
                    </span>
                </div>

                <hr class="my-4 border-gray-200 dark:border-gray-700">

                {{-- Detail Konten --}}
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Penyewa</span>
                        <span class="font-medium text-gray-900 dark:text-white text-xs">{{ $record->customer->nama ?? 'N/A' }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 text-xs">No. Telepon</span>
                        <span class="text-xs text-gray-900 dark:text-white font-semibold">{{ $record->customer->no_telp ?? 'N/A' }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Lokasi Pengantaran</span>
                        <span class="text-xs text-gray-900 dark:text-white font-semibold">{{ $record->lokasi_pengantaran ?? 'N/A' }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Vendor</span>
                        <span class="text-xs text-gray-900 dark:text-white font-semibold">{{ $record->car->garasi ?? 'N/A' }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Staff</span>
                        <span class="font-medium bg-green-500 text-gray-900 dark:text-white text-xs">
                            {{ $record->driver->nama ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Jadwal Kembali</span>
                        <div class="text-right">
                            <p class="font-semibold text-xs">
                                Pukul {{ \Carbon\Carbon::parse($record->waktu_kembali)->format('H:i') }} WITA
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400 text-xs">Tanggal Kembali</span>
                        <div class="text-right">
                            <p class="font-semibold text-xs">
                                {{ \Carbon\Carbon::parse($record->tanggal_kembali)->isoFormat('dddd, D MMMM Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-6 flex items-center justify-end">
                    <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.bookings.edit', ['record' => $record->id]) }}"
                    target="_blank"
                    icon="heroicon-o-check-circle"
                    color="success">
                    Selesaikan
                </x-filament::button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 dark:text-gray-400">
                Tidak ada mobil yang dijadwalkan kembali hari ini atau besok.
            </div>
        @endforelse
    </div>
</x-filament-widgets::widget>
