<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Judul Widget --}}
        <x-slot name="heading">
            <span class="flex items-center gap-2">
                <span>🚗</span>
                <span>Mobil Tersedia Saat Ini</span>
            </span>
        </x-slot>

        {{-- Grid Card --}}
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-3 gap-4">
            @forelse ($cars as $merek => $mobilList)
                <div
                    class="group rounded-xl border border-gray-200 dark:border-gray-700
                           bg-white dark:bg-gray-800
                           shadow-sm hover:shadow-md transition-all">

                    {{-- Header --}}
                    <div
                        class="px-4 py-3 border-b border-gray-200 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800/60 rounded-t-xl">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ Illuminate\Support\Str::title($merek) }}
                            <span class="ml-1 text-xs font-normal text-gray-500 dark:text-gray-400">
                                • {{ $mobilList->count() }} unit
                            </span>
                        </h3>
                    </div>

                    {{-- List Mobil --}}
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($mobilList as $mobil)
                            <li
                                class="px-4 py-3 flex items-center justify-between
                                       transition-colors
                                       hover:bg-emerald-50 dark:hover:bg-emerald-900/20">

                                {{-- Nama Mobil --}}
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $mobil->carModel->name }}
                                </p>

                                {{-- Badge Nopol --}}
                                <span
                                    class="text-xs font-semibold px-2 py-1 rounded-md
                                           bg-emerald-100 text-emerald-700
                                           dark:bg-emerald-900/40 dark:text-emerald-300">
                                    {{ $mobil->nopol }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                {{-- Empty State --}}
                <div
                    class="col-span-full rounded-xl border border-dashed border-gray-300 dark:border-gray-600
                           py-10 text-center text-gray-500 dark:text-gray-400">
                    🚫 Tidak ada mobil berstatus <span class="font-semibold">Ready</span> saat ini
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
