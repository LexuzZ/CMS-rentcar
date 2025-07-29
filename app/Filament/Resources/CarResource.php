<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Models\Car;
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


                    Select::make('merek')
                        ->label('Merek')
                        ->options([
                            'toyota' => 'Toyota',
                            'mitsubishi' => 'Mitsubishi',
                            'suzuki' => 'Suzuki',
                            'daihatsu' => 'Daihatsu',
                            'honda' => 'Honda',
                        ])
                        ->default('toyota')
                        ->required(),

                    TextInput::make('garasi')
                        ->label('Garasi')
                        ->required(),

                    TextInput::make('nama_mobil')
                        ->label('Nama Mobil')
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'ready' => 'Ready',
                            'disewa' => 'Disewa',
                            'perawatan' => 'Perawatan',
                        ])
                        ->default('ready')
                        ->required(),





                    TextInput::make('harga_harian')
                        ->label('Harga Sewa Harian')
                        ->numeric()
                        ->prefix('Rp'),
                    TextInput::make('harga_pokok')
                        ->label('Harga Pokok')
                        ->numeric()
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
                ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')->label('Foto')->width(80)->height(50)->toggleable()->alignCenter(),
                TextColumn::make('nopol')->label('Nopol')->sortable()->searchable(),
                TextColumn::make('nama_mobil')->label('Nama Mobil')->sortable()->toggleable()->searchable()->alignCenter(),
                TextColumn::make('merek')
                    ->label('Merk Mobil')
                    ->badge()
                    ->alignCenter()
                    
                    ->formatStateUsing(fn($state) => match ($state) {
                        'toyota' => 'Toyota',
                        'mitsubishi' => 'Mitsubishi',
                        'suzuki' => 'Suzuki',
                        'honda' => 'Honda',
                        'daihatsu' => 'Daihatsu',
                        default => ucfirst($state),
                    }),
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
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'ready' => 'Ready',
                        'disewa' => 'Disewa',
                        'perawatan' => 'Maintenance',
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
        return [
            \App\Filament\Resources\CarResource\BookingsResource\RelationManagers\BookingsRelationManager::class,

        ];
    }
    public static function getWidgets(): array
    {
        return [
            \App\Filament\Resources\CarResource\Widgets\CarStatsOverview::class,
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query(Car::query()->latest())
            ->where('status', 'ready')
            ->count();
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Mobil yang siap disewa';
    }
}
