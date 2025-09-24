<x-filament-panels::page>
    <div>
        {{-- Filter Section --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Ringkasan --}}
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                Ringkasan Operasional {{ $reportTitle }}
            </x-slot>

            <div class="overflow-x-auto">
                <table
                    class="w-full text-sm text-left text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Rincian</th>
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
                    <thead class="bg-gray-100 dark:bg-gray-800">
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
