<x-filament-widgets::widget>
    <x-filament::section>
        {{-- ============================================================
             HEADER: Judul + Filter
        ============================================================ --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                    Ringkasan Kehadiran Bulanan
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $this->getMonthLabel() }} {{ $this->selectedYear }}
                    — {{ $this->getSelectedUserName() }}
                </p>
            </div>

            {{-- Filter Controls --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Filter Bulan --}}
                <select
                    wire:model.live="selectedMonth"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white text-sm px-3 py-1.5 shadow-sm
                           focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                    @foreach([
                        '01'=>'Januari','02'=>'Februari','03'=>'Maret',
                        '04'=>'April','05'=>'Mei','06'=>'Juni',
                        '07'=>'Juli','08'=>'Agustus','09'=>'September',
                        '10'=>'Oktober','11'=>'November','12'=>'Desember',
                    ] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                {{-- Filter Tahun --}}
                <select
                    wire:model.live="selectedYear"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white text-sm px-3 py-1.5 shadow-sm
                           focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                >
                    @foreach(range(now()->year, now()->year - 3) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                {{-- Filter Karyawan (hanya superadmin) --}}
                @if(Auth::user()->role === 'superadmin')
                <select
                    wire:model.live="selectedUserId"
                    class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                           text-gray-900 dark:text-white text-sm px-3 py-1.5 shadow-sm
                           focus:ring-2 focus:ring-primary-500 focus:border-primary-500 min-w-[160px]"
                >
                    <option value="">Semua Karyawan</option>
                    @foreach($this->getUsers() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>

        {{-- ============================================================
             STAT CARDS
        ============================================================ --}}
        @php $s = $this->getSummary(); @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">

            {{-- Hari Kerja --}}
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-calendar-days class="w-4 h-4" />
                    <span class="text-xs font-medium">Hari Kerja</span>
                </div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $s['hari_kerja'] }}</div>
                <div class="text-xs text-gray-400">hari</div>
            </div>

            {{-- Hadir --}}
            <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-1.5 text-green-600 dark:text-green-400">
                    <x-heroicon-o-check-circle class="w-4 h-4" />
                    <span class="text-xs font-medium">Hadir</span>
                </div>
                <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $s['hadir'] }}</div>
                <div class="text-xs text-green-500">hari</div>
            </div>

            {{-- Terlambat --}}
            <div class="rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-1.5 text-yellow-600 dark:text-yellow-400">
                    <x-heroicon-o-clock class="w-4 h-4" />
                    <span class="text-xs font-medium">Terlambat</span>
                </div>
                <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $s['terlambat'] }}</div>
                <div class="text-xs text-yellow-500">hari</div>
            </div>

            {{-- Izin --}}
            <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-1.5 text-blue-600 dark:text-blue-400">
                    <x-heroicon-o-document-text class="w-4 h-4" />
                    <span class="text-xs font-medium">Izin</span>
                </div>
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $s['izin'] }}</div>
                <div class="text-xs text-blue-500">hari</div>
            </div>

            {{-- Alpha --}}
            <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-1.5 text-red-600 dark:text-red-400">
                    <x-heroicon-o-x-circle class="w-4 h-4" />
                    <span class="text-xs font-medium">Alpha</span>
                </div>
                <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $s['alpha'] }}</div>
                <div class="text-xs text-red-500">hari</div>
            </div>

            {{-- Tidak Tercatat --}}
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 p-4 flex flex-col gap-1">
                <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-question-mark-circle class="w-4 h-4" />
                    <span class="text-xs font-medium">Tidak Tercatat</span>
                </div>
                <div class="text-2xl font-bold text-gray-600 dark:text-gray-300">{{ $s['tidak_tercatat'] }}</div>
                <div class="text-xs text-gray-400">hari</div>
            </div>
        </div>

        {{-- ============================================================
             PROGRESS BAR KEHADIRAN + INFO BAWAH
        ============================================================ --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tingkat Kehadiran</span>
                <span class="text-sm font-bold
                    @if($s['persentase'] >= 80) text-green-600 dark:text-green-400
                    @elseif($s['persentase'] >= 60) text-yellow-600 dark:text-yellow-400
                    @else text-red-600 dark:text-red-400
                    @endif
                ">
                    {{ $s['persentase'] }}%
                </span>
            </div>

            {{-- Progress bar --}}
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                <div
                    class="h-3 rounded-full transition-all duration-700
                        @if($s['persentase'] >= 80) bg-green-500
                        @elseif($s['persentase'] >= 60) bg-yellow-500
                        @else bg-red-500
                        @endif
                    "
                    style="width: {{ $s['persentase'] }}%"
                ></div>
            </div>

            {{-- Keterangan bawah --}}
            <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                <span>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $s['total_hadir'] }}</span>
                    dari {{ $s['hari_kerja'] }} hari hadir fisik
                </span>
                <span class="flex items-center gap-1">
                    <x-heroicon-o-clock class="w-3 h-3" />
                    Rata-rata check-in:
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $s['avg_check_in'] }}</span>
                </span>
                @if($s['persentase'] < 80)
                <span class="text-red-500 font-medium">⚠ Di bawah standar minimum 80%</span>
                @endif
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
