<x-filament::widget>
    <x-filament::section>
        <x-slot name="heading">
            🚀 Quick Menu
        </x-slot>
        <div class="quick-menu-grid">
            {{-- TOMBOL 1: Form Sewa --}}
            <a href="{{ \App\Filament\Resources\BookingResource::getUrl('create') }}"
                class="min-w-[calc(50%-0.5rem)] flex flex-col items-center justify-center p-1 border rounded-lg shadow-sm dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">

                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 flex items-center justify-center">
                    <div class="text-4xl">🧾</div>
                </div>

                <span class="mt-1 text-xs md:text-sm lg:text-lg font-medium text-gray-800 dark:text-gray-100">
                    Form Sewa
                </span>
            </a>

            {{-- TOMBOL 2: Transaksi --}}
            <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('index') }}"
                class="min-w-[calc(50%-0.5rem)] flex flex-col items-center justify-center p-1 border rounded-lg shadow-sm hover:bg-gray-50 dark:text-black dark:hover:text-gray-900 transition-colors">

                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                    <div class="text-4xl">💳</div>
                </div>

                <span class="mt-1 text-xs md:text-sm lg:text-lg font-medium text-gray-800 dark:text-gray-100">
                    Transaksi
                </span>
            </a>

            {{-- TOMBOL 3: Kalender Unit --}}
            <a href="{{ \App\Filament\Pages\VehicleSchedule::getUrl() }}"
                class="min-w-[calc(50%-0.5rem)] flex flex-col items-center justify-center p-1 border rounded-lg shadow-sm bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">

                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                    <div class="text-4xl">📅</div>
                </div>

                <span class="mt-1 text-xs md:text-sm lg:text-lg font-medium text-gray-800 dark:text-gray-100">
                    Kalender Unit
                </span>
            </a>

            {{-- TOMBOL 4: Checklist Keluar --}}
            <a href="{{ \App\Filament\Resources\AgreementResource::getUrl('index') }}"
                class="min-w-[calc(50%-0.5rem)] flex flex-col items-center justify-center p-1 border rounded-lg shadow-sm bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">

                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                    <div class="text-4xl">✅</div>
                </div>

                <span class="mt-1 text-xs md:text-sm lg:text-lg font-medium text-gray-800 dark:text-gray-100">
                    Checklist Keluar
                </span>
            </a>

            {{-- TOMBOL 5: Checklist Kembali --}}
            <a href="{{ \App\Filament\Resources\ReturnAgreementResource::getUrl('index') }}"
                class="min-w-[calc(50%-0.5rem)] flex flex-col items-center justify-center p-1 border rounded-lg shadow-sm bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">

                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                    <div class="text-6xl">📦</div>
                </div>

                <span class="mt-1 text-xs md:text-sm lg:text-lg font-medium text-gray-800 dark:text-gray-100">
                    Checklist Kembali
                </span>
            </a>
        </div>
        <style>
            .quick-menu-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 0.5rem;
            }

            @media (min-width: 768px) {
                .quick-menu-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media (min-width: 1024px) {
                .quick-menu-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }
        </style>
    </x-filament::section>
</x-filament::widget>
