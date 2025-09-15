<div>
    <textarea id="invoice-text"
        class="w-full text-sm border rounded p-2 text-black dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200"
        rows="12" readonly>{{ $textToCopy }}</textarea>

    <div class="mt-4 flex justify-end">
        <x-filament::button color="gray" icon="heroicon-o-clipboard-document" x-data="{
            copy(text) {
                navigator.clipboard.writeText(text).then(() => {
                    $dispatch('notify', {
                        status: 'success',
                        message: 'Detail faktur berhasil disalin 📋',
                    })
                })
            }
        }"
            x-on:click="copy($refs.invoiceText.innerText)">
            Salin Detail Faktur
        </x-filament::button>
    </div>

    {{-- Simpan text di elemen tersembunyi --}}
    <div x-ref="invoiceText" class="hidden">
        Halo 👋😊 {{ $record->booking->customer->nama }}

        🧾 Faktur: #{{ $record->id }}
        Mobil: {{ $record->booking->car->carModel->brand->name }} {{ $record->booking->car->carModel->name }}
        ({{ $record->booking->car->nopol }})
        Tanggal: {{ \Carbon\Carbon::parse($record->tanggal_invoice)->isoFormat('D MMMM Y') }}
        Total: Rp
        {{ number_format(($record->booking->estimasi_biaya ?? 0) + ($record->pickup_dropOff ?? 0) + ($record->booking?->penalty->sum('amount') ?? 0), 0, ',', '.') }}
        DP: Rp {{ number_format($record->dp ?? 0, 0, ',', '.') }}
        Sisa: Rp
        {{ number_format(($record->booking->estimasi_biaya ?? 0) + ($record->pickup_dropOff ?? 0) + ($record->booking?->penalty->sum('amount') ?? 0) - ($record->dp ?? 0), 0, ',', '.') }}
    </div>

</div>
