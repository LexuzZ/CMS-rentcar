@php
    $statePath = $getStatePath();
@endphp

<div x-data="{ state: $wire.entangle('{{ $statePath }}') }"
    class="flex flex-col items-center justify-center p-4 border border-gray-300 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
    {{-- Preview Gambar --}}
    <div x-show="state" class="mb-4 w-full flex justify-center">
        <img x-bind:src="state" alt="Foto BBM" class="max-h-64 rounded-lg shadow-md border" />
    </div>

    {{-- Input File Hidden --}}
    <input type="file" accept="image/*" x-ref="fileInput" class="hidden"
        @change="
            const file = $event.target.files[0];
            if (file) {
                // Validasi ukuran file (max 2MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file maksimal 10MB');
                    $event.target.value = null;
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        const MAX_WIDTH = 800;
                        const MAX_HEIGHT = 800;
                        let width = img.width;
                        let height = img.height;

                        // Resize proporsional
                        if (width > height) {
                            if (width > MAX_WIDTH) {
                                height *= MAX_WIDTH / width;
                                width = MAX_WIDTH;
                            }
                        } else {
                            if (height > MAX_HEIGHT) {
                                width *= MAX_HEIGHT / height;
                                height = MAX_HEIGHT;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);

                        // Kompres ke 70% quality
                        state = canvas.toDataURL('image/jpeg', 0.7);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        ">

    {{-- Tombol --}}
    <div class="flex gap-2">
        <x-filament::button type="button" color="primary" size="sm" @click="$refs.fileInput.click()">
            <span x-show="!state">📷 Pilih Foto </span>
            <span x-show="state">🔄 Ganti Foto</span>
        </x-filament::button>

        <x-filament::button x-show="state" type="button" color="danger" size="sm" @click="state = null">
            ❌ Hapus
        </x-filament::button>
    </div>
</div>
