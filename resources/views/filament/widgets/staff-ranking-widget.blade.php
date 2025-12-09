<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <h2 class="text-lg font-bold">Peringkat Kinerja Staff</h2>
                <p class="text-sm text-gray-500">
                    Data Tanggal: {{ $dateForHumans }}
                </p>
            </div>

            {{-- Form Datepicker --}}
            <div class="w-48">
                {{ $this->form }}
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-2">Nama Staff</th>
                        <th class="px-4 py-2 text-center">Antar</th>
                        <th class="px-4 py-2 text-center">Jemput</th>
                        <th class="px-4 py-2 text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stats as $stat)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2 font-medium">
                                {{ $stat['staff_name'] }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-blue-100 bg-blue-600 rounded-full">
                                    {{ $stat['penyerahan'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-600 rounded-full">
                                    {{ $stat['pengembalian'] }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center font-bold">
                                {{ $stat['total'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                Tidak ada aktivitas staff pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
