<x-filament-panels::page>
    <div class="space-y-8">

        {{-- ================= FILTER ================= --}}
        <x-filament::section class="shadow-sm">
            {{ $this->form }}
        </x-filament::section>

        {{-- ================= STATISTICS ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-3">
            @foreach ($statistics as $stat)
                <x-filament::section class="relative overflow-hidden transition hover:shadow-md">
                    {{-- Accent --}}
                    <div class="absolute inset-x-0 top-0 h-1
                        {{ $stat['color'] === 'success'
                            ? 'bg-emerald-500'
                            : ($stat['color'] === 'warning'
                                ? 'bg-amber-500'
                                : 'bg-rose-500') }}">
                    </div>

                    <div class="pt-4 space-y-2">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ $stat['label'] }}
                        </p>

                        <p class="text-xl font-bold tabular-nums text-gray-900 dark:text-white">
                            Rp {{ number_format($stat['value'], 0, ',', '.') }}
                        </p>

                        @php
                            $isPositive = $stat['change'] >= 0;
                            $icon = $isPositive
                                ? 'heroicon-m-arrow-trending-up'
                                : 'heroicon-m-arrow-trending-down';
                        @endphp

                        <x-filament::badge :color="$stat['color']" size="sm" class="inline-flex gap-1">
                            <x-filament::icon :icon="$icon" class="w-3.5 h-3.5" />
                            {{ number_format($stat['change'], 1) }}%
                        </x-filament::badge>
                    </div>
                </x-filament::section>
            @endforeach
        </div>

        {{-- ================= RINGKASAN ================= --}}
        <x-filament::section class="border-t-4 border-primary-500">
            <x-slot name="heading">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            Ringkasan Operasional
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Periode {{ $reportTitle }}
                        </p>
                    </div>

                    <x-filament::button
                        color="gray"
                        size="sm"
                        icon="heroicon-o-printer"
                        wire:click="downloadPdf">
                        Export PDF
                    </x-filament::button>
                </div>
            </x-slot>

            <div class="space-y-6">

                {{-- ================= INCOME ================= --}}
                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-3 bg-emerald-50 dark:bg-emerald-900/30 font-semibold text-emerald-700 dark:text-emerald-300">
                        Rincian Pendapatan
                    </div>

                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($rincianTableData as $row)
                            <tr class="transition hover:bg-emerald-50 dark:hover:bg-emerald-900/30">
                                <td class="px-6 py-4 font-medium text-emerald-700 dark:text-emerald-400">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold tabular-nums text-emerald-700 dark:text-emerald-400">
                                    Rp {{ number_format($row['value'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <x-filament::badge color="success" size="sm">
                                        {{ number_format($row['change'], 1) }}%
                                    </x-filament::badge>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                {{-- ================= EXPENSE ================= --}}
                <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-3 bg-rose-50 dark:bg-rose-900/30 font-semibold text-rose-700 dark:text-rose-300">
                        Rincian Pengeluaran
                    </div>

                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($rincianCostTableData as $row)
                            <tr class="transition hover:bg-rose-50 dark:hover:bg-rose-900/30">
                                <td class="px-6 py-4 font-medium text-rose-700 dark:text-rose-400">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold tabular-nums text-rose-700 dark:text-rose-400">
                                    Rp {{ number_format($row['value'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <x-filament::badge color="danger" size="sm">
                                        {{ number_format($row['change'], 1) }}%
                                    </x-filament::badge>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                {{-- ================= TOTAL / SUMMARY ================= --}}
                <div class="rounded-xl overflow-hidden border-2 border-primary-500">
                    <div class="px-6 py-3 bg-primary-50 dark:bg-primary-900/30 font-bold text-primary-700 dark:text-primary-300">
                        Total
                    </div>

                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($summaryTableData as $row)
                            <tr class="transition hover:bg-primary-100 dark:hover:bg-primary-900/50">
                                <td class="px-6 py-4 font-bold text-primary-700 dark:text-primary-300">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold tabular-nums text-primary-700 dark:text-primary-300">
                                    Rp {{ number_format($row['value'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <x-filament::badge
                                        :color="$row['change'] >= 0 ? 'success' : 'danger'"
                                        size="sm"
                                        class="font-bold">
                                        {{ number_format($row['change'], 1) }}%
                                    </x-filament::badge>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
