<div>
    <textarea id="invoice-text" class="w-full text-sm border rounded p-2 text-black dark:text-black" rows="12" readonly>{{ $textToCopy }}</textarea>

    <div class="mt-4 flex justify-end">
        <x-filament::button
            color="primary"
            icon="heroicon-o-clipboard"
            x-on:click="
                navigator.clipboard.writeText(document.getElementById('invoice-text').value).then(() => {
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: { status: 'success', message: 'Detail faktur berhasil disalin 📋' }
                    }))
                })
            "
        >
            Copy ke Clipboard
        </x-filament::button>
    </div>
</div>
