<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Filter Section --}}
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                Filter Data
            </x-slot>
            {{ $this->form }}
        </x-filament::section>

        {{-- Statistics Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @foreach ($statistics as $stat)
                <div class="relative overflow-hidden bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 transition duration-300 hover:shadow-lg hover:-translate-y-1 group">
                    {{-- Accent Bar --}}
                    <div class="absolute top-0 left-0 w-1 h-full
                        {{ $stat['color'] === 'success' ? 'bg-emerald-500' : ($stat['color'] === 'warning' ? 'bg-amber-500' : 'bg-rose-500') }}">
                    </div>

                    <div class="p-6 pl-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 group-hover:text-primary-600 transition">
                                    {{ $stat['label'] }}
                                </p>
                                <h3 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                                    Rp {{ number_format($stat['value'], 0, ',', '.') }}
                                </h3>
                            </div>

                            <div class="p-3 rounded-full bg-gray-50 dark:bg-white/5 group-hover:bg-gray-100 dark:group-hover:bg-white/10 transition">
                                <x-filament::icon :icon="$stat['icon']" class="w-6 h-6 text-gray-400 dark:text-gray-500" />
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-x-2">
                            @php
                                $isPositive = $stat['change'] >= 0;
                                $icon = $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
                                $color = $stat['color']; // Menggunakan warna dari data stat
                            @endphp

                            <x-filament::badge :color="$color" size="sm" class="shadow-sm">
                                <div class="flex items-center gap-1">
                                    <x-filament::icon :icon="$icon" class="w-3 h-3" />
                                    <span>{{ number_format(abs($stat['change']), 1) }}%</span>
                                </div>
                            </x-filament::badge>
                            <span class="text-xs text-gray-400">vs periode lalu</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Ringkasan Operasional Section --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 w-full">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            Laporan Keuangan
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Periode Laporan: <span class="font-medium text-primary-600">{{ $reportTitle }}</span>
                        </p>
                    </div>

                    <x-filament::button color="gray" icon="heroicon-o-printer" wire:click="downloadPdf" outlined>
                        Export PDF
                    </x-filament::button>
                </div>
            </x-slot>

            {{-- Grid Layout untuk Tabel Rincian --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-4">

                {{-- Kolom Kiri: Pendapatan --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <x-filament::icon icon="heroicon-o-banknotes" class="w-5 h-5 text-emerald-500"/>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Rincian Pendapatan</h3>
                    </div>

                    <div class="overflow-hidden bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl">
                        <table class="w-full text-sm text-left divide-y divide-gray-200 dark:divide-white/5">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Keterangan</th>
                                    <th class="px-6 py-3 font-medium text-right text-gray-500 dark:text-gray-400">Nominal</th>
                                    <th class="px-6 py-3 font-medium text-center text-gray-500 dark:text-gray-400">Tren</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                {{-- Gabungkan data rincian dan costRent jika logic bisnis mengizinkan, atau loop terpisah --}}
                                @foreach ($rincianTableData as $row)
                                    @include('components.partials.table-row', ['row' => $row, 'inverse' => false])
                                @endforeach

                                {{-- Jika Cost Rent masuk kategori pendapatan/HPP --}}
                                @foreach ($costRentTableData as $row)
                                    @include('components.partials.table-row', ['row' => $row, 'inverse' => true])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Kolom Kanan: Pengeluaran --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-200 dark:border-gray-700">
                        <x-filament::icon icon="heroicon-o-shopping-cart" class="w-5 h-5 text-rose-500"/>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Rincian Pengeluaran</h3>
                    </div>

                    <div class="overflow-hidden bg-white dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl">
                        <table class="w-full text-sm text-left divide-y divide-gray-200 dark:divide-white/5">
                            <thead class="bg-gray-50 dark:bg-white/5">
                                <tr>
                                    <th class="px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Keterangan</th>
                                    <th class="px-6 py-3 font-medium text-right text-gray-500 dark:text-gray-400">Nominal</th>
                                    <th class="px-6 py-3 font-medium text-center text-gray-500 dark:text-gray-400">Tren</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @foreach ($rincianCostTableData as $row)
                                    @include('components.partials.table-row', ['row' => $row, 'inverse' => true])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Summary / Grand Total Section --}}
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 ring-1 ring-gray-950/5 dark:ring-white/10">
                    <h3 class="text-lg font-bold mb-4 text-center lg:text-left text-gray-900 dark:text-white">Ringkasan Akhir (Net Profit)</h3>

                    <div class="overflow-x-auto">
                         <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @foreach ($costTableData as $row)
                                    <tr class="group hover:bg-gray-100 dark:hover:bg-white/5 transition">
                                        <td class="px-6 py-3 font-medium text-gray-700 dark:text-gray-200 w-1/2">{{ $row['label'] }}</td>
                                        <td class="px-6 py-3 text-right font-semibold text-gray-900 dark:text-white w-1/4">
                                            @if (is_numeric($row['value']))
                                                Rp {{ number_format($row['value'], 0, ',', '.') }}
                                            @else
                                                {{ $row['value'] }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-center w-1/4">
                                            {{-- Reuse badge logic --}}
                                            @if (!is_null($row['change']))
                                                @php
                                                    // Logic inverse: Pengeluaran naik = bad (merah), Pendapatan naik = good (hijau)
                                                    // Asumsi untuk total row defaultnya normal
                                                    $isPositive = $row['change'] >= 0;
                                                    $badgeColor = $isPositive ? 'success' : 'danger';
                                                    $icon = $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
                                                @endphp
                                                <div class="flex justify-center">
                                                    <x-filament::badge :color="$badgeColor">
                                                        <div class="flex items-center gap-1">
                                                            <x-filament::icon :icon="$icon" class="w-3 h-3" />
                                                            {{ number_format(abs($row['change']), 1) }}%
                                                        </div>
                                                    </x-filament::badge>
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Final Summary Rows (Net Profit, etc) --}}
                                @foreach ($summaryTableData as $row)
                                     <tr class="bg-primary-50/50 dark:bg-primary-900/10 border-t-2 border-primary-100 dark:border-primary-800">
                                        <td class="px-6 py-4 font-bold text-primary-700 dark:text-primary-400 text-base">{{ $row['label'] }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-primary-700 dark:text-primary-400 text-lg">
                                            @if (is_numeric($row['value']))
                                                Rp {{ number_format($row['value'], 0, ',', '.') }}
                                            @else
                                                {{ $row['value'] }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                             @if (!is_null($row['change']))
                                                @php
                                                    $isPositive = $row['change'] >= 0;
                                                    $badgeColor = $isPositive ? 'success' : 'danger';
                                                    $icon = $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
                                                @endphp
                                                 <div class="flex justify-center">
                                                    <x-filament::badge :color="$badgeColor" size="md">
                                                        <div class="flex items-center gap-1 font-bold">
                                                            <x-filament::icon :icon="$icon" class="w-4 h-4" />
                                                            {{ number_format(abs($row['change']), 1) }}%
                                                        </div>
                                                    </x-filament::badge>
                                                 </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </x-filament::section>
    </div>
</x-filament-panels::page>

{{--
    NOTE: Tambahkan komponen blade kecil ini di file view yang sama (di bawah)
    atau buat file terpisah untuk menghindari duplikasi kode baris tabel (DRY).
--}}
@push('scripts')
<script>
    // Hanya placeholder jika Anda ingin memisahkan partial view
</script>
@endpush

{{-- Definisi Template Baris Tabel (Reusable) --}}
{{-- Anda bisa memindahkannya ke components/partials/table-row.blade.php --}}
@verbatim
    <template id="row-template">
        </template>
@endverbatim

{{--
    Snippet Reusable untuk Baris Tabel (Salin ini jika tidak ingin pakai @include)
    Ganti bagian @include di atas dengan kode di bawah ini di dalam loop
--}}
{{--
<tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150">
    <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">
        {{ $row['label'] }}
    </td>
    <td class="px-6 py-3 text-right text-gray-700 dark:text-gray-300 font-mono">
        @if (is_numeric($row['value']))
            Rp {{ number_format($row['value'], 0, ',', '.') }}
        @else
            {{ $row['value'] }}
        @endif
    </td>
    <td class="px-6 py-3 text-center">
        @if (!is_null($row['change']))
            @php
                // Jika inverse = true (misal pengeluaran), makin besar change makin merah
                $isPositive = isset($inverse) && $inverse ? $row['change'] <= 0 : $row['change'] >= 0;
                $badgeColor = $isPositive ? 'success' : 'danger';
                $icon = $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
            @endphp
            <div class="flex justify-center">
                <x-filament::badge :color="$badgeColor" class="flex items-center gap-1 w-fit">
                    <x-filament::icon :icon="$icon" class="w-3 h-3" />
                    {{ number_format(abs($row['change']), 1) }}%
                </x-filament::badge>
            </div>
        @else
            <span class="text-gray-300 dark:text-gray-600">-</span>
        @endif
    </td>
</tr>
--}}
