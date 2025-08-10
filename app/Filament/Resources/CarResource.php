<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\BookingsResource\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\CarResource\Pages;
use App\Filament\Resources\CarResource\RelationManagers\TempoRelationManager;
use App\Models\Car;
use App\Models\CarModel;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\{TextInput, Select, FileUpload, Grid};
use Filament\Tables\Columns\{TextColumn, ImageColumn};

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
                    // Dropdown Merek (menggunakan relasi)
                    Select::make('brand_id') // Field "virtual" untuk state
                        ->label('Merek')
                        ->relationship(name: 'carModel.brand', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn(Forms\Set $set) => $set('car_model_id', null))
                        ->dehydrated(false), // <-- SOLUSINYA DI SINI

                    // Dropdown Nama Mobil (dinamis berdasarkan merek)
                    Select::make('car_model_id') // Kolom asli di database
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
                        ->unique(ignoreRecord: true),

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
                        ->panelAspectRatio('2:1')
                        ->panelLayout('integrated')
                        ->disk('public')
                        ->visibility('public')
                        ->columnSpanFull(), // Agar foto mengambil lebar penuh
                ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->label('Foto')->width(80)->height(50)->toggleable()->alignCenter(),
                TextColumn::make('nopol')->label('Nopol')->searchable()->alignCenter(),

                // Mengambil nama mobil dari relasi carModel
                TextColumn::make('carModel.name')->label('Nama Mobil')->searchable()->alignCenter(),

                // Mengambil merek dari relasi carModel.brand
                TextColumn::make('carModel.brand.name')
                    ->label('Merk Mobil')
                    ->badge()
                    ->alignCenter()
                    
                    ->searchable(),

                TextColumn::make('warna')->label('Warna Mobil')->searchable()->alignCenter(),
                TextColumn::make('garasi')->label('Garasi')->toggleable()->alignCenter()->searchable(),
                TextColumn::make('year')->label('Tahun')->toggleable()->alignCenter(),
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
                TextColumn::make('transmisi')
                    ->label('Transmisi')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'manual',
                        'info' => 'matic',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'manual' => 'Manual Transmisi',
                        'matic' => 'Automatic Transmisi',
                        default => ucfirst($state),
                    }),
                TextColumn::make('harga_harian')->label('Harian')->money('IDR')->alignCenter(),
                TextColumn::make('harga_pokok')->label('Pokok')->money('IDR')->toggleable()->alignCenter(),
            ])
            ->defaultSort('status', 'asc')
            ->filters([])
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
        // DAFTARKAN RELATION MANAGER DI SINI
        return [
            BookingsRelationManager::class,
            TempoRelationManager::class,
        ];
    }
    public static function getWidgets(): array
    {
        return [
            // Jika ada widget, biarkan di sini
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
    // app/Filament/Resources/CarResource.php

    // Staff boleh melihat daftar mobil
    public static function canViewAny(): bool
    {
        return true;
    }

    // Hanya admin yang bisa membuat data mobil baru
    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    // Hanya admin yang bisa mengedit data mobil
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    // Hanya admin yang bisa menghapus data mobil
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->isAdmin();
    }
}
