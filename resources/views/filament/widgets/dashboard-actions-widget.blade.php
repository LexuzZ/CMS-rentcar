<x-filament::widget>
    <x-filament::card>
        {{-- Anda bisa tambahkan judul jika mau --}}
        <x-slot name="heading">
            Akses Cepat
        </x-slot>

        {{-- Tombol Anda --}}
        <x-filament::button
            {{-- Ganti SewaResource dengan nama Resource Anda yang sebenarnya --}}
            href="{{ \App\Filament\Resources\SewaResource::getUrl('create') }}"
            icon="heroicon-o-plus"
            tag="a"
        >
            Buat Form Sewa
        </x-filament::button>

        {{--
            Tips: Anda bisa menambahkan tombol lain di sini
            <x-filament::button
                href="{{ \App\Filament\Resources\CustomerResource::getUrl('create') }}"
                icon="heroicon-o-user-plus"
                tag="a"
                color="secondary"
                class="ml-2"
            >
                Tambah Customer
            </x-filament::button>
        --}}

    </x-filament::card>
</x-filament::widget>
