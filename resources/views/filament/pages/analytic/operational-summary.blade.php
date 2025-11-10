<x-filament-panels::page>
    <div>
        {{-- Filter Section --}}

        <x-filament::section>
            {{ $this->form }}

        </x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2 mt-6">
            @foreach ($statistics as $stat)
                <x-filament::section class="relative overflow-hidden">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <x-filament::icon
                                    :icon="$stat['icon']"
                                    class="w-5 h-5 text-gray-400"
                                />
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ $stat['label'] }}
                                </span>
                            </div>

                            <div class="mb-2">
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                    Rp {{ number_format($stat['value'], 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                @if (!is_null($stat['change']))
                                    @php
                                        $isPositive = $stat['change'] >= 0;
                                        $badgeColor = $stat['color'];
                                        $icon = $isPositive
                                            ? 'heroicon-m-arrow-trending-up'
                                            : 'heroicon-m-arrow-trending-down';
                                    @endphp
                                    <x-filament::badge :color="$badgeColor" size="xs" class="flex items-center gap-1">
                                        <x-filament::icon :icon="$icon" class="w-8 h-3" />
                                        {{ number_format($stat['change'], 1) }}%
                                    </x-filament::badge>
                                @endif
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $stat['description'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endforeach
        </div>

        {{-- Ringkasan --}}
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full">
                    <span>Ringkasan Operasional {{ $reportTitle }}</span>
                    <x-filament::button color="success" wire:click="downloadPdf" icon="heroicon-o-printer">
                        Cetak PDF
                    </x-filament::button>
                </div>
            </x-slot>


            <div class="overflow-x-auto">
                <table
                    class="w-full text-sm text-left text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <thead class="bg-gray-100 dark:bg-gray-800 dark:text-white">
                        <tr>
                            <th class="px-4 py-2 text-left">Rincian Pendapatan</th>
                            <th class="px-4 py-2 text-center"></th>
                            <th class="px-4 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rincianTableData as $row)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-4 py-2 text-center dark:text-white">
                                    @if (is_numeric($row['value']))
                                        Rp {{ number_format($row['value'], 0, ',', '.') }}
                                    @else
                                        {{ $row['value'] }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if (!is_null($row['change']))
                                        @php
                                            $isPositive = $row['change'] >= 0;
                                            $badgeColor = $isPositive ? 'success' : 'danger';
                                            $icon = $isPositive
                                                ? 'heroicon-m-arrow-trending-up'
                                                : 'heroicon-m-arrow-trending-down';
                                        @endphp
                                        <x-filament::badge :color="$badgeColor" class="flex items-center gap-1">
                                            <x-filament::icon :icon="$icon" class="w-4 h-4" />
                                            {{ number_format($row['change'], 1) }}%
                                        </x-filament::badge>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @foreach ($costRentTableData as $row)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-4 py-2 text-center dark:text-white">
                                    @if (is_numeric($row['value']))
                                        Rp {{ number_format($row['value'], 0, ',', '.') }}
                                    @else
                                        {{ $row['value'] }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if (!is_null($row['change']))
                                        @php
                                            $isPositive = $row['change'] <= 0;
                                            $badgeColor = $isPositive ? 'success' : 'danger';
                                            $icon = $isPositive
                                                ? 'heroicon-m-arrow-trending-up'
                                                : 'heroicon-m-arrow-trending-down';
                                        @endphp
                                        <x-filament::badge :color="$badgeColor" class="flex items-center gap-1">
                                            <x-filament::icon :icon="$icon" class="w-4 h-4" />
                                            {{ number_format($row['change'], 1) }}%
                                        </x-filament::badge>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="overflow-x-auto">
                <table
                    class="w-full text-sm text-left text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <thead class="bg-gray-100 dark:bg-gray-800 dark:text-white">
                        <tr>
                            <th class="px-4 py-2 text-left">Rincian Pengeluaran</th>
                            <th class="px-4 py-2 text-center"></th>
                            <th class="px-4 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rincianCostTableData as $row)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-4 py-2 text-center dark:text-white">
                                    @if (is_numeric($row['value']))
                                        Rp {{ number_format($row['value'], 0, ',', '.') }}
                                    @else
                                        {{ $row['value'] }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if (!is_null($row['change']))
                                        @php
                                            $isPositive = $row['change'] <= 0;
                                            $badgeColor = $isPositive ? 'success' : 'danger';
                                            $icon = $isPositive
                                                ? 'heroicon-m-arrow-trending-up'
                                                : 'heroicon-m-arrow-trending-down';
                                        @endphp
                                        <x-filament::badge :color="$badgeColor" class="flex items-center gap-1">
                                            <x-filament::icon :icon="$icon" class="w-4 h-4" />
                                            {{ number_format($row['change'], 1) }}%
                                        </x-filament::badge>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="overflow-x-auto">
                <table
                    class="w-full text-sm text-left text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <thead class="bg-gray-100 dark:bg-gray-800 dark:text-white">
                        <tr>
                            <th class="px-4 py-2 text-left">Total </th>
                            <th class="px-4 py-2 text-center"></th>
                            <th class="px-4 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($costTableData as $row)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-4 py-2 text-center dark:text-white">
                                    @if (is_numeric($row['value']))
                                        Rp {{ number_format($row['value'], 0, ',', '.') }}
                                    @else
                                        {{ $row['value'] }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if (!is_null($row['change']))
                                        @php
                                            $isPositive = $row['change'] <= 0;
                                            $badgeColor = $isPositive ? 'success' : 'danger';
                                            $icon = $isPositive
                                                ? 'heroicon-m-arrow-trending-up'
                                                : 'heroicon-m-arrow-trending-down';
                                        @endphp
                                        <x-filament::badge :color="$badgeColor" class="flex items-center gap-1">
                                            <x-filament::icon :icon="$icon" class="w-4 h-4" />
                                            {{ number_format($row['change'], 1) }}%
                                        </x-filament::badge>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @foreach ($summaryTableData as $row)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-4 py-2 text-center dark:text-white">
                                    @if (is_numeric($row['value']))
                                        Rp {{ number_format($row['value'], 0, ',', '.') }}
                                    @else
                                        {{ $row['value'] }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if (!is_null($row['change']))
                                        @php
                                            $isPositive = $row['change'] >= 0;
                                            $badgeColor = $isPositive ? 'success' : 'danger';
                                            $icon = $isPositive
                                                ? 'heroicon-m-arrow-trending-up'
                                                : 'heroicon-m-arrow-trending-down';
                                        @endphp
                                        <x-filament::badge :color="$badgeColor" class="flex items-center gap-1">
                                            <x-filament::icon :icon="$icon" class="w-4 h-4" />
                                            {{ number_format($row['change'], 1) }}%
                                        </x-filament::badge>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
