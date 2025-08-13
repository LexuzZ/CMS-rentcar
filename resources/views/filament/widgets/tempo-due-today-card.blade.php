<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($tempos as $record)
            <div class="bg-white p-6 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                {{-- Header Kartu (Nama Mobil & Nopol) --}}
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        {{-- Menampilkan nama model mobil dari relasi --}}
                        @if ($record->car && $record->car->carModel)
                            {{ $record->car->carModel->name }}
                        @else
                            Mobil Telah Dihapus
                        @endif
                    </h3>
                    <span class="bg-primary-500 text-white text-xs font-semibold px-2.5 py-0.5 rounded-md">
                        {{ $record->car->nopol ?? 'N/A' }}
                    </span>
                </div>

                <hr class="my-4 border-gray-200 dark:border-gray-700">

                {{-- Detail Konten --}}
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Jenis Perawatan</span>
                        @php
                            $perawatanText = match($record->perawatan) {
                                'pajak'   => 'Pajak STNK',
                                'service' => 'Service Berkala',
                                default   => ucfirst($record->perawatan)
                            };
                        @endphp
                        <span class="font-medium text-gray-900 dark:text-white">
                            {{ $perawatanText }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Jatuh Tempo</span>
                        {{-- Menggunakan Carbon::diffForHumans() untuk sisa waktu --}}
                        <span class="font-semibold text-danger-500">
                            {{ \Carbon\Carbon::parse($record->jatuh_tempo)->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            {{-- Tampilan jika tidak ada data tempo yang akan datang --}}
            <div class="col-span-full text-center text-gray-500 dark:text-gray-400">
                Tidak ada jadwal perawatan mendatang.
            </div>
        @endforelse
    </div>
</x-filament-widgets::widget>
