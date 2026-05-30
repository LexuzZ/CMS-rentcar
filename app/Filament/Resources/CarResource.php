<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\BookingsResource\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\CarResource\Pages;
use App\Filament\Resources\CarResource\RelationManagers\ServiceHistoriesRelationManager;
use App\Filament\Resources\CarResource\RelationManagers\TempoRelationManager;
use App\Models\Car;
use App\Models\CarModel;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\{TextInput, Select, FileUpload, Grid, DatePicker, DateTimePicker, TimePicker};
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\{TextColumn, ImageColumn};
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Manajemen Mobil';
    protected static ?int $navigationSort = 3;
    protected static ?string $label = 'Mobil';
    protected static ?string $pluralLabel = 'Data Mobil';

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('brand_id')
                        ->label('Merek')
                        ->relationship(name: 'carModel.brand', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn(Forms\Set $set) => $set('car_model_id', null))
                        ->dehydrated(false),

                    Select::make('car_model_id')
                        ->label('Nama Mobil')
                        ->options(
                            fn(Forms\Get $get): array => CarModel::query()
                                ->where('brand_id', $get('brand_id'))
                                ->pluck('name', 'id')->all()
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required(),

                    TextInput::make('nopol')
                        ->label('Nomor Polisi')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->validationMessages([
                            'unique' => 'Nomor polisi ini sudah terdaftar di sistem.',
                        ]),

                    TextInput::make('year')
                        ->label('Tahun')
                        ->numeric()
                        ->minValue(2000)
                        ->maxValue(date('Y') + 1)
                        ->required(),

                    TextInput::make('garasi')
                        ->label('Garasi')
                        ->required(),

                    TextInput::make('warna')
                        ->label('Warna Mobil')
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'ready' => 'Ready',
                            'disewa' => 'Disewa',
                            'perawatan' => 'Perawatan',
                            'nonaktif' => 'Nonaktif',
                        ])
                        ->default('ready')
                        ->required(),

                    Select::make('transmisi')
                        ->label('Transmisi')
                        ->options([
                            'matic' => 'Matic',
                            'manual' => 'Manual',
                        ])
                        ->default('matic')
                        ->required(),

                    TextInput::make('harga_harian')
                        ->label('Harga Sewa Harian')
                        ->numeric()
                        ->required()
                        ->prefix('Rp'),

                    TextInput::make('harga_pokok')
                        ->label('Harga Pokok')
                        ->numeric()
                        ->required()
                        ->prefix('Rp'),

                    TextInput::make('harga_bulanan')
                        ->label('Harga Sewa Bulanan')
                        ->numeric()
                        ->prefix('Rp'),

                    TextInput::make('harga_vendor')
                        ->label('Harga Investor')
                        ->numeric()
                        ->prefix('Rp'),

                    FileUpload::make('photo')
                        ->label('Foto Mobil')
                        ->image()
                        ->directory('cars')
                        ->imagePreviewHeight('150')
                        ->loadingIndicatorPosition('left')
                        ->panelLayout('integrated')
                        ->disk('public')
                        ->visibility('public')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    // ─────────────────────────────────────────
    //  TABLE
    // ─────────────────────────────────────────
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([

                // Foto thumbnail


                // Nopol + nama mobil dalam satu kolom
                TextColumn::make('nopol')
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->description(
                        fn(Car $record): string =>
                        ($record->carModel?->brand?->name ?? '') . ' ' . ($record->carModel?->name ?? '—')
                    ),

                // Garasi
                TextColumn::make('garasi')
                    ->label('Garasi')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                // Transmisi
                TextColumn::make('transmisi')
                    ->label('Transmisi')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(string $state): string => match ($state) {
                        'matic' => 'info',
                        'manual' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                // Status
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(string $state): string => match ($state) {
                        'ready' => 'success',
                        'disewa' => 'info',
                        'perawatan' => 'danger',
                        'nonaktif' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'ready' => 'heroicon-m-check-circle',
                        'disewa' => 'heroicon-m-key',
                        'perawatan' => 'heroicon-m-wrench-screwdriver',
                        'nonaktif' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'ready' => 'Ready',
                        'disewa' => 'Disewa',
                        'perawatan' => 'Maintenance',
                        'nonaktif' => 'Nonaktif',
                        default => ucfirst($state),
                    }),

                // Harga harian
                TextColumn::make('harga_harian')
                    ->label('Harga / Hari')
                    ->alignEnd()
                    ->sortable()
                    ->color('success')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                // Tahun – tersembunyi di mobile, tampil di desktop
                TextColumn::make('year')
                    ->label('Tahun')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->badge()
                    ->color('gray'),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Filter::make('availability')
                    ->label('Cek Ketersediaan Mobil')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('start_date')
                                ->label('Dari Tanggal')
                                ->native(false)
                                ->minDate(today())
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set, $state) {
                                    // Reset end_date jika lebih awal dari start_date
                                    $set('end_date', null);
                                }),

                            DatePicker::make('end_date')
                                ->label('Sampai Tanggal')
                                ->native(false)
                                ->minDate(fn(Forms\Get $get) => $get('start_date') ?? today())
                                ->live(),
                        ]),
                        Grid::make(2)->schema([
                            TimePicker::make('start_time')
                                ->label('Jam Keluar')
                                ->default('08:00')
                                ->withoutSeconds()
                                ->live(),

                            TimePicker::make('end_time')
                                ->label('Jam Kembali')
                                ->default('20:00')
                                ->withoutSeconds()
                                ->live(),
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // Semua field harus terisi sebelum filter dijalankan
                        if (
                            blank($data['start_date']) ||
                            blank($data['end_date']) ||
                            blank($data['start_time']) ||
                            blank($data['end_time'])
                        ) {
                            return $query;
                        }

                        $start = Carbon::parse($data['start_date'] . ' ' . $data['start_time']);
                        $end = Carbon::parse($data['end_date'] . ' ' . $data['end_time']);

                        // Pastikan rentang valid (end harus setelah start)
                        if ($end->lte($start)) {
                            return $query;
                        }

                        return $query
                            // Hanya tampilkan mobil yang statusnya memungkinkan untuk disewa
                            ->whereNotIn('status', ['perawatan', 'nonaktif'])

                            // Tidak ada booking aktif yang tumpang tindih dengan rentang waktu ini
                            ->whereDoesntHave('bookings', function (Builder $q) use ($start, $end) {
                                $q
                                    // Hanya booking yang masih aktif (bukan selesai / batal)
                                    ->whereNotIn('status', ['selesai', 'batal', 'cancelled'])

                                    // Logika overlap: booking bentrok jika
                                    // tanggal_keluar < end DAN tanggal_kembali > start
                                    ->where('tanggal_keluar', '<', $end)
                                    ->where('tanggal_kembali', '>', $start);
                            });
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if (
                            filled($data['start_date']) &&
                            filled($data['end_date']) &&
                            filled($data['start_time']) &&
                            filled($data['end_time'])
                        ) {
                            $start = Carbon::parse($data['start_date'] . ' ' . $data['start_time'])
                                ->locale('id')->isoFormat('D MMM Y, HH:mm');
                            $end = Carbon::parse($data['end_date'] . ' ' . $data['end_time'])
                                ->locale('id')->isoFormat('D MMM Y, HH:mm');

                            $indicators[] = Tables\Filters\Indicator::make("Tersedia: {$start} → {$end}")
                                ->removeField('start_date')  // klik × akan reset semua field
                                ->removeField('start_time')
                                ->removeField('end_date')
                                ->removeField('end_time');
                        }

                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'ready' => 'Ready',
                        'disewa' => 'Disewa',
                        'perawatan' => 'Maintenance',
                        'nonaktif' => 'Nonaktif',
                    ]),

                Tables\Filters\SelectFilter::make('transmisi')
                    ->label('Transmisi')
                    ->options([
                        'matic' => 'Matic',
                        'manual' => 'Manual',
                    ]),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->headerActions([
                Action::make('copyList')
                    ->label('Copy Daftar Mobil')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->visible(function (Pages\ListCars $livewire): bool {
                        $filters = $livewire->tableFilters;
                        return !empty($filters['availability']['start_date']) &&
                            !empty($filters['availability']['end_date']);
                    })
                    ->modalContent(function (Pages\ListCars $livewire): View {
                        $cars = $livewire->getFilteredTableQuery()
                            ->where('garasi', 'SPT')
                            ->with('carModel')
                            ->get()
                            ->sortBy(fn($car) => $car->carModel->name, SORT_NATURAL | SORT_FLAG_CASE)
                            ->values();

                        $filters = $livewire->tableFilters;
                        $startDateTime = Carbon::parse($filters['availability']['start_date'] . ' ' . $filters['availability']['start_time'])
                            ->locale('id')->isoFormat('D MMMM Y, HH:mm');
                        $endDateTime = Carbon::parse($filters['availability']['end_date'] . ' ' . $filters['availability']['end_time'])
                            ->locale('id')->isoFormat('D MMMM Y, HH:mm');

                        $textToCopy = "Halo,✋ Lombok 😊\nMobil yang tersedia di Garasi Semeton Pesiar periode *{$startDateTime}* sampai *{$endDateTime}* :\n\n";
                        foreach ($cars as $index => $car) {
                            $textToCopy .= ($index + 1) . ". *{$car->carModel->brand->name} {$car->carModel->name}* {$car->nopol} ✅\n";
                        }
                        $textToCopy .= "\nInfo lebih lanjut bisa hubungi kami. Terima kasih.\n\n📞  WA: 081128948884\n🌐  Website: www.semetonpesiar.com";

                        return view('filament.actions.copy-car-list', ['textToCopy' => $textToCopy]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
            ])

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info'),

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
    //  PAGES & RELATIONS
    // ─────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
            'view' => Pages\ViewCar::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            BookingsRelationManager::class,
            ServiceHistoriesRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [];
    }

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('status', 'ready')
            ->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Mobil yang siap disewa';
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    // ─────────────────────────────────────────
    //  ACCESS CONTROL
    // ─────────────────────────────────────────
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
