<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-danger-50 dark:bg-danger-950">
                    <x-heroicon-s-exclamation-triangle class="w-4 h-4 text-danger-500" />
                </div>
                <div>
                    <span class="text-base font-semibold text-gray-900 dark:text-white">Tugas Terlambat</span>
                    <p class="text-xs text-gray-500 font-normal">Booking yang melewati jadwal dan perlu segera ditindaklanjuti</p>
                </div>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Kolom Terlambat Pick Up --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <x-heroicon-o-truck class="w-4 h-4 text-warning-500" />
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Terlambat Pick Up</h3>
                    <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full bg-warning-50 text-warning-600 dark:bg-warning-950 dark:text-warning-400">
                        {{ $overduePickups->count() }} booking
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse ($overduePickups as $booking)
                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-colors duration-150">
                            <div class="flex justify-between items-start gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $booking->car->carModel->name }}
                                        <span class="font-mono text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded px-1.5 py-0.5 ml-1">
                                            {{ $booking->car->nopol }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                        <x-heroicon-o-user class="w-3 h-3" />
                                        {{ $booking->customer->nama }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs font-semibold text-danger-600 dark:text-danger-400">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->diffForHumans() }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            @if($canPerformActions)
                                <div class="border-t border-gray-100 dark:border-gray-800 mt-3 pt-3 flex justify-end">
                                    <x-filament::button
                                        wire:click="pickupOverdue({{ $booking->id }})"
                                        color="danger"
                                        size="xs"
                                        icon="heroicon-o-check">
                                        Pick Up
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 px-4 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                            <x-heroicon-o-check-circle class="w-6 h-6 text-gray-300 dark:text-gray-600 mb-2" />
                            <p class="text-sm text-gray-400 dark:text-gray-500">Tidak ada jadwal pick up yang terlewat.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Kolom Terlambat Selesaikan --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <x-heroicon-o-arrow-path class="w-4 h-4 text-danger-500" />
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Terlambat Selesaikan</h3>
                    <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full bg-danger-50 text-danger-600 dark:bg-danger-950 dark:text-danger-400">
                        {{ $overdueReturns->count() }} booking
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse ($overdueReturns as $booking)
                        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-colors duration-150">
                            <div class="flex justify-between items-start gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $booking->car->carModel->name }}
                                        <span class="font-mono text-xs bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded px-1.5 py-0.5 ml-1">
                                            {{ $booking->car->nopol }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                        <x-heroicon-o-user class="w-3 h-3" />
                                        {{ $booking->customer->nama }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs font-semibold text-danger-600 dark:text-danger-400">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->diffForHumans() }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            @if($canPerformActions)
                                <div class="border-t border-gray-100 dark:border-gray-800 mt-3 pt-3 flex justify-end">
                                    <x-filament::button
                                        wire:click="returnOverdue({{ $booking->id }})"
                                        color="danger"
                                        size="xs"
                                        icon="heroicon-o-check">
                                        Selesaikan
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 px-4 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                            <x-heroicon-o-check-circle class="w-6 h-6 text-gray-300 dark:text-gray-600 mb-2" />
                            <p class="text-sm text-gray-400 dark:text-gray-500">Tidak ada jadwal pengembalian yang terlewat.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
