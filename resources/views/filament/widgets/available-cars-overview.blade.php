{{-- File: resources/views/filament/widgets/available-cars-widget.blade.php --}}

<x-filament::widget>
    <x-filament::card>
        <h2 class="text-sm font-bold mb-4">Mobil Tersedia</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($cars as $merek => $mobilList)
            <div class="shadow rounded-lg p-4 dark-text-slate-100">
                <h3 class="bg-gray-800 dark:text-slate-100 font-semibold text-xs mb-2">
                    {{ Illuminate\Support\Str::title($merek) }} ({{ $mobilList->count() }} unit)
                </h3>

                {{-- Beri class 'custom-select' untuk target JavaScript --}}
                <select class="custom-select w-full bg-gray-800 dark:text-gray-800 text-xs rounded-md">
                    <option value="" class="text-gray-800">Pilih mobil {{ Illuminate\Support\Str::title($merek) }}</option>
                    @foreach ($mobilList as $mobil)
                        <option value="{{ $mobil->id }}">
                            {{-- PERUBAHAN DI SINI: Mengambil nama model dari relasi --}}
                            {{ $mobil->carModel->name }}
                            <span class='bg-gray-200 text-gray-800 text-xs font-medium ms-2 px-2 py-0.5 rounded-full dark:bg-gray-700 dark:text-gray-300'>
                               ({{$mobil->nopol}})
                            </span>
                        </option>
                    @endforeach
                </select>
            </div>
            @endforeach
        </div>
    </x-filament::card>
</x-filament::widget>

{{-- Pastikan script TomSelect Anda masih ada di sini jika Anda menggunakannya untuk styling badge --}}
@push('scripts')
<script>
    // Definisikan fungsi untuk menginisialisasi Tom Select
    const initTomSelect = () => {
        const customSelects = document.querySelectorAll('.custom-select');

        customSelects.forEach(selectElement => {
            if (selectElement.tomselect) {
                return; // Jika sudah, lewati
            }
            new TomSelect(selectElement, {
                render: {
                    option: function(data, escape) {
                        return `<div>${data.text}</div>`;
                    },
                    item: function(data, escape) {
                        return `<div>${data.text}</div>`;
                    }
                }
            });
        });
    };

    // Jalankan fungsi saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', () => {
        initTomSelect();
    });

    // Dengarkan sinyal dari Livewire untuk re-inisialisasi
    // Anda mungkin perlu mengirim event ini dari file PHP widget Anda
    document.addEventListener('init-tom-select', () => {
        initTomSelect();
    });
</script>
@endpush
