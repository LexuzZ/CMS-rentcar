<x-filament-panels::page>
    <div>
        {{-- Filter Section --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Report Table Section --}}
        <x-filament::section class="mt-6">
            {{-- Menggunakan judul dinamis dari properti $reportTitle --}}
            <x-slot name="heading">
                Ringkasan Kinerja untuk Bulan {{ $reportTitle }}
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Mobil</th>
                            <th scope="col" class="px-4 py-3">No. Polisi</th>
                            <th scope="col" class="px-4 py-3 text-center">Total Hari Disewa</th>
                            <th scope="col" class="px-4 py-3 text-right">Perkiraan Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Menggunakan properti $reportTableData untuk looping --}}
                        @forelse ($reportTableData as $data)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $data['model'] }}
                                </td>
                                <td class="px-4 py-3">{{ $data['nopol'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $data['days_rented'] }} hari</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center">Tidak ada data kinerja mobil untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
