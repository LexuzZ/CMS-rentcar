<?php

namespace App\Filament\Pages\Worksheet\Widgets;

use App\Models\Booking;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MobilSchedule extends Widget
{
    protected static string $view = 'filament.widgets.mobil-schedule';
    protected static ?int $sort   = 3;
    protected int|string|array $columnSpan = 'full';

    /* ── Aksi Pick Up ── */
    public function pickupBooking(int $bookingId): void
    {
        if (!Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor'])) return;

        $booking = Booking::with('car')->find($bookingId);
        if (!$booking) return;

        $booking->status    = 'disewa';
        $booking->car->status = 'disewa';
        $booking->save();
        $booking->car->save();

        Notification::make()
            ->title('Mobil Telah Diambil')
            ->body("Booking {$booking->car->nopol} diubah menjadi 'Disewa'.")
            ->success()->send();
    }

    /* ── Aksi Selesaikan ── */
    public function selesaikanBooking(int $bookingId): void
    {
        if (!Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor'])) return;

        $booking = Booking::with('car')->find($bookingId);
        if (!$booking) return;

        $booking->status      = 'selesai';
        $booking->car->status = 'ready';
        $booking->save();
        $booking->car->save();

        Notification::make()
            ->title('Sewa Selesai')
            ->body("Booking {$booking->car->nopol} berhasil diselesaikan.")
            ->success()->send();
    }

    /* ── Redirect Edit ── */
    public function editBooking(int $bookingId)
    {
        return redirect()->to(
            \App\Filament\Resources\BookingResource::getUrl('edit', ['record' => $bookingId])
        );
    }

    /* ── Data ── */
    protected function getViewData(): array
    {
        $today    = \Carbon\Carbon::today('Asia/Jakarta');
        $tomorrow = \Carbon\Carbon::tomorrow('Asia/Jakarta');
        $with     = ['car.carModel.brand', 'customer', 'driver'];

        return [
            // 1. Keluar hari ini
            'keluarHariIni' => Booking::with($with)
                ->where('status', 'booking')
                ->whereDate('tanggal_keluar', $today)
                ->orderBy('waktu_keluar')
                ->get(),

            // 2. Kembali hari ini
            'kembaliHariIni' => Booking::with($with)
                ->where('status', 'disewa')
                ->whereDate('tanggal_kembali', $today)
                ->orderBy('waktu_kembali')
                ->get(),

            // 3. Keluar besok
            'keluarBesok' => Booking::with($with)
                ->where('status', 'booking')
                ->whereDate('tanggal_keluar', $tomorrow)
                ->orderBy('waktu_keluar')
                ->get(),

            // 4. Kembali besok
            'kembaliBesok' => Booking::with($with)
                ->where('status', 'disewa')
                ->whereDate('tanggal_kembali', $tomorrow)
                ->orderBy('waktu_kembali')
                ->get(),

            'canPerformActions' => Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor']),
            'today'    => $today->locale('id')->isoFormat('dddd, D MMM'),
            'tomorrow' => $tomorrow->locale('id')->isoFormat('dddd, D MMM'),
        ];
    }
}
