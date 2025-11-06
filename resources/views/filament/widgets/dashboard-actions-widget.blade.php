<x-filament::widget>
    <x-filament::section>
        <x-slot name="heading">
            ✅ Akses Cepat
        </x-slot>
        <x-filament::card>

            {{-- Slot untuk Judul Widget --}}

            {{-- Judul "Akses Cepat" --}}


            {{-- Ini adalah Grid container --}}
            {{-- Kita buat 3 kolom (grid-cols-3), Anda bisa ganti ke 4 atau 5 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">

                {{-- TOMBOL 1: Form Sewa --}}
                <a href="{{ \App\Filament\Resources\BookingResource::getUrl('index') }}"
                    class="flex flex-col items-center justify-center p-4 border rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

                    {{-- Ganti ikon di sini --}}
                    <x-heroicon-o-document-plus class="w-10 h-10 text-red-500" />

                    <span class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        Form Sewa
                    </span>
                </a>

                {{-- TOMBOL 2: Transaksi (Contoh) --}}
                {{-- Ganti 'SewaResource' dan 'index' dengan link Anda --}}
                <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('index') }}"
                    class="flex flex-col items-center justify-center p-4 border rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

                    {{-- Ikon ganti jadi 'credit-card' --}}
                    <x-heroicon-o-credit-card class="w-10 h-10 text-primary-600" />

                    <span class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        Transaksi
                    </span>
                </a>

                {{-- TOMBOL 3: Approval (Contoh) --}}
                <a href="#" {{-- Ganti link # --}}
                    class="flex flex-col items-center justify-center p-4 border rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

                    {{-- Ikon ganti jadi 'check-badge' --}}
                    <x-heroicon-o-check-badge class="w-10 h-10 text-primary-600" />

                    <span class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        Approval
                    </span>
                </a>

                {{-- Tambahkan tombol lain di sini (Kalender, Laporan, dll) --}}
                {{-- Contoh Kalender --}}
                <a href="#" {{-- Ganti link # --}}
                    class="flex flex-col items-center justify-center p-4 border rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

                    <x-heroicon-o-calendar-days class="w-10 h-10 text-primary-600" />

                    <span class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        Kalender
                    </span>
                </a>

            </div>
        </x-filament::card>

    </x-filament::section>

</x-filament::widget>
