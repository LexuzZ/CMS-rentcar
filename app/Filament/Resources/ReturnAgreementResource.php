<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturnAgreementResource\Pages;
use App\Filament\Resources\ReturnAgreementResource\RelationManagers;
use App\Models\Booking;
use App\Models\ReturnAgreement;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReturnAgreementResource extends Resource
{
    protected static ?string $model = Booking::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Form Kembali';
    // protected static ?string $navigationGroup = 'Kelola Pesanan Sewa';
    protected static ?int $navigationSort = 6;
    protected static ?string $label = 'Form Kembali';
    protected static ?string $pluralLabel = 'Form Kembali';

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

                    Forms\Components\Placeholder::make('car.carModel.name')
                        ->label('Nama Mobil')
                        ->content(fn(?Booking $record): string => $record?->car?->carModel->name ?? '-'),

                    Forms\Components\Placeholder::make('tanggal_keluar')
                        ->label('Tanggal Keluar')
                        ->content(
                            fn(?Booking $record): string =>
                            $record?->tanggal_keluar ? Carbon::parse($record->tanggal_keluar)->format('d M Y') : '-'
                        ),

                    Forms\Components\Placeholder::make('tanggal_kembali')
                        ->label('Tanggal Kembali')
                        ->content(
                            fn(?Booking $record): string =>
                            $record?->tanggal_kembali ? Carbon::parse($record->tanggal_kembali)->format('d M Y') : '-'
                        ),

                    Forms\Components\Placeholder::make('car.nopol')
                        ->label('No. Polisi')
                        ->content(fn(?Booking $record): string => $record?->car?->nopol ?? '-'),

                    Forms\Components\Placeholder::make('waktu_keluar')
                        ->label('Waktu Keluar')
                        ->content(
                            fn(?Booking $record): string =>
                            $record?->waktu_keluar ? Carbon::parse($record->waktu_keluar)->format('H:i') : '-'
                        ),

                    Forms\Components\Placeholder::make('waktu_kembali')
                        ->label('Waktu Kembali')
                        ->content(
                            fn(?Booking $record): string =>
                            $record?->waktu_kembali ? Carbon::parse($record->waktu_kembali)->format('H:i') : '-'
                        ),

                    Forms\Components\Placeholder::make('total_hari')
                        ->label('Total Hari')
                        ->content(fn(?Booking $record): string => $record?->total_hari ? "{$record->total_hari} Hari" : '-'),

                    Forms\Components\Placeholder::make('invoice.dp')
                        ->label('Uang Muka (DP)')
                        ->content(
                            fn(?Booking $record): string =>
                            $record?->invoice?->dp ? 'Rp ' . number_format($record->invoice->dp, 0, ',', '.') : '-'
                        ),

                    Forms\Components\Placeholder::make('sisa_pembayaran')
                        ->label('Sisa Pembayaran')
                        ->content(function (?Booking $record): string {
                            if (!$record) {
                                return '-';
                            }

                            $biayaSewa = $record->estimasi_biaya ?? 0;
                            $biayaAntarJemput = $record->invoice?->pickup_dropOff ?? 0;
                            $totalDenda = $record->penalty?->sum('amount') ?? 0;
                            $dp = $record->invoice?->dp ?? 0;

                            $sisa = ($biayaSewa + $biayaAntarJemput + $totalDenda) - $dp;

                            return 'Rp ' . number_format($sisa, 0, ',', '.');
                        }),


                    Forms\Components\Placeholder::make('total_tagihan')
                        ->label('Total Tagihan')
                        ->content(function (?Booking $record): string {
                            if (!$record) {
                                return '-';
                            }

                            $biayaSewa = $record->estimasi_biaya ?? 0;
                            $biayaAntarJemput = $record->invoice?->pickup_dropOff ?? 0;
                            $totalDenda = $record->penalty?->sum('amount') ?? 0;

                            $totalTagihan = $biayaSewa + $biayaAntarJemput + $totalDenda;

                            return 'Rp ' . number_format($totalTagihan, 0, ',', '.');
                        }),

                ])
                ->columns(3),

            Forms\Components\Section::make('Data Pengembalian')
                ->schema([
                    Forms\Components\Section::make('Foto Indikator BBM')
                        ->schema([
                            // Menggunakan View kustom untuk input kamera
                            Forms\Components\View::make('filament.forms.camera-capture')
                                ->statePath('foto_bbm'), // State ini akan berisi data base64 dari foto
                        ]),
                    Forms\Components\Section::make('Foto Dongkrak')
                        ->schema([
                            // Menggunakan View kustom untuk input kamera
                            Forms\Components\View::make('filament.forms.camera-capture')
                                ->statePath('foto_dongkrak'), // State ini akan berisi data base64 dari foto
                        ]),
                    Forms\Components\Section::make('Foto Pelunasan')
                        ->schema([
                            // Menggunakan View kustom untuk input kamera
                            Forms\Components\View::make('filament.forms.camera-capture')
                                ->statePath('foto_pelunasan'), // State ini akan berisi data base64 dari foto
                        ]),
                    Forms\Components\Section::make('Foto Ban Serep')
                        ->schema([
                            // Menggunakan View kustom untuk input kamera
                            Forms\Components\View::make('filament.forms.camera-capture')
                                ->statePath('foto_ban_serep'), // State ini akan berisi data base64 dari foto
                        ]),
                    // Forms\Components\Section::make('Foto Jaminan Sewa (Motor/STNK)')
                    //     ->schema([
                    //         // Menggunakan View kustom untuk input kamera
                    //         Forms\Components\View::make('filament.forms.camera-capture')
                    //             ->statePath('foto_jaminan_sewa'), // State ini akan berisi data base64 dari foto
                    //     ]),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Booking ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')->label('Penyewa')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('car.carModel.name')->label('Mobil')->sortable(),
                Tables\Columns\TextColumn::make('car.nopol')->label('Nopol'),
                // Tables\Columns\TextColumn::make('tanggal_keluar')->date('d M Y')->label('Tanggal Keluar'),
                Tables\Columns\TextColumn::make('tanggal_kembali')->date('d M Y')->label('Tanggal Kembali '),
            ])
            ->filters([])
            ->actions([
                // Tables\Actions\EditAction::make()->label('Form Kembali')->icon('heroicon-o-pencil')->color('success'),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Form Kembali')
                    ->icon('heroicon-o-pencil')
                    ->color('success')
                    ->hiddenLabel()
                    ->button(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('ttd');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReturnAgreements::route('/'),
            'create' => Pages\CreateReturnAgreement::route('/create'),
            'edit' => Pages\EditReturnAgreement::route('/{record}/edit'),
        ];
    }
}
