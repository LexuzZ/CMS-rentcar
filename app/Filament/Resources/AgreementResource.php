<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgreementResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Filters\Filter;

class AgreementResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Checklist Garasi';
    protected static ?string $label = 'Checklist';
    protected static ?string $pluralLabel = 'Checklist Garasi';

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

                    Forms\Components\Placeholder::make('invoice.sisa_pembayaran')
                        ->label('Sisa Pembayaran')
                        ->content(
                            fn(?Booking $record): string =>
                            $record?->invoice?->sisa_pembayaran ? 'Rp ' . number_format($record->invoice->sisa_pembayaran, 0, ',', '.') : '-'
                        ),

                    Forms\Components\Placeholder::make('invoice.total')
                        ->label('Total Tagihan')
                        ->content(
                            fn(?Booking $record): string =>
                            $record?->invoice?->total ? 'Rp ' . number_format($record->invoice->total, 0, ',', '.') : '-'
                        ),
                ])
                ->columns(3),
            // Forms\Components\Section::make('Foto Indikator BBM')
            //     ->schema([
            //         // Menggunakan View kustom untuk input kamera
            //         Forms\Components\View::make('filament.forms.camera-capture')
            //             ->statePath('foto_bbm'), // State ini akan berisi data base64 dari foto

            //     ]),
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
            Forms\Components\Section::make('Foto Serah Terima')
                ->schema([
                    // Menggunakan View kustom untuk input kamera
                    Forms\Components\View::make('filament.forms.camera-capture')
                        ->statePath('foto_serah_terima'), // State ini akan berisi data base64 dari foto
                ]),
            // Forms\Components\Section::make('Foto Kendaraan & Dokumen')
            //     ->schema([
            //         // Foto BBM
            //         Forms\Components\View::make('filament.forms.camera-capture')
            //             ->label('Foto Indikator BBM')
            //             ->statePath('foto_bbm'),

            //         // Foto Dongkrak
            //         Forms\Components\View::make('filament.forms.camera-capture')
            //             ->label('Foto Dongkrak')
            //             ->statePath('foto_dongkrak'),

            //         // Foto Pelunasan
            //         Forms\Components\View::make('filament.forms.camera-capture')
            //             ->label('Foto Pelunasan')
            //             ->statePath('foto_pelunasan'),

            //         // Foto Serah Terima
            //         Forms\Components\View::make('filament.forms.camera-capture')
            //             ->label('Foto Serah Terima')
            //             ->statePath('foto_serah_terima'),
            //     ])
            //     ->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Booking ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Penyewa')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->width(250),
                Tables\Columns\TextColumn::make('car.carModel.name')
                    ->label('Mobil')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->sortable()
                    ->date('d M Y')
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('ttd')
                    ->label('TTD')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Filter::make('tanggal_keluar')
                    ->form([
                        DatePicker::make('date')
                            ->label('Tanggal Keluar')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date'],
                            fn($q, $date) => $q->whereDate('tanggal_keluar', $date)
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('E-Sign')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->button(),
            ])
            ->bulkActions([])
            ->defaultPaginationPageOption(10)
            ->paginated();
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
