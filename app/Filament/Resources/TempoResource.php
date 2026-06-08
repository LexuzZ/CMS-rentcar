<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TempoResource\Pages;
use App\Models\Car;
use App\Models\Tempo;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TempoResource extends Resource
{
    protected static ?string $model = Tempo::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Manajemen Mobil';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Jatuh Tempo';
    protected static ?string $modelLabel = 'Jatuh Tempo';
    protected static ?string $pluralModelLabel = 'Daftar Jatuh Tempo';

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        $count = Tempo::whereDate('jatuh_tempo', '<=', now()->addDays(30))->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $hasOverdue = Tempo::whereDate('jatuh_tempo', '<', today())->exists();
        return $hasOverdue ? 'danger' : 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Jatuh tempo dalam 30 hari ke depan';
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Kendaraan & Jenis Perawatan')
                ->icon('heroicon-o-truck')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('car_id')
                        ->label('Mobil')
                        ->relationship(
                            name: 'car',
                            titleAttribute: 'nopol',
                            modifyQueryUsing: fn(Builder $query) => $query
                                ->where('garasi', 'SPT')
                                ->with('carModel')
                        )
                        ->getOptionLabelFromRecordUsing(fn(Car $record) => "{$record->carModel->name} ({$record->nopol})")
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search) {
                            return Car::query()
                                ->where('garasi', 'SPT')
                                ->where(function ($query) use ($search) {
                                    $query->where('nopol', 'like', "%{$search}%")
                                        ->orWhereHas('carModel', fn($q) => $q->where('name', 'like', "%{$search}%"));
                                })
                                ->with('carModel')
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn($car) => [
                                    $car->id => "{$car->carModel->name} ({$car->nopol})"
                                ]);
                        })
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('perawatan')
                        ->label('Jenis Perawatan')
                        ->options([
                            'pajak' => 'Pajak STNK',
                            'service' => 'Service Berkala',
                        ])
                        ->required(),
                ]),

            Forms\Components\Section::make('Jadwal & Keterangan')
                ->icon('heroicon-o-calendar-days')
                ->columns(1)
                ->schema([
                    Forms\Components\DatePicker::make('jatuh_tempo')
                        ->label('Tanggal Jatuh Tempo')
                        ->required()
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->closeOnDateSelection()
                        ->helperText('Masukkan tanggal jatuh tempo pajak atau service.'),

                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi / Catatan')
                        ->placeholder('Contoh: Pajak tahunan, perpanjang STNK 5 tahun, dsb.')
                        ->rows(3),
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

                // Mobil + nopol
                Tables\Columns\TextColumn::make('car.carModel.name')
                    ->label('Kendaraan')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->description(fn(Tempo $record): string => $record->car->nopol ?? '—')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('car.carModel', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('car', fn($q) => $q->where('nopol', 'like', "%{$search}%"));
                    }),

                // Jenis perawatan — badge + ikon
                Tables\Columns\TextColumn::make('perawatan')
                    ->label('Jenis')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn(string $state): string => match ($state) {
                        'pajak' => 'heroicon-m-document-text',
                        'service' => 'heroicon-m-wrench-screwdriver',
                        default => 'heroicon-m-cog-6-tooth',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pajak' => 'primary',
                        'service' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pajak' => 'Pajak STNK',
                        'service' => 'Service Berkala',
                        default => ucfirst($state),
                    }),

                // Jatuh tempo + urgensi
                Tables\Columns\TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->alignCenter()
                    ->sortable()
                    ->icon('heroicon-m-clock')
                    ->color(fn($state): string => match (true) {
                        Carbon::parse($state)->isPast() => 'danger',
                        Carbon::parse($state)->diffInDays(now(), false) >= -7 => 'warning',
                        default => 'success',
                    })
                    ->description(fn($state): string => match (true) {
                        Carbon::parse($state)->isPast() => '⚠ Sudah lewat jatuh tempo',
                        Carbon::parse($state)->isToday() => '📌 Hari ini',
                        default => Carbon::parse($state)->locale('id')->diffForHumans(),
                    }),

                // Deskripsi (toggleable)
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->placeholder('—')
                    ->limit(50)
                    ->tooltip(fn($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->defaultSort('jatuh_tempo', 'asc') // urutkan dari yang paling dekat

            ->filters([
                Tables\Filters\SelectFilter::make('perawatan')
                    ->options([
                        'pajak' => 'Pajak STNK',
                        'service' => 'Service Berkala',
                    ]),

                Tables\Filters\Filter::make('overdue')
                    ->label('Sudah Lewat Jatuh Tempo')
                    ->query(
                        fn(Builder $query) =>
                        $query->whereDate('jatuh_tempo', '<', now()->toDateString())
                    ),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Jatuh Tempo Bulan Ini')
                    ->query(
                        fn(Builder $query) =>
                        $query
                            ->whereMonth('jatuh_tempo', now()->month)
                            ->whereYear('jatuh_tempo', now()->year)
                    ),

                Tables\Filters\Filter::make('upcoming_30')
                    ->label('30 Hari ke Depan')
                    ->query(
                        fn(Builder $query) =>
                        $query
                            ->whereDate('jatuh_tempo', '>=', today())
                            ->whereDate('jatuh_tempo', '<=', today()->addDays(30))
                    ),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\EditAction::make()
                    //     ->label('Edit')
                    //     ->icon('heroicon-o-pencil-square')
                    //     ->color('warning'),

                    Tables\Actions\Action::make('selesaikan')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Tandai Selesai?')
                        ->modalDescription('Data jatuh tempo ini akan dihapus setelah ditandai selesai.')
                        ->modalSubmitActionLabel('Ya, Selesai')
                        ->action(fn(Tempo $record) => $record->delete())
                        ->visible(fn() => Auth::user()->hasAnyRole(['superadmin', 'admin'])),

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
            ->paginated([10, 25, 50])

            // Baris overdue diberi highlight merah
            ->recordClasses(fn(Tempo $record): string => match (true) {
                Carbon::parse($record->jatuh_tempo)->isPast()
                => 'bg-red-50 dark:bg-red-950/20',
                Carbon::parse($record->jatuh_tempo)->diffInDays(now(), false) >= -7
                => 'bg-amber-50 dark:bg-amber-950/20',
                default => '',
            });
    }

    // ─────────────────────────────────────────
    //  PAGES & ACCESS
    // ─────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTempos::route('/'),
            'create' => Pages\CreateTempo::route('/create'),
            'edit' => Pages\EditTempo::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
