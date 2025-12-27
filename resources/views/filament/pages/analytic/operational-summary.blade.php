<x-filament-panels::page>
    <div>
        {{-- Filter Section --}}

        <x-filament::section>
            {{ $this->form }}

        </x-filament::section>
        <div class="grid grid-cols-4 gap-4 mt-6">
            @foreach ($statistics as $stat)
                <x-filament::section class="relative">
                    {{-- Accent bar --}}
                    <div
                        class="absolute inset-x-0 top-0 h-1
                {{ $stat['color'] === 'success'
                    ? 'bg-emerald-500'
                    : ($stat['color'] === 'warning'
                        ? 'bg-yellow-500'
                        : 'bg-red-500') }}">
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $stat['label'] }}
                            </p>

                            <p class="text-xl font-semibold text-gray-900 dark:text-white">
                                Rp {{ number_format($stat['value'], 0, ',', '.') }}
                            </p>

                            <div class="mt-2">
                                @php
                                    $isPositive = $stat['change'] >= 0;
                                    $icon = $isPositive
                                        ? 'heroicon-m-arrow-trending-up'
                                        : 'heroicon-m-arrow-trending-down';
                                @endphp

                                <x-filament::badge :color="$stat['color']" size="sm">
                                    <x-filament::icon :icon="$icon" class="w-3 h-3 mr-1" />
                                    {{ number_format($stat['change'], 1) }}%
                                </x-filament::badge>
                            </div>
                        </div>

                        <x-filament::icon :icon="$stat['icon']" class="w-6 h-6 text-gray-400" />
                    </div>
                </x-filament::section>
            @endforeach
        </div>




        {{-- Ringkasan --}}
        <div class="pt-8">
            <x-filament::section class="mt-8 pt-8">
                <x-slot name="heading">
                    <div class="flex items-center justify-between w-full">
                        <div>
                            <h2 class="text-lg font-bold">
                                Ringkasan Operasional
                            </h2>
                            <p class="text-sm text-gray-500">
                                Periode {{ $reportTitle }}
                            </p>
                        </div>

                        <x-filament::button color="gray" icon="heroicon-o-printer" wire:click="downloadPdf">
                            Export PDF
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

    </div>
</x-filament-panels::page>
