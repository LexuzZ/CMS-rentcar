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

                    $pdf = Pdf::loadView('pdf.agreement', [
                        'booking' => $this->getRecord(),
                        'foto_bbm' => $data['foto_bbm'] ?? null,
                        'foto_dongkrak' => $data['foto_dongkrak'] ?? null,
                        'foto_pelunasan' => $data['foto_pelunasan'] ?? null,
                        'foto_serah_terima' => $data['foto_serah_terima'] ?? null,
                    ]);

                    return response()->streamDownload(
                        fn() => print ($pdf->output()),
                        "Perjanjian-Booking-{$this->getRecord()->customer->nama}.pdf"
                    );
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
