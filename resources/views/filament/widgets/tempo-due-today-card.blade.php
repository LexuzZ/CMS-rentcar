<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($tempos as $record)
        <div class="bg-white p-6 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            {{-- Header Kartu (Nama Mobil & Nopol) --}}
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                    {{ $record->car->carModel->brand->name }} {{ $record->car->carModel->name }}
                </h3>
                <span class="bg-primary-700  text-xs font-semibold px-2.5 py-0.5 rounded-md">
                    {{ $record->car->nopol }}
                </span>
            </div>

            <hr class="my-4 border-gray-200 dark:border-gray-700">

            {{-- Detail Konten --}}
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 dark:text-gray-400">Jenis Perawatan</span>
                    {{-- Logika untuk Badge Perawatan (disesuaikan seperti contoh Anda) --}}
                    @php
                        $perawatan = $record->perawatan;
                        $perawatanText = match($perawatan) {
                            'pajak'   => 'Pajak STNK',
                            'service' => 'Service Berkala',
                            default   => ucfirst($perawatan)
                        };
                        // Menentukan nama warna logis, sama seperti di ->colors()
                        $perawatanColorName = match($perawatan) {
                            'pajak'   => 'info',    // Menggunakan warna 'info' (biru) untuk pajak
                            'service' => 'danger',  // Menggunakan warna 'danger' (merah) untuk service
                            default   => 'gray',
                        };
                    @endphp
                    {{-- Menggunakan kelas tema Filament agar konsisten --}}
                    <x-filament::button
                    color="danger" class="text-xs">
                     {{ $perawatanText }}
                </x-filament::button>
                    
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 dark:text-gray-400">Jatuh Tempo</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($record->jatuh_tempo)->isoFormat('D MMMM YYYY') }}
                        <span class="text-danger-500">(Hari Ini)</span>
                    </span>
                </div>
            </div>
        </div>
        @empty
        {{-- Tampilan jika tidak ada data tempo hari ini --}}
        <div class="col-span-full text-center text-gray-500 dark:text-gray-400">
            Tidak ada jadwal perawatan yang jatuh tempo hari ini.
        </div>
        @endforelse
    </div>
</x-filament-widgets::widget>
