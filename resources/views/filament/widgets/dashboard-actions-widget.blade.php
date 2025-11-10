<x-filament::widget>
    <x-filament::section>
        <x-slot name="heading">
            🚀 Quick Menu
        </x-slot>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-2">

            {{-- TOMBOL 1: Form Sewa --}}
            <a href="{{ \App\Filament\Resources\BookingResource::getUrl('index') }}"
                class="flex flex-col items-center justify-center p-2 border rounded-lg shadow-sm bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">

                {{-- Ganti ikon di sini --}}
                <div
                    class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 flex items-center justify-center">
                    {{-- IKON (ukurannya lebih kecil dari lingkaran) --}}
                    <div class="text-4xl">🧾</div>
                </div>

                <span class="mt-2 text-xs md:text-sm lg:text-lg font-medium text-gray-900 dark:text-gray-100">
                    Form Sewa
                </span>
            </a>
            <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('index') }}"
                class="flex flex-col items-center justify-center p-2 border rounded-lg shadow-sm  hover:bg-gray-50 dark:text-black dark:hover:text-gray-900 transition-colors">

                {{-- Ganti ikon di sini --}}
                <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                    {{-- IKON (ukurannya lebih kecil dari lingkaran) --}}
                    <div class="text-4xl">💳</div>
                </div>

                <span class="mt-2 text-xs md:text-sm lg:text-lg font-medium text-gray-900 dark:text-gray-100">
                    Transaksi
                </span>
            </a>
            <a href="{{ \App\Filament\Pages\VehicleSchedule::getUrl() }}"
                class="flex flex-col items-center justify-center p-2 border rounded-lg shadow-sm bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">

                {{-- Ganti ikon di sini --}}
                <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                    {{-- IKON (ukurannya lebih kecil dari lingkaran) --}}
                    <div class="text-4xl">📅</div>
                </div>

                <span class="mt-2 text-xs md:text-sm lg:text-lg font-medium text-gray-900 dark:text-gray-100">
                    Kalender Unit
                </span>
            </a>
            <a href="{{ \App\Filament\Resources\AgreementResource::getUrl('index') }}"
                class="flex flex-col items-center justify-center p-2 border rounded-lg shadow-sm bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">

                {{-- Ganti ikon di sini --}}
                <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                    {{-- IKON (ukurannya lebih kecil dari lingkaran) --}}
                    <div class="text-4xl">✅</div>
                </div>

                <span class="mt-2 text-xs md:text-sm lg:text-lg font-medium text-gray-900 dark:text-gray-100">
                    Checklist Keluar
                </span>
            </a>
            <a href="{{ \App\Filament\Resources\ReturnAgreementResource::getUrl('index') }}"
                class="flex flex-col items-center justify-center p-2 border rounded-lg shadow-sm bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">

                {{-- Ganti ikon di sini --}}
                <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                    {{-- IKON (ukurannya lebih kecil dari lingkaran) --}}
                    <div class="text-6xl">📦</div>
                </div>

                <span class="mt-2 text-xs md:text-sm lg:text-lg font-medium text-gray-900 dark:text-gray-100">
                    Checklist Kembali
                </span>
            </a>



        </div>
        {{-- <x-filament::card> --}}


    </x-filament::section>

</x-filament::widget>
