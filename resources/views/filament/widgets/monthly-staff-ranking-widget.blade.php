<x-filament-widgets::widget>
    <x-filament::section>

        {{-- Judul Widget --}}
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span class="text-lg font-semibold">Peringkat Staff Bulanan</span>
                <span class="text-sm px-3 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                    {{ $dateForHumans }}
                </span>
            </div>
        </x-slot>

        {{-- Filter Bulan & Tahun --}}
        <div class="mb-4">
            {{ $this->form }}
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">

                {{-- Header --}}
                <thead class="bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Rank</th>
                        <th class="px-4 py-3 font-semibold">Staff</th>
                        <th class="px-4 py-3 text-center font-semibold">Total</th>
                        <th class="px-4 py-3 text-center font-semibold">Penyerahan</th>
                        <th class="px-4 py-3 text-center font-semibold">Pengembalian</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($stats as $stat)
                        <tr
                            class="
                                border-b dark:border-gray-700 transition
                                @if ($loop->first)
                                    bg-yellow-50/60 dark:bg-yellow-900/20 font-semibold
                                @else
                                    hover:bg-gray-50 dark:hover:bg-gray-800/40
                                @endif
                            "
                        >
                            {{-- Rank --}}
                            <td class="px-4 py-3 whitespace-nowrap text-gray-900 dark:text-white">
                                @if ($loop->first)
                                    <span class="text-xl">🏆</span>
                                @else
                                    <span class="font-medium">{{ $loop->iteration }}</span>
                                @endif
                            </td>

                            {{-- Staff Name + Initial Avatar --}}
                            <td class="px-4 py-3 whitespace-nowrap flex items-center gap-3 text-gray-900 dark:text-white">
                                <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($stat['staff_name'], 0, 1)) }}
                                </div>
                                {{ $stat['staff_name'] }}
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 font-semibold">
                                    {{ $stat['total'] }}
                                </span>
                            </td>

                            {{-- Penyerahan --}}
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 font-medium">
                                    {{ $stat['penyerahan'] }} 🚗
                                </span>
                            </td>

                            {{-- Pengembalian --}}
                            <td class="px-4 py-3 text-center">
                                <span class="px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 font-medium">
                                    {{ $stat['pengembalian'] }} ↩️
                                </span>
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

        <p class="text-xs text-gray-500 mt-3">
            💡 Menampilkan kinerja staff pada bulan <span class="font-semibold">{{ $dateForHumans }}</span>.
        </p>

    </x-filament::section>
</x-filament-widgets::widget>
