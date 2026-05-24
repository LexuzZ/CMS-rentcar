<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-danger-50 dark:bg-danger-950 shrink-0">
                    <x-heroicon-s-exclamation-triangle class="w-4 h-4 text-danger-500" />
                </div>
                <div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white tracking-tight">Tugas Terlambat</span>
                    <p class="text-xs text-gray-400 dark:text-gray-500 font-normal mt-0.5">Booking yang melewati jadwal dan perlu segera ditindaklanjuti</p>
                </div>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-100 dark:divide-gray-800 -mx-6 -mb-6">

            {{-- Kolom Terlambat Pick Up --}}
            <div class="px-6 py-5">
                <div class="flex items-center gap-2 mb-4">
                    <x-heroicon-o-truck class="w-4 h-4 text-warning-500 shrink-0" />
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Terlambat Pick Up</h3>
                    <span class="ml-auto inline-flex items-center text-[11px] font-semibold px-2 py-0.5 rounded-full bg-warning-50 text-warning-700 dark:bg-warning-950 dark:text-warning-400 ring-1 ring-warning-200 dark:ring-warning-800">
                        {{ $overduePickups->count() }}
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse ($overduePickups as $booking)
                        <div class="relative bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden hover:border-gray-300 dark:hover:border-gray-700 hover:shadow-sm transition-all duration-150">
                            {{-- Left accent bar --}}
                            <div class="absolute inset-y-0 left-0 w-[3px] bg-warning-400 dark:bg-warning-500 rounded-l-xl"></div>

                            <div class="pl-4 pr-4 pt-3.5 pb-3">
                                <div class="flex justify-between items-start gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5 flex-wrap">
                                            {{ $booking->car->carModel->name }}
                                            <span class="font-mono text-[11px] bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-md px-1.5 py-px tracking-wide">
                                                {{ $booking->car->nopol }}
                                            </span>
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 flex items-center gap-1">
                                            <x-heroicon-o-user class="w-3 h-3 shrink-0" />
                                            {{ $booking->customer->nama }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-semibold text-danger-600 dark:text-danger-400">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->diffForHumans() }}
                                        </p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->isoFormat('D MMM YYYY') }}
                                        </p>
                                    </div>
                                </div>

                                @if($canPerformActions)
                                    <div class="border-t border-gray-100 dark:border-gray-800 mt-3 pt-2.5 flex justify-end">
                                        <x-filament::button
                                            wire:click="pickupOverdue({{ $booking->id }})"
                                            color="warning"
                                            size="xs"
                                            icon="heroicon-o-check-circle"
                                            outlined>
                                            Pick Up
                                        </x-filament::button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 px-4 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-800/30">
                            <div class="w-8 h-8 rounded-full bg-success-50 dark:bg-success-950 flex items-center justify-center mb-2">
                                <x-heroicon-o-check-circle class="w-4 h-4 text-success-500" />
                            </div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Semua jadwal pick up on time</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Tidak ada yang terlewat</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Kolom Terlambat Selesaikan --}}
            <div class="px-6 py-5">
                <div class="flex items-center gap-2 mb-4">
                    <x-heroicon-o-arrow-path class="w-4 h-4 text-danger-500 shrink-0" />
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Terlambat Selesaikan</h3>
                    <span class="ml-auto inline-flex items-center text-[11px] font-semibold px-2 py-0.5 rounded-full bg-danger-50 text-danger-700 dark:bg-danger-950 dark:text-danger-400 ring-1 ring-danger-200 dark:ring-danger-800">
                        {{ $overdueReturns->count() }}
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse ($overdueReturns as $booking)
                        <div class="relative bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden hover:border-gray-300 dark:hover:border-gray-700 hover:shadow-sm transition-all duration-150">
                            {{-- Left accent bar --}}
                            <div class="absolute inset-y-0 left-0 w-[3px] bg-danger-500 rounded-l-xl"></div>

                            <div class="pl-4 pr-4 pt-3.5 pb-3">
                                <div class="flex justify-between items-start gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-1.5 flex-wrap">
                                            {{ $booking->car->carModel->name }}
                                            <span class="font-mono text-[11px] bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-md px-1.5 py-px tracking-wide">
                                                {{ $booking->car->nopol }}
                                            </span>
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 flex items-center gap-1">
                                            <x-heroicon-o-user class="w-3 h-3 shrink-0" />
                                            {{ $booking->customer->nama }}
                                        </p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-semibold text-danger-600 dark:text-danger-400">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->diffForHumans() }}
                                        </p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->isoFormat('D MMM YYYY') }}
                                        </p>
                                    </div>
                                </div>

                                @if($canPerformActions)
                                    <div class="border-t border-gray-100 dark:border-gray-800 mt-3 pt-2.5 flex justify-end">
                                        <x-filament::button
                                            wire:click="returnOverdue({{ $booking->id }})"
                                            color="danger"
                                            size="xs"
                                            icon="heroicon-o-check-circle"
                                            outlined>
                                            Selesaikan
                                        </x-filament::button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 px-4 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-800/30">
                            <div class="w-8 h-8 rounded-full bg-success-50 dark:bg-success-950 flex items-center justify-center mb-2">
                                <x-heroicon-o-check-circle class="w-4 h-4 text-success-500" />
                            </div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Semua pengembalian tepat waktu</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Tidak ada yang terlewat</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
