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

    // ─────────────────────────────────────────
    //  BASE QUERY
    // ─────────────────────────────────────────
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('car', fn(Builder $q) => $q->where('garasi', 'SPT'));
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Informasi Kendaraan')
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
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Select::make('jenis_service')
                        ->label('Jenis Service')
                        ->options([
                            'service' => 'Service & Tune Up',
                            'oli_mesin' => 'Oli Mesin',
                            'oli_transmisi' => 'Oli Transmisi',
                            'ganti_aki' => 'Pergantian Aki',
                            'ganti_ban' => 'Pergantian Ban',
                        ])
                        ->default('service')
                        ->required(),

                    Forms\Components\TextInput::make('workshop')
                        ->label('Nama Bengkel')
                        ->placeholder('Contoh: Bengkel Maju Jaya')
                        ->prefixIcon('heroicon-o-building-storefront'),
                ]),

            Forms\Components\Section::make('Detail Pelaksanaan')
                ->icon('heroicon-o-calendar-days')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('service_date')
                        ->label('Tanggal Service')
                        ->native(false)
                        ->required(),

                    Forms\Components\TextInput::make('current_km')
                        ->label('KM Saat Ini')
                        ->numeric()
                        ->suffix('km')
                        ->nullable(),

                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi Pekerjaan')
                        ->placeholder('Contoh: Ganti oli mesin + filter udara, cek rem...')
                        ->rows(3)
                        ->required()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Jadwal Berikutnya')
                ->icon('heroicon-o-arrow-path')
                ->columns(2)
                ->schema([
                    Forms\Components\DatePicker::make('next_service_date')
                        ->label('Tanggal Service Berikutnya')
                        ->native(false)
                        ->nullable(),

                    Forms\Components\TextInput::make('next_km')
                        ->label('KM Service Berikutnya')
                        ->numeric()
                        ->suffix('km')
                        ->nullable(),
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
                    ->description(fn(ServiceHistory $record): string => $record->car->nopol ?? '—')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('car.carModel', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('car', fn($q) => $q->where('nopol', 'like', "%{$search}%"));
                    }),

                // Tanggal service
                Tables\Columns\TextColumn::make('service_date')
                    ->label('Tgl. Service')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                // Jenis service — badge berwarna + ikon
                Tables\Columns\TextColumn::make('jenis_service')
                    ->label('Jenis')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn(string $state): string => match ($state) {
                        'service' => 'heroicon-m-wrench-screwdriver',
                        'oli_mesin' => 'heroicon-m-beaker',
                        'oli_transmisi' => 'heroicon-m-beaker',
                        'ganti_aki' => 'heroicon-m-bolt',
                        'ganti_ban' => 'heroicon-m-arrow-path',
                        default => 'heroicon-m-cog-6-tooth',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'service' => 'danger',
                        'oli_mesin' => 'warning',
                        'oli_transmisi' => 'success',
                        'ganti_aki' => 'info',
                        'ganti_ban' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'service' => 'Service & Tune Up',
                        'ganti_aki' => 'Ganti Aki',
                        'ganti_ban' => 'Ganti Ban',
                        'oli_mesin' => 'Oli Mesin',
                        'oli_transmisi' => 'Oli Transmisi',
                        default => ucfirst($state),
                    }),

                // Bengkel
                Tables\Columns\TextColumn::make('workshop')
                    ->label('Bengkel')
                    ->searchable()
                    ->placeholder('—')
                    ->icon('heroicon-m-building-storefront')
                    ->toggleable(isToggledHiddenByDefault: true),

                // KM service berikutnya

                Tables\Columns\TextColumn::make('current_km')
                    ->label('KM Saat Ini')
                    ->alignCenter()
                    ->placeholder('—')
                    ->formatStateUsing(fn($state) => $state ? number_format($state, 0, ',', '.') . ' km' : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('next_km')
                    ->label('Next KM')
                    ->alignCenter()
                    ->placeholder('—')
                    ->formatStateUsing(fn($state) => $state ? number_format($state, 0, ',', '.') . ' km' : '—')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Tanggal service berikutnya + indikator urgensi
                Tables\Columns\TextColumn::make('next_service_date')
                    ->label('Next Service')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—')
                    ->icon('heroicon-m-clock')
                    ->color(fn($state): string => match (true) {
                        $state === null => 'gray',
                        Carbon::parse($state)->isPast() => 'danger',
                        Carbon::parse($state)->diffInDays(now(), false) >= -7 => 'warning',
                        default => 'success',
                    })
                    ->description(fn($state): ?string => match (true) {
                        $state === null => null,
                        Carbon::parse($state)->isPast() => 'Sudah lewat!',
                        Carbon::parse($state)->isToday() => 'Hari ini',
                        default => Carbon::parse($state)->locale('id')->diffForHumans(),
                    }),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Filter::make('bulan_ini')
                    ->label('Hanya Bulan Ini')
                    ->default()
                    ->query(function (Builder $query): Builder {
                        return $query
                            ->whereNotNull('next_service_date')
                            ->whereMonth('next_service_date', now()->month)
                            ->whereYear('next_service_date', now()->year);
                    }),

                Tables\Filters\SelectFilter::make('jenis_service')
                    ->label('Jenis Service')
                    ->options([
                        'service' => 'Service & Tune Up',
                        'oli_mesin' => 'Oli Mesin',
                        'oli_transmisi' => 'Oli Transmisi',
                        'ganti_aki' => 'Pergantian Aki',
                        'ganti_ban' => 'Pergantian Ban',
                    ]),

                Filter::make('overdue')
                    ->label('Sudah Lewat Jadwal')
                    ->query(function (Builder $query): Builder {
                        return $query
                            ->whereNotNull('next_service_date')
                            ->whereDate('next_service_date', '<', now());
                    }),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tambah ke Jatuh Tempo
                    Tables\Actions\Action::make('addToTempo')
                        ->label('Tambah ke Jatuh Tempo')
                        ->icon('heroicon-o-calendar-days')
                        ->color('success')
                        ->visible(
                            fn(ServiceHistory $record): bool =>
                            $record->next_service_date !== null &&
                            Auth::user()->hasAnyRole(['superadmin', 'admin'])
                        )
                        ->action(function (ServiceHistory $record) {
                            $existing = \App\Models\Tempo::where('car_id', $record->car_id)
                                ->where('perawatan', 'service')
                                ->whereDate('jatuh_tempo', $record->next_service_date)
                                ->first();

                            if ($existing) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Data Sudah Ada')
                                    ->body(
                                        'Jatuh tempo service untuk ' .
                                        $record->car->carModel->name . ' (' . $record->car->nopol . ') pada ' .
                                        Carbon::parse($record->next_service_date)->format('d M Y') .
                                        ' sudah ada.'
                                    )
                                    ->warning()
                                    ->send();
                                return;
                            }

                            \App\Models\Tempo::create([
                                'car_id' => $record->car_id,
                                'perawatan' => 'service',
                                'jatuh_tempo' => $record->next_service_date,
                                'description' => 'Service berikutnya — ' . ($record->description ?: 'Service rutin'),
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Berhasil Ditambahkan!')
                                ->body('Data service berhasil ditambahkan ke Jatuh Tempo.')
                                ->success()
                                ->send();
                        }),

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
    //  RELATIONS & PAGES
    // ─────────────────────────────────────────
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceHistories::route('/'),
            'create' => Pages\CreateServiceHistory::route('/create'),
            'edit' => Pages\EditServiceHistory::route('/{record}/edit'),
        ];
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
