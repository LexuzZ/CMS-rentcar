<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">
            Peringkat Staff Bulanan – {{ $dateForHumans }}
        </x-slot>

        {{-- Filter Bulan & Tahun --}}
        <div class="mb-4">
            {{ $this->form }}
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
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
                        <tr class="border-b dark:border-gray-700
                            @if ($loop->first) bg-yellow-50 dark:bg-yellow-900/20 @endif">

                            {{-- Rank --}}
                            <td class="px-4 py-3 whitespace-nowrap font-medium">
                                @if ($loop->first)
                                    🏆
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </td>

                            {{-- Nama --}}
                            <td class="px-4 py-3 font-medium">
                                {{ $stat['staff_name'] }}
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-3 text-center font-bold">
                                {{ $stat['total'] }}
                            </td>

                            {{-- Penyerahan --}}
                            <td class="px-4 py-3 text-center">
                                {{ $stat['penyerahan'] }} 🚗
                            </td>

                            {{-- Pengembalian --}}
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
