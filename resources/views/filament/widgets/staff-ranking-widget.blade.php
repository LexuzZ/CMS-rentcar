<x-filament-widgets::widget>
    <x-filament::section>
        {{-- JUDUL WIDGET --}}
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span class="text-base font-semibold">⭐ Staff Paling Aktif</span>
                {{-- <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    {{ $dateForHumans }}
                </span> --}}
            </div>
        </x-slot>

        {{-- DATE PICKER --}}
        <div class="mb-4">
            {{ $this->form }}
        </div>

        {{-- TABEL RANKING --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 uppercase text-xs">
                    <tr>
                        <th class="px-3 py-3 text-center">Rank</th>
                        <th class="px-3 py-3">Staff</th>
                        <th class="px-3 py-3 text-center">Total</th>
                        <th class="px-3 py-3 text-center">Penyerahan</th>
                        <th class="px-3 py-3 text-center">Pengembalian</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-900">
                    @forelse ($stats as $stat)
                        <tr class="border-b dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">

                            {{-- Ranking --}}
                            <td class="px-3 py-3 text-center font-bold text-gray-900 dark:text-white">
                                @if($loop->iteration == 1)
                                    <span class="text-yellow-500 text-lg">🏆</span>
                                @elseif($loop->iteration == 2)
                                    <span class="text-gray-400 text-lg">🥈</span>
                                @elseif($loop->iteration == 3)
                                    <span class="text-amber-700 text-lg">🥉</span>
                                @else
                                    <span class="text-sm">{{ $loop->iteration }}</span>
                                @endif
                            </td>

                            {{-- Nama Staff --}}
                            <td class="px-3 py-3 font-semibold text-gray-900 dark:text-white">
                                {{ $stat['staff_name'] }}
                            </td>

                            {{-- Total --}}
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-1 text-xs font-bold rounded-full
                                    @if($stat['total'] >= 5)
                                        bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                    @elseif($stat['total'] >= 3)
                                        bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                                    @else
                                        bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400
                                    @endif
                                ">
                                    {{ $stat['total'] }}
                                </span>
                            </td>

                            {{-- Penyerahan --}}
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 text-xs rounded-full">
                                    🚗 {{ $stat['penyerahan'] }}
                                </span>
                            </td>

                            {{-- Pengembalian --}}
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 text-xs rounded-full">
                                    ↩️ {{ $stat['pengembalian'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada aktivitas staff pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-500 mt-2"> ℹ️ Data menampilkan kinerja staff pada tanggal {{ $dateForHumans }}. </p>

    </x-filament::section>
</x-filament-widgets::widget>
