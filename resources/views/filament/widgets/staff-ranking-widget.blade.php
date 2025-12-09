<x-filament-widgets::widget>
    <x-filament::section>
        {{-- HEADER WIDGET --}}
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon
                    icon="heroicon-m-trophy"
                    class="h-5 w-5 text-yellow-500"
                />
                <span class="text-base font-semibold">Leaderboard Staff</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Statistik penugasan antar/jemput pada {{ $dateForHumans }}.
        </x-slot>

        {{-- DATE PICKER --}}
        <div class="mb-6 max-w-xs">
            {{ $this->form }}
        </div>

        {{-- TABEL RANKING --}}
        <div class="overflow-x-auto rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 mt-6">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-white/5 text-gray-700 dark:text-gray-200 uppercase text-xs font-medium">
                    <tr>
                        <th class="px-4 py-3 text-center w-16">Rank</th>
                        <th class="px-4 py-3">Nama Driver</th>
                        <th class="px-4 py-3 text-center">Total Job</th>
                        <th class="px-4 py-3 text-center">Antar</th>
                        <th class="px-4 py-3 text-center">Jemput</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-white/5 bg-white dark:bg-gray-900">
                    @forelse ($stats as $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150">

                            {{-- Ranking --}}
                            <td class="px-4 py-3 text-center font-bold text-gray-900 dark:text-white">
                                @if($loop->iteration == 1)
                                    <span class="text-2xl" title="Juara 1">🥇</span>
                                @elseif($loop->iteration == 2)
                                    <span class="text-2xl" title="Juara 2">🥈</span>
                                @elseif($loop->iteration == 3)
                                    <span class="text-2xl" title="Juara 3">🥉</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 text-xs text-gray-500 dark:text-gray-400 font-medium">
                                        {{ $loop->iteration }}
                                    </span>
                                @endif
                            </td>

                            {{-- Nama Staff --}}
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $stat['staff_name'] }}
                            </td>

                            {{-- Total --}}
                            <td class="px-4 py-3 text-center">
                                <span @class([
                                    'px-2.5 py-0.5 text-xs font-bold rounded-full',
                                    'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400 ring-1 ring-inset ring-success-600/20' => $stat['total'] >= 5,
                                    'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400 ring-1 ring-inset ring-warning-600/20' => $stat['total'] >= 3 && $stat['total'] < 5,
                                    'bg-gray-50 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400 ring-1 ring-inset ring-gray-500/10' => $stat['total'] < 3,
                                ])>
                                    {{ $stat['total'] }}
                                </span>
                            </td>

                            {{-- Penyerahan (Antar) --}}
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium rounded-md bg-blue-50 text-blue-700 dark:bg-blue-400/10 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10">
                                    <x-filament::icon icon="heroicon-m-arrow-right-start-on-rectangle" class="w-3 h-3" />
                                    <span>{{ $stat['penyerahan'] }}</span>
                                </span>
                            </td>

                            {{-- Pengembalian (Jemput) --}}
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium rounded-md bg-purple-50 text-purple-700 dark:bg-purple-400/10 dark:text-purple-400 ring-1 ring-inset ring-purple-700/10">
                                    <x-filament::icon icon="heroicon-m-arrow-left-end-on-rectangle" class="w-3 h-3" />
                                    <span>{{ $stat['pengembalian'] }}</span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-full">
                                        <x-filament::icon
                                            icon="heroicon-o-users"
                                            class="h-6 w-6 text-gray-400"
                                        />
                                    </div>
                                    <span>Belum ada aktivitas staff pada tanggal ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
