<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgreementResource\Pages;
use App\Models\Booking;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AgreementResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Form Keluar';
    protected static ?int    $navigationSort  = 5;
    protected static ?string $label           = 'Form Keluar';
    protected static ?string $pluralLabel     = 'Form Keluar';

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereMonth('tanggal_keluar', now()->month)
            ->whereYear('tanggal_keluar', now()->year)
            ->whereNull('ttd')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Booking belum ditandatangani bulan ini';
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            // ── Detail Booking ──────────────────────
            Forms\Components\Section::make('Informasi Booking')
                ->icon('heroicon-o-calendar-days')
                ->columns(3)
                ->schema([
                    Forms\Components\Placeholder::make('id')
                        ->label('Booking ID')
                        ->content(fn (?Booking $record): string => $record?->id
                            ? '#BK' . str_pad($record->id, 3, '0', STR_PAD_LEFT)
                            : '—'
                        ),

                    Forms\Components\Placeholder::make('customer.nama')
                        ->label('Nama Penyewa')
                        ->content(fn (?Booking $record): string => $record?->customer?->nama ?? '—'),

                    Forms\Components\Placeholder::make('customer.ktp')
                        ->label('No. KTP')
                        ->content(fn (?Booking $record): string => $record?->customer?->ktp ?? '—'),

                    Forms\Components\Placeholder::make('car.carModel.name')
                        ->label('Nama Mobil')
                        ->content(fn (?Booking $record): string => $record?->car?->carModel->name ?? '—'),

                    Forms\Components\Placeholder::make('car.nopol')
                        ->label('No. Polisi')
                        ->content(fn (?Booking $record): string => $record?->car?->nopol ?? '—'),

                    Forms\Components\Placeholder::make('total_hari')
                        ->label('Total Hari')
                        ->content(fn (?Booking $record): string =>
                            $record?->total_hari ? "{$record->total_hari} Hari" : '—'
                        ),

                    Forms\Components\Placeholder::make('tanggal_keluar')
                        ->label('Tanggal Keluar')
                        ->content(fn (?Booking $record): string =>
                            $record?->tanggal_keluar
                                ? Carbon::parse($record->tanggal_keluar)->format('d M Y')
                                : '—'
                        ),

                    Forms\Components\Placeholder::make('tanggal_kembali')
                        ->label('Tanggal Kembali')
                        ->content(fn (?Booking $record): string =>
                            $record?->tanggal_kembali
                                ? Carbon::parse($record->tanggal_kembali)->format('d M Y')
                                : '—'
                        ),

                    Forms\Components\Placeholder::make('waktu_keluar')
                        ->label('Waktu Keluar')
                        ->content(fn (?Booking $record): string =>
                            $record?->waktu_keluar
                                ? Carbon::parse($record->waktu_keluar)->format('H:i') . ' WITA'
                                : '—'
                        ),
                ]),

            // ── Ringkasan Biaya ──────────────────────
            Forms\Components\Section::make('Ringkasan Biaya')
                ->icon('heroicon-o-banknotes')
                ->columns(3)
                ->schema([
                    Forms\Components\Placeholder::make('harga_harian')
                        ->label('Harga Harian')
                        ->content(fn (?Booking $record): string =>
                            $record?->harga_harian
                                ? 'Rp ' . number_format($record->harga_harian, 0, ',', '.')
                                : '—'
                        ),

                    Forms\Components\Placeholder::make('estimasi_biaya')
                        ->label('Estimasi Biaya Sewa')
                        ->content(fn (?Booking $record): string =>
                            $record?->estimasi_biaya
                                ? 'Rp ' . number_format($record->estimasi_biaya, 0, ',', '.')
                                : '—'
                        ),

                    Forms\Components\Placeholder::make('invoice.total_tagihan')
                        ->label('Total Tagihan')
                        ->content(fn (?Booking $record): string =>
                            $record?->invoice
                                ? 'Rp ' . number_format($record->invoice->total_tagihan, 0, ',', '.')
                                : '—'
                        ),

                    Forms\Components\Placeholder::make('invoice.sisa_pembayaran')
                        ->label('Sisa Pembayaran')
                        ->content(fn (?Booking $record): string =>
                            $record?->invoice
                                ? 'Rp ' . number_format($record->invoice->sisa_pembayaran, 0, ',', '.')
                                : '—'
                        ),
                ]),

            // ── Perjanjian ──────────────────────────
            Forms\Components\Section::make('Perjanjian Sewa')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\Section::make('Isi Perjanjian')
                        ->schema([
                            Forms\Components\View::make('filament.forms.agreement-rules'),
                        ]),

                    Forms\Components\Checkbox::make('agreement_confirmed')
                        ->label('Saya telah membaca dan menyetujui seluruh isi perjanjian di atas.')
                        ->required()
                        ->helperText('Wajib dicentang sebelum melanjutkan ke tanda tangan.'),
                ]),

            // ── Tanda Tangan ────────────────────────
            Forms\Components\Section::make('Tanda Tangan Digital')
                ->icon('heroicon-o-pencil')
                ->schema([
                    Forms\Components\View::make('filament.forms.signature-pad')
                        ->statePath('ttd'),
                ]),

            // ── Checklist Foto ──────────────────────
            Forms\Components\Section::make('Checklist Foto Serah Terima')
                ->icon('heroicon-o-camera')
                ->description('Dokumentasikan kondisi kendaraan sebelum diserahkan kepada penyewa.')
                ->columns(2)
                ->schema([
                    Forms\Components\Section::make('📷 Indikator BBM')
                        ->schema([
                            Forms\Components\View::make('filament.forms.camera-capture')
                                ->statePath('foto_bbm'),
                        ]),

                    Forms\Components\Section::make('📷 Foto Serah Terima')
                        ->schema([
                            Forms\Components\View::make('filament.forms.camera-capture')
                                ->statePath('foto_dongkrak'),
                        ]),

                    Forms\Components\Section::make('📷 Ban Serep & Dongkrak')
                        ->schema([
                            Forms\Components\View::make('filament.forms.camera-capture')
                                ->statePath('foto_ban_serep'),
                        ]),
                ]),
        ]);
    }

    // ─────────────────────────────────────────
    //  TABLE
    // ─────────────────────────────────────────
    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([

                // Booking ID
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#BK' . str_pad($state, 3, '0', STR_PAD_LEFT))
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->searchable(),

                // Penyewa + Mobil
                Tables\Columns\TextColumn::make('customer.nama')
                    ->label('Penyewa')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->searchable()
                    ->description(fn (Booking $record): string =>
                        ($record->car?->carModel?->name ?? '—') .
                        ' · ' . ($record->car?->nopol ?? '—')
                    ),

                // Tanggal keluar
                Tables\Columns\TextColumn::make('tanggal_keluar')
                    ->label('Tgl. Keluar')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-arrow-right-circle')
                    ->iconColor('success')
                    ->description(fn (Booking $record): string =>
                        $record->tanggal_kembali
                            ? Carbon::parse($record->tanggal_kembali)->format('d M Y')
                            : '—'
                    ),

                // Status TTD
                Tables\Columns\TextColumn::make('ttd')
                    ->label('Status TTD')
                    ->badge()
                    ->alignCenter()
                    ->state(fn (Booking $record) => $record->ttd ? 'signed' : 'unsigned')
                    ->icon(fn (string $state): string => match ($state) {
                        'signed'   => 'heroicon-m-check-badge',
                        'unsigned' => 'heroicon-m-pencil',
                        default    => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'signed'   => 'success',
                        'unsigned' => 'warning',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'signed'   => 'Sudah TTD',
                        'unsigned' => 'Belum TTD',
                        default    => '—',
                    }),

                // Waktu keluar
                Tables\Columns\TextColumn::make('waktu_keluar')
                    ->label('Jam Keluar')
                    ->formatStateUsing(fn ($state) => $state
                        ? Carbon::parse($state)->format('H:i') . ' WITA'
                        : '—'
                    )
                    ->alignCenter()
                    ->icon('heroicon-m-clock')
                    ->color('gray')
                    ->toggleable(),
            ])

            ->defaultSort('tanggal_keluar', 'desc')

            ->filters([
                Filter::make('bulan_ini')
                    ->label('Hanya Bulan Ini')
                    ->toggle()
                    ->default(true)
                    ->query(function (Builder $query, array $data): Builder {
                        if (! ($data['isActive'] ?? false)) return $query;
                        return $query
                            ->whereMonth('tanggal_keluar', now()->month)
                            ->whereYear('tanggal_keluar', now()->year);
                    })
                    ->indicateUsing(fn (array $data): ?string =>
                        ($data['isActive'] ?? false)
                            ? 'Bulan ini: ' . now()->locale('id')->isoFormat('MMMM Y')
                            : null
                    ),

                Filter::make('belum_ttd')
                    ->label('Belum Ditandatangani')
                    ->toggle()
                    ->query(function (Builder $query, array $data): Builder {
                        if (! ($data['isActive'] ?? false)) return $query;
                        return $query->whereNull('ttd');
                    })
                    ->indicateUsing(fn (array $data): ?string =>
                        ($data['isActive'] ?? false) ? '✍ Belum ada TTD' : null
                    ),

                Filter::make('tanggal_keluar')
                    ->label('Tanggal Tertentu')
                    ->form([
                        DatePicker::make('date')
                            ->label('Tanggal Keluar')
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder =>
                        $query->when(
                            $data['date'],
                            fn ($q, $date) => $q->whereDate('tanggal_keluar', $date)
                        )
                    )
                    ->indicateUsing(fn (array $data): ?string =>
                        $data['date']
                            ? 'Tgl: ' . Carbon::parse($data['date'])->isoFormat('D MMMM Y')
                            : null
                    ),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\Action::make('esign')
                    ->label('E-Sign')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->button()
                    ->url(fn (Booking $record) => static::getUrl('edit', ['record' => $record->id])),
            ])

            ->bulkActions([])

            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginated()

            // Row highlight berdasarkan status TTD
            ->recordClasses(fn (Booking $record): string =>
                $record->ttd
                    ? 'opacity-70'
                    : 'bg-amber-50/50 dark:bg-amber-950/10'
            );
    }

    // ─────────────────────────────────────────
    //  PAGES
    // ─────────────────────────────────────────
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgreements::route('/'),
            'edit'  => Pages\EditAgreement::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
