<?php

namespace App\Filament\Resources\AgreementResource\Pages;

use App\Filament\Resources\AgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class EditAgreement extends EditRecord
{
    protected static string $resource = AgreementResource::class;
    protected function getHeaderInfolist(): ?Infolist
    {
        return Infolist::make()
            ->schema([
                Section::make('Detail Booking')
                    ->schema([
                        TextEntry::make('id')->label('Booking ID'),
                        TextEntry::make('customer.nama')->label('Nama Customer'),
                        TextEntry::make('car.carModel.name')->label('Nama Mobil'),

                        TextEntry::make('tanggal_keluar')
                            ->label('Tanggal Keluar')
                            ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('d M Y') : '-'),

                        TextEntry::make('tanggal_kembali')
                            ->label('Tanggal Kembali')
                            ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('d M Y') : '-'),

                        TextEntry::make('car.nopol')->label('No. Polisi'),

                        TextEntry::make('waktu_keluar')
                            ->label('Waktu Keluar')
                            ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('H:i') : '-'),

                        TextEntry::make('waktu_kembali')
                            ->label('Waktu Kembali')
                            ->formatStateUsing(fn($state) => $state ? Carbon::parse($state)->format('H:i') : '-'),

                        TextEntry::make('total_hari')
                            ->label('Total Hari')
                            ->formatStateUsing(fn($state) => $state ? "{$state} Hari" : '-'),

                        TextEntry::make('invoice.dp')
                            ->label('Uang Muka (DP)')
                            ->formatStateUsing(fn($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),

                        TextEntry::make('invoice.sisa_pembayaran')
                            ->label('Sisa Pembayaran')
                            ->formatStateUsing(fn($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),

                        TextEntry::make('invoice.total')
                            ->label('Total Tagihan')
                            ->formatStateUsing(fn($state) => $state ? 'Rp ' . number_format($state, 0, ',', '.') : '-'),
                    ])
                    ->columns(3),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
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
                        'foto_ban_serep' => $data['foto_ban_serep'] ?? null,
                    ]);

                    return response()->streamDownload(
                        fn() => print ($pdf->output()),
                        "form-keluar-{$this->getRecord()->customer->nama}.pdf"
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
