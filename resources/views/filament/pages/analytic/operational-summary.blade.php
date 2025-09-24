<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Ringkasan Operasional Bulan {{ $reportTitle }}
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Metrix</th>
                        <th class="px-4 py-3 text-right">Nilai Bulan Ini</th>
                        <th class="px-4 py-3 text-right">% vs Bulan Lalu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($summaryTableData as $row)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $row['label'] }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if (is_numeric($row['value']))
                                    Rp {{ number_format($row['value'], 0, ',', '.') }}
                                @else
                                    {{ $row['value'] }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($row['change'] !== null)
                                    <span class="{{ $row['change'] >= 0 ? 'text-emerald-600' : 'text-danger-600' }}">
                                        {{ number_format($row['change'], 1) }}%
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
