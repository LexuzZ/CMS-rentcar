@php
    $statePath = $getStatePath();
@endphp

<div
    x-data="{ state: $wire.entangle('{{ $statePath }}') }"
    class="flex flex-col items-center justify-center p-4 border border-gray-300 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600"
>
    {{-- Preview Gambar --}}
    <div x-show="state" class="mb-4 w-full flex justify-center">
        <img x-bind:src="state" alt="Foto BBM" class="max-h-64 rounded-lg shadow-md border" />
    </div>

    {{-- Input File Hidden --}}
    <input
        type="file"
        accept="image/*"
        x-ref="fileInput"
        class="hidden"
        @change="
            const file = $event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        const MAX_WIDTH = 800;
                        const scaleSize = MAX_WIDTH / img.width;
                        canvas.width = MAX_WIDTH;
                        canvas.height = img.height * scaleSize;
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        state = canvas.toDataURL('image/jpeg', 0.8);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        "
    >

    {{-- Tombol --}}
    <div class="flex gap-2">
        <x-filament::button
            type="button"
            color="primary"
            size="sm"
            @click="$refs.fileInput.click()"
        >
            <span x-show="!state">📷 Pilih Foto </span>
            <span x-show="state">🔄 Ganti Foto</span>
        </x-filament::button>

        <x-filament::button
            x-show="state"
            type="button"
            color="danger"
            size="sm"
            @click="state = null"
        >
            ❌ Hapus
        </x-filament::button>
    </div>
</div>
