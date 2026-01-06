<x-filament-widgets::widget>
    <x-filament::section
        class="bg-gray-50 dark:bg-gray-900
               text-gray-900 dark:text-gray-100">

        {{-- Heading --}}
        <x-slot name="heading">
            <span class="flex items-center gap-2 text-gray-800 dark:text-gray-100">
                🚗 Mobil Tersedia Saat Ini
            </span>
        </x-slot>

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            @forelse ($cars as $merek => $mobilList)
                <div
                    class="rounded-xl border
                           border-gray-200 dark:border-gray-700
                           bg-gray-300 dark:bg-gray-800
                           transition hover:shadow-md">

                    {{-- Header --}}
                    <div
                        class="px-4 py-3 border-b
                               border-gray-200 dark:border-gray-700
                               bg-gray-200 dark:bg-gray-700/60 dark:text-gray-200
                               rounded-t-xl">

                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ Illuminate\Support\Str::title($merek) }}
                            <span class="ml-1 text-xs font-normal text-gray-600 dark:text-gray-400">
                                ({{ $mobilList->count() }} unit)
                            </span>
                        </h3>
                    </div>

                    {{-- List --}}
                    <ul class="divide-y divide-gray-300 dark:divide-gray-700">
                        @foreach ($mobilList as $mobil)
                            <li
                                class="px-4 py-3 flex items-center justify-between
                                       bg-gray-100 dark:bg-gray-800
                                       hover:bg-emerald-100 dark:hover:bg-emerald-900/30
                                       transition-colors">

                                {{-- Nama Mobil --}}
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $mobil->carModel->name }}
                                </p>

                                {{-- Nopol Badge --}}
                                <span
                                    class="text-xs font-semibold px-2 py-1 rounded-md
                                           bg-emerald-200 text-emerald-900
                                           dark:bg-emerald-900/60 dark:text-emerald-300">
                                    {{ $mobil->nopol }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                {{-- Empty --}}
                <div
                    class="col-span-full text-center py-10
                           border border-dashed
                           border-gray-300 dark:border-gray-600
                           bg-gray-100 dark:bg-gray-800
                           text-gray-600 dark:text-gray-400
                           rounded-xl">
                    🚫 Tidak ada mobil <span class="font-semibold">Ready</span>
                </div>
            @endforelse

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
