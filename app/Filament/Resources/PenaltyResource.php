<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenaltyResource\Pages;
use App\Models\Penalty;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PenaltyResource extends Resource
{
    protected static ?string $model = Penalty::class;

    protected static ?string $navigationIcon  = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Klaim Garasi';
    protected static ?int    $navigationSort  = 4;

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        $count = Penalty::whereMonth('created_at', now()->month)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Klaim bulan ini';
    }

    // ─────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────
    private static function klaimOptions(): array
    {
        return [
            'baret'      => 'Baret / Kerusakan',
            'bbm'        => 'BBM',
            'overtime'   => 'Overtime',
            'overland'   => 'Overland',
            'washer'     => 'Washer / Cuci Mobil',
            'event'      => 'Event',
            'check_in'   => 'Check In',
            'check_out'  => 'Check Out',
            'no_penalty' => 'Tidak Ada Denda',
        ];
    }

    private static function klaimLabel(string $state): string
    {
        return self::klaimOptions()[$state] ?? ucfirst($state);
    }

    private static function klaimColor(string $state): string
    {
        return match ($state) {
            'baret'      => 'danger',
            'bbm'        => 'warning',
            'overtime'   => 'warning',
            'overland'   => 'info',
            'washer'     => 'primary',
            'event'      => 'success',
            'check_in'   => 'info',
            'check_out'  => 'gray',
            'no_penalty' => 'gray',
            default      => 'gray',
        };
    }

    private static function klaimIcon(string $state): string
    {
        return match ($state) {
            'baret'      => 'heroicon-m-exclamation-triangle',
            'bbm'        => 'heroicon-m-beaker',
            'overtime'   => 'heroicon-m-clock',
            'overland'   => 'heroicon-m-map',
            'washer'     => 'heroicon-m-sparkles',
            'event'      => 'heroicon-m-star',
            'check_in'   => 'heroicon-m-arrow-right-circle',
            'check_out'  => 'heroicon-m-arrow-left-circle',
            'no_penalty' => 'heroicon-m-check-circle',
            default      => 'heroicon-m-tag',
        };
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Referensi Booking')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Select::make('booking_id')
                        ->label('Booking')
                        ->relationship('booking', 'id')
                        ->getOptionLabelFromRecordUsing(function ($record) {
                            $keluar  = Carbon::parse($record->tanggal_keluar)->format('d M Y');
                            $kembali = Carbon::parse($record->tanggal_kembali)->format('d M Y');
                            return '#BK' . str_pad($record->id, 3, '0', STR_PAD_LEFT)
                                . ' · ' . $record->customer->nama
                                . ' · ' . $record->car->nopol
                                . ' (' . $keluar . ' – ' . $kembali . ')';
                        })
                        ->required()
                        ->searchable()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Detail Klaim')
                ->icon('heroicon-o-exclamation-triangle')
                ->columns(2)
                ->schema([
                    Select::make('klaim')
                        ->label('Jenis Klaim')
                        ->options(self::klaimOptions())
                        ->default('no_penalty')
                        ->required()
                        ->native(false),

                    TextInput::make('amount')
                        ->label('Nominal Denda')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->placeholder('0'),

                    Textarea::make('description')
                        ->label('Deskripsi / Keterangan')
                        ->rows(3)
                        ->placeholder('Contoh: Baret pada bumper kiri, terjadi saat parkir...')
                        ->columnSpanFull(),
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

                // Nomor booking
                TextColumn::make('booking.id')
                    ->label('Booking')
                    ->formatStateUsing(fn ($state) => '#BK' . str_pad($state, 3, '0', STR_PAD_LEFT))
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                // Penyewa + Mobil dalam satu kolom
                TextColumn::make('booking.customer.nama')
                    ->label('Penyewa')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->searchable()
                    ->description(fn (Penalty $record): string =>
                        ($record->booking->car->carModel->name ?? '—') .
                        ' · ' . ($record->booking->car->nopol ?? '—')
                    ),

                // Jenis klaim — badge berwarna + ikon
                TextColumn::make('klaim')
                    ->label('Jenis Klaim')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn (string $state): string => self::klaimIcon($state))
                    ->color(fn (string $state): string => self::klaimColor($state))
                    ->formatStateUsing(fn ($state): string => self::klaimLabel($state)),

                // Nominal — warna merah jika > 0
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->alignEnd()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->color(fn ($state): string => $state > 0 ? 'danger' : 'gray')
                    ->formatStateUsing(fn ($state) => $state > 0
                        ? 'Rp ' . number_format($state, 0, ',', '.')
                        : '—'
                    ),

                // Tanggal dibuat
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->alignCenter()
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\SelectFilter::make('klaim')
                    ->label('Jenis Klaim')
                    ->options(self::klaimOptions())
                    ->searchable(),



                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->toggle()
                    ->query(fn (Builder $q) => $q
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                    )
                    ->indicateUsing(fn (array $data): ?string =>
                        $data['isActive']
                            ? 'Bulan ini: ' . now()->locale('id')->isoFormat('MMMM Y')
                            : null
                    ),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning'),

                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(\Filament\Support\Enums\ActionSize::Small)
                ->color('gray')
                ->button(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])

            ->striped()
            ->paginated([10, 25, 50]);
    }

    // ─────────────────────────────────────────
    //  PAGES & ACCESS
    // ─────────────────────────────────────────
    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPenalties::route('/'),
            'create' => Pages\CreatePenalty::route('/create'),
            'edit'   => Pages\EditPenalty::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
