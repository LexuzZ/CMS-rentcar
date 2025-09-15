@php
    $statePath = $getStatePath();
@endphp
<div x-data="{ state: $wire.entangle('{{ $statePath }}') }">
    <input
        type="file"
        x-ref="fileInput"
        accept="image/*"
        capture="environment"
        @change="
            const file = $event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    state = e.target.result; // Mengatur state Livewire menjadi data base64
                };
                reader.readAsDataURL(file);
            }
        "
        class="hidden"
    />
    {{-- Tombol untuk memicu input file --}}
    <div class="p-4 border rounded-lg cursor-pointer hover:bg-gray-50 flex items-center justify-center" @click="$refs.fileInput.click()">
        <span class="text-sm text-gray-500">
            {{-- Menggunakan SVG atau icon yang sesuai --}}
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.842-1.683A2 2 0 019.462 2h5.076a2 2 0 011.664.89l.842 1.683A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Ambil Foto BBM
        </span>
    </div>

    {{-- Menampilkan pratinjau foto jika ada --}}
    <template x-if="state">
        <div class="mt-4">
            <img :src="state" class="w-full h-auto rounded-lg shadow-sm" alt="Foto Indikator BBM" />
            <x-filament::button color="danger" size="sm" @click="state = null" class="mt-2">Hapus Foto</x-filament::button>
        </div>
    </template>
</div>
