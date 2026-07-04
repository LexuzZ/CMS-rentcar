<div style="padding: 8px;">
    @if($record->proof)
        <img
            src="{{ Storage::disk('public')->url($record->proof) }}"
            alt="Bukti Pembayaran"
            style="width:100%; border-radius:10px; border:1px solid #e7e5e4; display:block;"
        >
        <p style="margin-top:10px; font-size:12px; color:#a8a29e; text-align:center;">
            {{ $record->invoice->booking->customer->nama ?? '—' }} ·
            Rp {{ number_format($record->pembayaran, 0, ',', '.') }} ·
            {{ \Carbon\Carbon::parse($record->tanggal_pembayaran)->locale('id')->isoFormat('D MMMM Y') }}
        </p>
    @else
        <p style="text-align:center; color:#a8a29e; padding:24px 0; font-size:13px;">
            Tidak ada bukti pembayaran.
        </p>
    @endif
</div>
