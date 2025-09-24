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
                <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Kategori</th>
                            <th class="px-4 py-2 text-right">Nilai</th>
                            <th class="px-4 py-2 text-center">Perubahan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($summaryTableData as $row)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $row['label'] }}
                                </td>
                                <td class="px-4 py-2 text-right">
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
                                            $changeColor = $isPositive ? 'text-success-700' : 'text-danger-600';
                                            $icon = $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
                                        @endphp
                                        <span class="{{ $changeColor }} flex items-center justify-center gap-1">
                                            <x-filament::icon :icon="$icon" class="w-4 h-4" />
                                            {{ number_format($row['change'], 1) }}%
                                        </span>
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
