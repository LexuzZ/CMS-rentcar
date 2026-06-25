<?php

namespace App\Filament\Resources;

use App\Models\Car;
use App\Models\CarInstallment;
use App\Models\Pengeluaran;
use App\Filament\Resources\CarInstallmentResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CarInstallmentResource extends Resource
{
    protected static ?string $model = CarInstallment::class;

    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Manajemen Mobil';
    protected static ?string $label           = 'Cicilan Mobil';
    protected static ?string $pluralLabel     = 'Cicilan Mobil';

    // ─────────────────────────────────────────
    //  BASE QUERY
    // ─────────────────────────────────────────
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('car', fn (Builder $q) => $q->where('garasi', 'SPT'));
    }

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'berjalan')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $hasMacet = static::getModel()::where('status', 'macet')->exists();
        return $hasMacet ? 'danger' : 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Cicilan yang sedang berjalan';
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Kendaraan & Leasing')
                ->icon('heroicon-o-truck')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('car_id')
                        ->label('Mobil')
                        ->relationship(
                            name: 'car',
                            titleAttribute: 'nopol',
                            modifyQueryUsing: fn (Builder $query) => $query
                                ->where('garasi', 'SPT')
                                ->with('carModel')
                        )
                        ->getOptionLabelFromRecordUsing(
                            fn (Car $record) => "{$record->carModel->name} ({$record->nopol})"
                        )
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search) {
                            return Car::query()
                                ->where('garasi', 'SPT')
                                ->where(fn ($q) => $q
                                    ->where('nopol', 'like', "%{$search}%")
                                    ->orWhereHas('carModel', fn ($q2) => $q2->where('name', 'like', "%{$search}%"))
                                )
                                ->with('carModel')
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn ($car) => [
                                    $car->id => "{$car->carModel->name} ({$car->nopol})"
                                ]);
                        })
                        ->preload()
                        ->required(),

                    TextInput::make('nama_leasing')
                        ->label('Nama Leasing')
                        ->prefixIcon('heroicon-o-building-library')
                        ->placeholder('Contoh: FIF, BAF, ACC…'),

                    Select::make('status')
                        ->label('Status Cicilan')
                        ->options([
                            'berjalan' => 'Berjalan',
                            'lunas'    => 'Lunas',
                            'macet'    => 'Macet',
                        ])
                        ->required()
                        ->native(false),
                ]),

            Forms\Components\Section::make('Jadwal & Nominal')
                ->icon('heroicon-o-calendar-days')
                ->columns(2)
                ->schema([
                    DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->native(false)
                        ->required(),

                    DatePicker::make('jatuh_tempo')
                        ->label('Jatuh Tempo')
                        ->native(false)
                        ->required(),

                    TextInput::make('total_hutang')
                        ->label('Total Hutang')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    TextInput::make('nominal_cicilan')
                        ->label('Nominal Cicilan / Bulan')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    TextInput::make('tenor')
                        ->label('Tenor')
                        ->numeric()
                        ->suffix('Bulan')
                        ->required(),

                    TextInput::make('cicilan_ke')
                        ->label('Cicilan Ke-')
                        ->numeric()
                        ->required()
                        ->suffix('(bulan berjalan)'),

                    Textarea::make('catatan')
                        ->label('Catatan')
                        ->rows(3)
                        ->placeholder('Nomor kontrak, informasi tambahan…')
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

                // Mobil + nopol
                Tables\Columns\TextColumn::make('car.carModel.name')
                    ->label('Kendaraan')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->description(fn (CarInstallment $record): string => $record->car->nopol ?? '—')
                    ->searchable(query: fn (Builder $query, string $search) => $query
                        ->whereHas('car.carModel', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('car', fn ($q) => $q->where('nopol', 'like', "%{$search}%"))
                    ),

                // Leasing
                Tables\Columns\TextColumn::make('nama_leasing')
                    ->label('Leasing')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                // Progress cicilan
                Tables\Columns\TextColumn::make('cicilan_ke')
                    ->label('Progress')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state, CarInstallment $record): string =>
                        "{$state} / {$record->tenor}"
                    )
                    ->description(fn (CarInstallment $record): string => match(true) {
                        $record->tenor > 0 => round($record->cicilan_ke / $record->tenor * 100) . '% selesai',
                        default => '—',
                    }),

                // Nominal cicilan
                Tables\Columns\TextColumn::make('nominal_cicilan')
                    ->label('Cicilan / Bln')
                    ->alignEnd()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                // Sisa hutang
                Tables\Columns\TextColumn::make('sisa_hutang')
                    ->label('Sisa Hutang')
                    ->alignEnd()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state > 0
                        ? 'Rp ' . number_format($state, 0, ',', '.')
                        : 'Lunas'
                    ),

                // Jatuh tempo
                Tables\Columns\TextColumn::make('jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-clock')
                    ->color(fn ($state) => match(true) {
                        \Carbon\Carbon::parse($state)->isPast()                       => 'danger',
                        \Carbon\Carbon::parse($state)->diffInDays(now(), false) >= -30 => 'warning',
                        default                                                        => 'gray',
                    })
                    ->toggleable(),

                // Status
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn (string $state): string => match ($state) {
                        'berjalan' => 'heroicon-m-arrow-path',
                        'lunas'    => 'heroicon-m-check-circle',
                        'macet'    => 'heroicon-m-exclamation-triangle',
                        default    => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'berjalan' => 'warning',
                        'lunas'    => 'success',
                        'macet'    => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'berjalan' => 'Berjalan',
                        'lunas'    => 'Lunas',
                        'macet'    => 'Macet',
                        default    => ucfirst($state),
                    }),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'berjalan' => 'Berjalan',
                        'lunas'    => 'Lunas',
                        'macet'    => 'Macet',
                    ]),

                Tables\Filters\Filter::make('macet')
                    ->label('Hanya Macet')
                    ->toggle()
                    ->query(fn (Builder $q) => $q->where('status', 'macet'))
                    ->indicateUsing(fn (array $data): ?string =>
                        ($data['isActive'] ?? false) ? '⚠ Hanya cicilan macet' : null
                    ),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\Action::make('tambah_pengeluaran')
                        ->label('Tambah ke Pengeluaran')
                        ->icon('heroicon-o-banknotes')
                        ->color('danger')
                        ->visible(fn ($record) => !$record->pengeluaran &&
                            Auth::user()->hasAnyRole(['superadmin', 'admin'])
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Tambah ke Pengeluaran?')
                        ->modalDescription('Nominal cicilan ini akan dicatat sebagai pengeluaran.')
                        ->modalSubmitActionLabel('Ya, Tambahkan')
                        ->action(function ($record) {
                            Pengeluaran::create([
                                'car_installment_id'  => $record->id,
                                'tanggal_pengeluaran' => now(),
                                'nama_pengeluaran'    => 'cicilan',
                                'description'         => 'Pembayaran cicilan mobil ' . $record->car?->nopol,
                                'pembayaran'          => $record->nominal_cicilan,
                                'status'              => 'paid',
                            ]);

                            Notification::make()
                                ->title('Berhasil!')
                                ->body('Cicilan berhasil ditambahkan ke pengeluaran.')
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
            ->paginated([10, 25, 50])

            // Row highlight untuk macet
            ->recordClasses(fn (CarInstallment $record): string => match ($record->status) {
                'macet'  => 'bg-red-50 dark:bg-red-950/20',
                'lunas'  => 'opacity-60',
                default  => '',
            });
    }

    // ─────────────────────────────────────────
    //  PAGES & ACCESS
    // ─────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCarInstallments::route('/'),
            'create' => Pages\CreateCarInstallment::route('/create'),
            'edit'   => Pages\EditCarInstallment::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool  { return true; }

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
