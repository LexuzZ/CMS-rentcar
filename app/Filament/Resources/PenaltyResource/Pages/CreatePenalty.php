<?php

namespace App\Filament\Resources\PenaltyResource\Pages;

use App\Filament\Resources\PenaltyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePenalty extends CreateRecord
{
    protected static string $resource = PenaltyResource::class;
    protected static ?string $title = 'Tambah Klaim Garasi';
    public function mount(): void
    {
        // Cek apakah ada 'booking_id' di URL
        if (request()->has('booking_id')) {
            // Isi field 'booking_id' di form dengan nilai dari URL
            $this->form->fill([
                'booking_id' => request('booking_id'),
            ]);
        }
    }
    protected function getHeaderActions(): array
    {
        return [
            // Tombol Edit akan otomatis muncul di sini jika pengguna memiliki izin
            Actions\Action::make('kembali_ke_booking')
                ->label('Detail Pesanan')
                ->icon('heroicon-o-arrow-left')
                ->url(function () {
                    $booking = $this->record->booking;
                    if ($booking) {
                        // redirect ke halaman view booking di Filament
                        return \App\Filament\Resources\BookingResource::getUrl('view', [
                            'record' => $booking->id,
                        ]);
                    }

                    // fallback kalau tidak ada booking
                    return \App\Filament\Resources\BookingResource::getUrl();
                })
                ->color('gray'),
        ];
    }
}
