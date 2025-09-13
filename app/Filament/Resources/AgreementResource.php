<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgreementResource\Pages;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                        ->content(fn(?Booking $record): string => $record?->tanggal_keluar ?? '-'),
                    Forms\Components\Placeholder::make('tanggal_kembali')
                        ->label('Tanggal Kembali')
                        ->content(fn(?Booking $record): string => $record?->tanggal_kembali ?? '-'),
                    Forms\Components\Placeholder::make('car.nopol')
                        ->label('No. Polisi')
                        ->content(fn(?Booking $record): string => $record?->car?->nopol ?? '-'),
                    Forms\Components\Placeholder::make('waktu_keluar')
                        ->label('Waktu Keluar')

                        ->content(fn(?Booking $record): string => $record?->waktu_keluar ?? '-'),
                    Forms\Components\Placeholder::make('waktu_kembali')
                        ->label('Waktu Kembali')
                        ->content(fn(?Booking $record): string => $record?->waktu_kembali ?? '-'),
                    Forms\Components\Placeholder::make('total_hari')
                        ->label('Total Hari')
                        ->content(fn(?Booking $record): string => $record?->total_hari ?? '-'),
                    Forms\Components\Placeholder::make('estimasi_biaya')
                        ->label('Estimasi Biaya')
                        ->content(fn(?Booking $record): string => $record?->estimasi_biaya ?? '-'),
                    Forms\Components\Placeholder::make('invoice.dp')
                        ->label('Uang Muka (DP)')
                        ->content(fn(?Booking $record): string => $record?->invoice?->dp ?? '-'),
                    Forms\Components\Placeholder::make('invoice.sisa_pembayaran')
                        ->label('Sisa Pembayaran')
                        ->content(fn(?Booking $record): string => $record?->invoice->sisa_pembayaran ?? '-'),
                ])->columns(3),
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
                    ->wrap() // <-- Tambahkan wrap agar teks turun
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
                        // ❌ jangan ada ->default(now())
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
                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')   // ✅ biru, kontras dengan kuning
                    ->button()
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
            ->bulkActions([])
            ->defaultPaginationPageOption(10) // ✅ default 25 data per halaman
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
