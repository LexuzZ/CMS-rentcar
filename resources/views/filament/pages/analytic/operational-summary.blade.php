<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Section --}}
        <x-filament::section class="shadow-sm">
            {{ $this->form }}
        </x-filament::section>

        {{-- Statistics Cards --}}
         <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach ($statistics as $stat)
                <x-filament::section
                    class="relative overflow-hidden transition-all duration-200 hover:shadow-md dark:bg-gray-800/50"
                >
                    {{-- Accent bar --}}
                    <div
                        class="absolute inset-x-0 top-0 h-1.5
                        {{ $stat['color'] === 'success'
                            ? 'bg-gradient-to-r from-emerald-500 to-emerald-400'
                            : ($stat['color'] === 'warning'
                                ? 'bg-gradient-to-r from-amber-500 to-amber-400'
                                : 'bg-gradient-to-r from-rose-500 to-rose-400') }}"
                    ></div>

                    <div class="flex items-start justify-between pt-3">
                        <div class="space-y-2">
                            <p class="text-xs font-medium tracking-wide text-gray-500 dark:text-gray-400 uppercase">
                                {{ $stat['label'] }}
                            </p>

                            <p class="text-2xl font-bold text-gray-900 dark:text-white tabular-nums">
                                Rp {{ number_format($stat['value'], 0, ',', '.') }}
                            </p>

                            <div class="pt-1">
                                @php
                                    $isPositive = $stat['change'] >= 0;
                                    $icon = $isPositive
                                        ? 'heroicon-m-arrow-trending-up'
                                        : 'heroicon-m-arrow-trending-down';
                                @endphp

                                <x-filament::badge
                                    :color="$stat['color']"
                                    size="sm"
                                    class="inline-flex items-center gap-1.5 font-medium"
                                >
                                    <x-filament::icon
                                        :icon="$icon"
                                        class="w-3.5 h-3.5"
                                    />
                                    {{ number_format($stat['change'], 1) }}%
                                </x-filament::badge>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                            <x-filament::icon
                                :icon="$stat['icon']"
                                class="w-6 h-6 text-gray-400 dark:text-gray-300"
                            />
                        </div>
                    </div>
                </x-filament::section>
            @endforeach
        </div>

        {{-- Ringkasan Section --}}
        <x-filament::section
            class="mt-8 shadow-sm border-t-4 border-primary-500 dark:border-primary-600"
        >
            <x-slot name="heading">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full gap-4">
                    <div class="space-y-1">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            Ringkasan Operasional
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Periode {{ $reportTitle }}
                        </p>
                    </div>

                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-printer"
                        wire:click="downloadPdf"
                        class="shrink-0"
                        size="sm"
                    >
                        Export PDF
                    </x-filament::button>
                </div>
            </x-slot>

            <div class="space-y-2">
                {{-- Pendapatan Table --}}
                <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-2 py-2">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            Rincian Pendapatan
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($rincianTableData as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white min-w-[200px]">
                                        {{ $row['label'] }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium tabular-nums dark:text-white whitespace-nowrap">
                                        @if (is_numeric($row['value']))
                                            Rp {{ number_format($row['value'], 0, ',', '.') }}
                                        @else
                                            {{ $row['value'] }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if (!is_null($row['change']))
                                            @php
                                                $isPositive = $row['change'] >= 0;
                                                $badgeColor = $isPositive ? 'success' : 'danger';
                                                $icon = $isPositive
                                                    ? 'heroicon-m-arrow-trending-up'
                                                    : 'heroicon-m-arrow-trending-down';
                                            @endphp
                                            <x-filament::badge
                                                :color="$badgeColor"
                                                class="inline-flex items-center gap-1.5 px-2 py-1 text-xs"
                                            >
                                                <x-filament::icon :icon="$icon" class="w-3.5 h-3.5" />
                                                {{ number_format($row['change'], 1) }}%
                                            </x-filament::badge>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @foreach ($costRentTableData as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $row['label'] }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium tabular-nums dark:text-white">
                                        @if (is_numeric($row['value']))
                                            Rp {{ number_format($row['value'], 0, ',', '.') }}
                                        @else
                                            {{ $row['value'] }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if (!is_null($row['change']))
                                            @php
                                                $isPositive = $row['change'] <= 0;
                                                $badgeColor = $isPositive ? 'success' : 'danger';
                                                $icon = $isPositive
                                                    ? 'heroicon-m-arrow-trending-up'
                                                    : 'heroicon-m-arrow-trending-down';
                                            @endphp
                                            <x-filament::badge
                                                :color="$badgeColor"
                                                class="inline-flex items-center gap-1.5 px-2 py-1 text-xs"
                                            >
                                                <x-filament::icon :icon="$icon" class="w-3.5 h-3.5" />
                                                {{ number_format($row['change'], 1) }}%
                                            </x-filament::badge>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pengeluaran Table --}}
                <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-6 py-3">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            Rincian Pengeluaran
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($rincianCostTableData as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $row['label'] }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium tabular-nums dark:text-white">
                                        @if (is_numeric($row['value']))
                                            Rp {{ number_format($row['value'], 0, ',', '.') }}
                                        @else
                                            {{ $row['value'] }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if (!is_null($row['change']))
                                            @php
                                                $isPositive = $row['change'] <= 0;
                                                $badgeColor = $isPositive ? 'success' : 'danger';
                                                $icon = $isPositive
                                                    ? 'heroicon-m-arrow-trending-up'
                                                    : 'heroicon-m-arrow-trending-down';
                                            @endphp
                                            <x-filament::badge
                                                :color="$badgeColor"
                                                class="inline-flex items-center gap-1.5 px-2 py-1 text-xs"
                                            >
                                                <x-filament::icon :icon="$icon" class="w-3.5 h-3.5" />
                                                {{ number_format($row['change'], 1) }}%
                                            </x-filament::badge>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Total Table --}}
                <div class="rounded-lg overflow-hidden border-2 border-gray-300 dark:border-gray-600 shadow-lg">
                    <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/30 px-6 py-3">
                        <h3 class="font-semibold text-gray-800 dark:text-white">
                            Total
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($costTableData as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $row['label'] }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium tabular-nums dark:text-white">
                                        @if (is_numeric($row['value']))
                                            Rp {{ number_format($row['value'], 0, ',', '.') }}
                                        @else
                                            {{ $row['value'] }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if (!is_null($row['change']))
                                            @php
                                                $isPositive = $row['change'] <= 0;
                                                $badgeColor = $isPositive ? 'success' : 'danger';
                                                $icon = $isPositive
                                                    ? 'heroicon-m-arrow-trending-up'
                                                    : 'heroicon-m-arrow-trending-down';
                                            @endphp
                                            <x-filament::badge
                                                :color="$badgeColor"
                                                class="inline-flex items-center gap-1.5 px-2 py-1 text-xs"
                                            >
                                                <x-filament::icon :icon="$icon" class="w-3.5 h-3.5" />
                                                {{ number_format($row['change'], 1) }}%
                                            </x-filament::badge>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @foreach ($summaryTableData as $row)
                                <tr class="bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                        {{ $row['label'] }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-primary-600 dark:text-primary-400 tabular-nums">
                                        @if (is_numeric($row['value']))
                                            Rp {{ number_format($row['value'], 0, ',', '.') }}
                                        @else
                                            {{ $row['value'] }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if (!is_null($row['change']))
                                            @php
                                                $isPositive = $row['change'] >= 0;
                                                $badgeColor = $isPositive ? 'success' : 'danger';
                                                $icon = $isPositive
                                                    ? 'heroicon-m-arrow-trending-up'
                                                    : 'heroicon-m-arrow-trending-down';
                                            @endphp
                                            <x-filament::badge
                                                :color="$badgeColor"
                                                class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-bold"
                                            >
                                                <x-filament::icon :icon="$icon" class="w-3.5 h-3.5" />
                                                {{ number_format($row['change'], 1) }}%
                                            </x-filament::badge>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
