<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            @foreach ($announcements as $announcement)
                @php
                    // Logika untuk menentukan warna berdasarkan tipe
                    $colors = match ($announcement['type']) {
                        'warning' => 'bg-yellow-50 text-yellow-800 dark:bg-yellow-800/20 dark:text-yellow-400',
                        'danger' => 'bg-red-50 text-red-800 dark:bg-red-800/20 dark:text-red-400',
                        'success' => 'bg-green-50 text-green-800 dark:bg-green-800/20 dark:text-green-400',
                        default => 'bg-blue-50 text-blue-800 dark:bg-blue-800/20 dark:text-blue-400',
                    };
                    $icon = match ($announcement['type']) {
                        'warning' => 'heroicon-o-exclamation-triangle',
                        'danger' => 'heroicon-o-x-circle',
                        'success' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-information-circle',
                    };
                @endphp
                <div class="p-4 rounded-lg {{ $colors }}">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-dynamic-component :component="$icon" class="h-5 w-5" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium">{{ $announcement['title'] }}</h3>
                            <div class="mt-2 text-sm">
                                {{-- PERUBAHAN DI SINI: Menggunakan daftar berpoin --}}
                                <ul class="list-disc space-y-1 pl-5">
                                    <li>Pastikan kondisi mobil selalu siap pakai.</li>
                                    <li>Semua update data wajib dilakukan di aplikasi secara <strong>real-time</strong>.</li>
                                    <li>Semua dokumentasi (foto mobil) wajib diunggah ke aplikasi.</li>
                                    <li>Tidak diperkenankan meminjamkan mobil di luar sistem aplikasi.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
