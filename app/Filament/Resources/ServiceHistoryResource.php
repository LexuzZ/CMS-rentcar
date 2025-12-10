<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceHistoryResource\Pages;
use App\Models\Car;
use App\Models\ServiceHistory;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ServiceHistoryResource extends Resource
{
    protected static ?string $model = ServiceHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Manajemen Mobil';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Riwayat Service';
    protected static ?string $pluralModelLabel = 'Riwayat Service';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('car', function (Builder $query) {
                $query->where('garasi', 'SPT');
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // PERBAIKAN 2: Memfilter dropdown mobil
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



                Forms\Components\DatePicker::make('service_date')
                    ->label('Tanggal Service')
                    ->required(),
                Forms\Components\Select::make('jenis_service')
                    ->label('Jenis Service')
                    ->options(['service' => 'Service & Tune Up', 'oli_mesin' => 'Oli Mesin', 'oli_transmisi' => 'Oli Transmisi', 'ganti_aki' => 'Pergantian Aki', 'ganti_ban' => 'Pergantian Ban'])
                    ->default('service') // Mengubah default agar valid
                    ->required(),
                Forms\Components\TextInput::make('current_km')
                    ->label('KM Saat Ini')
                    ->numeric()
                    ->nullable(),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('workshop')
                    ->label('Nama Bengkel'),
                Forms\Components\TextInput::make('next_km')
                    ->label('KM Service Berikutnya')
                    ->numeric()
                    ->nullable(),
                Forms\Components\DatePicker::make('next_service_date')
                    ->label('Tanggal Service Berikutnya')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('car.carModel.name')
                    ->label('Mobil')
                    ->description(fn(ServiceHistory $record): string => $record->car->nopol)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('car.carModel', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('car', fn($q) => $q->where('nopol', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('service_date')
                    ->label('Tgl. Service')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_service')
                    ->label('Jenis Service')
                    ->badge()->alignCenter()
                    ->colors(['danger' => 'service', 'info' => 'ganti_aki', 'primary' => 'ganti_ban', 'warning' => 'oli_mesin', 'success' => 'oli_transmisi'])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'service' => 'Service & Tune Up',
                        'ganti_aki' => 'Pergantian Aki',
                        'ganti_ban' => 'Pergantian Ban',
                        'oli_mesin' => 'Oli Mesin',
                        'oli_transmisi' => 'Oli Transmisi',
                        default => ucfirst($state)
                    })->wrap()
                    ->width(150),
                Tables\Columns\TextColumn::make('next_km')
                    ->label('Next Km')
                    ->numeric(),
                Tables\Columns\TextColumn::make('workshop')
                    ->searchable()
                    ->wrap()
                    ->width(150),
                Tables\Columns\TextColumn::make('next_service_date')
                    ->label('Next Service')
                    ->date('d M Y')
                    ->sortable()
                    ->wrap()
                    ->width(150),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('bulan_ini')
                    ->label('Hanya Bulan Ini')
                    ->toggle() // Menjadikannya tombol on/off
                    ->default(true)
                    ->query(fn (Builder $query) => $query
                        ->whereMonth('next_service_date', Carbon::now()->month)
                        ->whereYear('next_service_date', Carbon::now()->year)
                    ),
                //
            ])
            ->actions([
                Tables\Actions\Action::make('addToTempo')
                    ->label('')
                    ->tooltip('Tambah ke Jatuh Tempo')
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->hiddenLabel()
                    ->button()
                    ->action(function (ServiceHistory $record) {
                        // Cek apakah sudah ada di Tempo untuk mobil yang sama
                        $existingTempo = \App\Models\Tempo::where('car_id', $record->car_id)
                            ->where('perawatan', 'service')
                            ->whereDate('jatuh_tempo', $record->next_service_date)
                            ->first();

                        if ($existingTempo) {
                            // GANTI: Pakai notifikasi
                            \Filament\Notifications\Notification::make()
                                ->title('Data Sudah Ada')
                                ->body('Jatuh tempo service untuk ' . $record->car->carModel->name . ' (' . $record->car->nopol . ') pada tanggal ' .
                                    \Carbon\Carbon::parse($record->next_service_date)->format('d M Y') .
                                    ' sudah ada di sistem Jatuh Tempo.')
                                ->warning()
                                ->send();
                            return;
                        }

                        // Buat data di Tempo
                        \App\Models\Tempo::create([
                            'car_id' => $record->car_id,
                            'perawatan' => 'service',
                            'jatuh_tempo' => $record->next_service_date,
                            'description' => "Service berikutnya - " . ($record->description ?: 'Service rutin'),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil!')
                            ->body('Data service berhasil ditambahkan ke Jatuh Tempo')
                            ->success()
                            ->send();
                    })
                    ->visible(
                        fn(ServiceHistory $record): bool =>
                        $record->next_service_date !== null &&
                        Auth::user()->hasAnyRole(['superadmin', 'admin'])
                    ),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Edit Riwayat Service')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->hiddenLabel()
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceHistories::route('/'),
            // PERBAIKAN DI SINI: Mengubah rute 'create' agar tidak konflik
            'create' => Pages\CreateServiceHistory::route('/create'),
            'edit' => Pages\EditServiceHistory::route('/{record}/edit'),
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
