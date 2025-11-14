<x-filament-widgets::widget>
    <x-filament::section>

        {{-- Judul Widget --}}
        <x-slot name="heading">
            Peringkat Staff Bulanan – {{ $dateForHumans }}
        </x-slot>

        {{-- Filter Bulan & Tahun --}}
        <div class="mb-4">
            {{ $this->form }}
        </div>

        {{-- Tabel Peringkat Staff --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs uppercase bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Rank</th>
                        <th class="px-4 py-3">Staff</th>
                        <th class="px-4 py-3 text-center">Total</th>
                        <th class="px-4 py-3 text-center">Penyerahan</th>
                        <th class="px-4 py-3 text-center">Pengembalian</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($stats as $stat)
                        <tr
                            class="border-b dark:border-gray-700
                            @if ($loop->first) bg-yellow-50 dark:bg-yellow-900/20 @endif"
                        >
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                @if ($loop->first)
                                    🏆
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $stat['staff_name'] }}
                            </td>

                            <td class="px-4 py-3 text-center font-bold">
                                {{ $stat['total'] }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ $stat['penyerahan'] }} 🚗
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ $stat['pengembalian'] }} ↩️
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center">
                                Tidak ada aktivitas staff pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-500 mt-2">
            💡 Menampilkan kinerja staff pada bulan {{ $dateForHumans }}.
        </p>

    </x-filament::section>
</x-filament-widgets::widget>
