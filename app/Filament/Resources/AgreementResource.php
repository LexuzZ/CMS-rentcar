<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgreementResource\Pages;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AgreementResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Checklist Garasi';
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereDate('tanggal_keluar', now()->toDateString());
    }



    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Booking')
                ->schema([

                    Forms\Components\Placeholder::make('id')
                        ->label('Booking ID')
                        ->content(fn(?Booking $record): string => $record?->id ?? '-'),

                    Forms\Components\Placeholder::make('customer.nama')
                        ->label('Nama Customer')
                        ->content(fn(?Booking $record): string => $record?->customer?->nama ?? '-'),
                ])->columns(2),
            Forms\Components\Section::make('Persetujuan')
                ->schema([
                    Forms\Components\Section::make('Isi Perjanjian')
                        ->schema([
                            Forms\Components\View::make('filament.forms.agreement-rules'),
                        ]),
                    Forms\Components\Checkbox::make('agreement_confirmed')
                        ->label('Saya telah membaca & menyetujui isi perjanjian di atas.')
                        ->required()
                        ->helperText('Wajib dicentang sebelum tanda tangan.'),
                ]),

            Forms\Components\Section::make('Tanda Tangan')
                ->schema([
                    // PERBAIKAN: Jadikan komponen View sebagai field input utama untuk 'ttd'.
                    // statePath() memberitahu Filament bahwa komponen ini bertanggung jawab
                    // untuk data 'ttd', sehingga field Hidden tidak lagi diperlukan.
                    Forms\Components\View::make('filament.forms.signature-pad')
                        ->statePath('ttd'), // Kunci utama perbaikan ada di sini.
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Booking ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Nama Customer')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('car.carModel.name')
                    ->label('Mobil')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->sortable(),

                Tables\Columns\IconColumn::make('ttd')
                    ->label('TTD')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Isi & Edit TTD'),
                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Booking $record) {
                        $pdf = Pdf::loadView('pdf.agreement', [
                            'booking' => $record,
                        ]);

                        return response()->streamDownload(
                            fn() => print ($pdf->output()),
                            "Perjanjian-Booking-{$record->customer->nama}.pdf"
                        );
                    })
                    ->visible(fn(Booking $record) => filled($record->ttd)),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgreements::route('/'),
            'edit' => Pages\EditAgreement::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
