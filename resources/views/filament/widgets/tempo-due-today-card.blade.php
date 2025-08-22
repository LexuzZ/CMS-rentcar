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
                    {{-- PERBAIKAN 1: Menggunakan inline style untuk badge nopol --}}
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-md" style="background-color: #4f46e5; color: white;">
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
                        <span class="font-semibold text-gray-900 dark:text-white" style="color: #ef4444;">
                            {{ $perawatanText }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Jatuh Tempo</span>
                        {{-- PERBAIKAN 2: Menggunakan inline style untuk teks jatuh tempo --}}
                        @php
                            $dueDate = \Carbon\Carbon::parse($record->jatuh_tempo);
                            $daysRemaining = now()->diffInDays($dueDate, false); // false agar bisa negatif

                            $color = '';
                            if ($daysRemaining < 0) {
                                $color = '#9B2C2C'; // Merah tua untuk yang sudah lewat
                            } elseif ($daysRemaining <= 7) {
                                $color = '#EF4444'; // Merah untuk 7 hari ke depan
                            } elseif ($daysRemaining <= 30) {
                                $color = '#F59E0B'; // Kuning/Amber untuk 1 bulan ke depan
                            }

                        @endphp

                        <span class="font-bold" style="color: {{ $color }};">
                            {{ $dueDate->locale('id')->diffForHumans() }}
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
