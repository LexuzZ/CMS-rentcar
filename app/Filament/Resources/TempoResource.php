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
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('car.carModel.name')
                    ->label('Mobil')
                    ->description(fn(Tempo $record): string => $record->car->nopol)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('car.carModel', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('car', fn($q) => $q->where('nopol', 'like', "%{$search}%"));
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
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Edit Jatuh Tempo')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->hiddenLabel()
                    ->button(),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Hapus Jatuh Tempo')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->hiddenLabel()
                    ->button(),
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
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
