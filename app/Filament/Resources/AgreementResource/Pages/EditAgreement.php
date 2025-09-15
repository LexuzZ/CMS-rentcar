<?php

namespace App\Filament\Resources\AgreementResource\Pages;

use App\Filament\Resources\AgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Barryvdh\DomPDF\Facade\Pdf;

class EditAgreement extends EditRecord
{
    protected static string $resource = AgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('downloadPdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->button()
                ->action(function () {
                    $data = $this->form->getState();
                    $record = $this->getRecord();

                    $fotoBbmPath = null;
                    $ttdPath = null;

                    try {
                        // Simpan foto BBM Base64 ke file sementara di server
                        if (!empty($data['foto_bbm'])) {
                            $fotoBbmData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['foto_bbm']));
                            $fotoBbmPath = tempnam(sys_get_temp_dir(), 'bbm_') . '.jpeg';
                            file_put_contents($fotoBbmPath, $fotoBbmData);
                        }

                        // Simpan tanda tangan Base64 ke file sementara di server
                        if ($record->ttd) {
                            $ttdData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $record->ttd));
                            $ttdPath = tempnam(sys_get_temp_dir(), 'ttd_') . '.png';
                            file_put_contents($ttdPath, $ttdData);
                        }

                        // Buat PDF dengan jalur file sementara
                        $pdf = Pdf::loadView('pdf.agreement', [
                            'booking' => $record,
                            'foto_bbm' => $fotoBbmPath,
                            'ttd_path' => $ttdPath,
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "Perjanjian-Booking-{$record->customer->nama}.pdf"
                        );
                    } finally {
                        // Pastikan file sementara dihapus setelah selesai
                        if ($fotoBbmPath && file_exists($fotoBbmPath)) {
                            unlink($fotoBbmPath);
                        }
                        if ($ttdPath && file_exists($ttdPath)) {
                            unlink($ttdPath);
                        }
                    }
                }),
            Actions\Action::make('simpan')
                ->label('Simpan')
                ->icon('heroicon-o-check')
                ->color('success')
                ->submit('save'),
            Actions\Action::make('cancel')
                ->label('Batal')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
