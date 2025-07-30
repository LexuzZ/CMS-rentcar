<x-filament::widget>
    <x-filament::card>
        <div class="p-6">
            <h2 class="text-2xl font-bold pb-6 text-gray-800">Mobil Tersedia (Ready)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse ($cars as $car)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow hover:shadow-lg transition-all duration-300 rounded-xl p-5 flex items-center gap-4">
                        <div class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-3 rounded-full">
                            <x-heroicon-o-truck class="w-6 h-6" />
                        </div>
                        <div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $car->nama_mobil }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-300">{{ $car->total }} Unit</div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">Tidak ada mobil dengan status <strong>ready</strong>.</p>
                @endforelse
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
