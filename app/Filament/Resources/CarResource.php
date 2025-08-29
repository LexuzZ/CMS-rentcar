<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\BookingsResource\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\CarResource\Pages; // <-- Pastikan ini di-import
use App\Filament\Resources\CarResource\RelationManagers\ServiceHistoriesRelationManager;
use App\Filament\Resources\CarResource\RelationManagers\TempoRelationManager;
use App\Models\Car;
use App\Models\CarModel;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\{TextInput, Select, FileUpload, Grid, DatePicker};
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
    protected static ?string $navigationGroup = 'Data ';
    protected static ?string $label = 'Mobil';
    protected static ?string $pluralLabel = 'Data Mobil';

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

                    FileUpload::make('photo')
                        ->label('Foto Mobil')
                        ->image()
                        ->directory('cars')
                        ->imagePreviewHeight('150')
                        ->loadingIndicatorPosition('left')
                        // ->panelAspectRatio('2:1')
                        ->panelLayout('integrated')
                        ->disk('public')
                        ->visibility('public')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('nopol')->label('Nopol')->sortable()->searchable(),
                TextColumn::make('carModel.name')->label('Nama Mobil')->sortable()->searchable()->alignCenter(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'ready',
                        'info' => 'disewa',
                        'danger' => 'perawatan',
                        'gray' => 'nonaktif',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'ready' => 'Ready',
                        'disewa' => 'Disewa',
                        'perawatan' => 'Maintenance',
                        'nonaktif' => 'Nonaktif',
                        default => ucfirst($state),
                    }),
                TextColumn::make('harga_harian')->label('Harian')->money('IDR')->alignCenter(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('availability')
                    ->label('Cek Ketersediaan Mobil')
                    ->form([
                        DatePicker::make('start_date')->label('Dari Tanggal'),
                        DatePicker::make('end_date')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $startDate = $data['start_date'];
                        $endDate = $data['end_date'];

                        if (!$startDate || !$endDate) {
                            return $query;
                        }

                        return $query
                            ->whereNotIn('status', ['perawatan', 'nonaktif'])
                            ->whereDoesntHave('bookings', function (Builder $bookingQuery) use ($startDate, $endDate) {
                                $bookingQuery->where(function (Builder $q) use ($startDate, $endDate) {
                                    $q->whereBetween('tanggal_keluar', [$startDate, $endDate])
                                        ->orWhereBetween('tanggal_kembali', [$startDate, $endDate])
                                        ->orWhere(function (Builder $subQ) use ($startDate, $endDate) {
                                            $subQ->where('tanggal_keluar', '<=', $startDate)
                                                ->where('tanggal_kembali', '>=', $endDate);
                                        });
                                });
                            });
                    }),
            ])
            ->headerActions([
                Action::make('copyList')
                    ->label('Copy Daftar Mobil')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->visible(function (Pages\ListCars $livewire): bool {
                        $filters = $livewire->tableFilters;
                        return !empty($filters['availability']['start_date']) && !empty($filters['availability']['end_date']);
                    })
                    // Membuka modal dengan konten dari file Blade
                    ->modalContent(function (Pages\ListCars $livewire): View {
                        $cars = $livewire->getFilteredTableQuery()
                            ->where('garasi', 'SPT')
                            ->get();

                        $filters = $livewire->tableFilters;
                        $startDate = \Carbon\Carbon::parse($filters['availability']['start_date'])->locale('id')->isoFormat('D MMMM Y');
                        $endDate = \Carbon\Carbon::parse($filters['availability']['end_date'])->locale('id')->isoFormat('D MMMM Y');

                        $textToCopy = "Halo,✋ Lombok 😊\nMobil yang tersedia di Garasi Semeton Pesiar periode  *{$startDate}* sampai *{$endDate}*:\n\n";
                        foreach ($cars as $index => $car) {
                            $textToCopy .= ($index + 1) . ". *{$car->carModel->brand->name} {$car->carModel->name}* - {$car->nopol}\n";
                        }
                        $textToCopy .= "\nInfo lebih lanjut bisa hubungi kami. Terima kasih.\n\nWA: 081907367197\nWebsite: www.semetonpesiar.com";

                        return view('filament.actions.copy-car-list', ['textToCopy' => $textToCopy]);
                    })
                    // Menyembunyikan tombol default modal
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

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
        return [
            //
        ];
    }
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

    // -- KONTROL AKSES BARU (superadmin, admin, staff) --

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
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
