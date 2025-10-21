<?php

namespace App\Filament\Resources\ReturnAgreementResource\Pages;

use App\Filament\Resources\ReturnAgreementResource;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReturnAgreement extends EditRecord
{
    protected static string $resource = ReturnAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
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

                    $pdf = Pdf::loadView('pdf.returnAgreement', [
                        'booking' => $this->getRecord(),
                        'foto_bbm' => $data['foto_bbm'] ?? null,
                        'foto_dongkrak' => $data['foto_dongkrak'] ?? null,
                        'foto_pelunasan' => $data['foto_pelunasan'] ?? null,
                        'foto_ban_serep' => $data['foto_ban_serep'] ?? null,
                    ]);

                    return response()->streamDownload(
                        fn() => print ($pdf->output()),
                        "form-kembali-{$this->getRecord()->customer->nama}.pdf"
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
