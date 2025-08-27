<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TempoResource\Pages;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Tempo;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class TempoResource extends Resource
{
    protected static ?string $model = Tempo::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Manajemen Mobil';
    protected static ?string $navigationLabel = 'Jatuh Tempo ';
    protected static ?string $modelLabel = 'Jatuh Tempo';
    protected static ?string $pluralModelLabel = 'Daftar Jatuh Tempo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Dependent Dropdown untuk memilih mobil
                Forms\Components\Select::make('brand_id')
                    ->label('Merek')
                    ->options(Brand::query()->pluck('name', 'id'))
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set) {
                        $set('car_model_id', null);
                        $set('car_id', null);
                    })
                    ->dehydrated(false), // Field ini virtual, tidak disimpan

                Forms\Components\Select::make('car_model_id')
                    ->label('Nama Mobil')
                    ->options(fn (Forms\Get $get): array => CarModel::query()
                        ->where('brand_id', $get('brand_id'))
                        ->pluck('name', 'id')->all()
                    )
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('car_id', null))
                    ->dehydrated(false), // Field ini juga virtual

                Forms\Components\Select::make('car_id')
                    ->label('Unit Mobil (No Polisi)')
                    ->options(fn (Forms\Get $get): array => Car::query()
                        ->where('car_model_id', $get('car_model_id'))
                        ->pluck('nopol', 'id')->all()
                    )
                    ->live()
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('perawatan')
                    ->label('Jenis Perawatan')
                    ->options([
                        'pajak' => 'Pajak',
                        'service' => 'Service',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('jatuh_tempo')
                    ->label('Tanggal Jatuh Tempo')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection(),
                    Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('car.nopol')
                    ->label('Mobil')
                    ->formatStateUsing(function (Model $record): HtmlString {
                        $car = $record->car;
                        // Pengecekan untuk menghindari error jika relasi tidak lengkap
                        if (!$car || !$car->carModel || !$car->carModel->brand) {
                            return new HtmlString('Data Mobil Tidak Lengkap');
                        }

                        $brandName = $car->carModel->brand->name;
                        $modelName = $car->carModel->name;
                        $nopol = $car->nopol;

                        $badge = "<span class='bg-primary-500 text-white text-xs font-semibold ms-2 px-2.5 py-0.5 rounded-md'>{$nopol}</span>";
                        $carName = "{$brandName} {$modelName}";

                        // Tampilkan nama mobil dan badge nopol di bawahnya
                        return new HtmlString("<div><p class='font-medium'>{$carName}</p>{$badge}</div>");
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        // Pencarian kustom yang mencari di relasi
                        return $query->whereHas('car', function ($carQuery) use ($search) {
                            $carQuery->where('nopol', 'like', "%{$search}%")
                                ->orWhereHas('carModel', function ($modelQuery) use ($search) {
                                    $modelQuery->where('name', 'like', "%{$search}%")
                                        ->orWhereHas('brand', function ($brandQuery) use ($search) {
                                            $brandQuery->where('name', 'like', "%{$search}%");
                                        });
                                });
                        });
                    }),

                TextColumn::make('perawatan')
                    ->label('Pajak + Service')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'primary' => 'pajak',
                        'danger' => 'service',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pajak' => 'Pajak STNK',
                        'service' => 'Service Berkala',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')->alignCenter()
                    ->sortable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('perawatan')
                    ->options([
                        'pajak' => 'Pajak',
                        'service' => 'Service',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

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
        // Semua peran bisa melihat daftar mobil
        return true;
    }

    public static function canCreate(): bool
    {
        // Hanya superadmin dan admin yang bisa membuat data baru
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa mengedit
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDelete(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa menghapus
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDeleteAny(): bool
    {
        // Hanya superadmin dan admin yang bisa hapus massal
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
