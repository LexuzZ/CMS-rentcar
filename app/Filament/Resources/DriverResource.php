<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Filament\Resources\DriverResource\RelationManagers;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Data';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $label           = 'Staff';
    protected static ?string $pluralLabel     = 'Data Staff';

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        $tersedia = static::getModel()::where('status', 'tersedia')->count();
        return $tersedia > 0 ? (string) $tersedia : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Staff yang tersedia';
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Informasi Staff')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    TextInput::make('nama')
                        ->label('Nama Lengkap')
                        ->required()
                        ->prefixIcon('heroicon-o-user')
                        ->columnSpanFull(),

                    TextInput::make('no_telp')
                        ->label('No HP / WhatsApp')
                        ->tel()
                        ->required()
                        ->prefixIcon('heroicon-o-phone'),

                    TextInput::make('harga')
                        ->label('Harga Jasa')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->prefixIcon('heroicon-o-banknotes'),

                    Select::make('status')
                        ->label('Status Ketersediaan')
                        ->options([
                            'tersedia'   => 'Tersedia',
                            'mengemudi'  => 'Sedang Mengemudi',
                        ])
                        ->default('tersedia')
                        ->required()
                        ->native(false)
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

                // Avatar inisial + nama + no HP
                TextColumn::make('nama')
                    ->label('Staff')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->searchable()
                    ->sortable()
                    ->description(fn (Driver $record): string => $record->no_telp ?? '—'),

                // Status badge + ikon
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn (string $state): string => match ($state) {
                        'tersedia'  => 'heroicon-m-check-circle',
                        'mengemudi' => 'heroicon-m-truck',
                        default     => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'tersedia'  => 'success',
                        'mengemudi' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'tersedia'  => 'Tersedia',
                        'mengemudi' => 'Sedang Bertugas',
                        default     => ucfirst($state),
                    }),

                // Harga jasa
                TextColumn::make('harga')
                    ->label('Harga Jasa')
                    ->alignEnd()
                    ->sortable()
                    ->color('success')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->money('IDR'),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'tersedia'  => 'Tersedia',
                        'mengemudi' => 'Sedang Bertugas',
                    ]),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning'),

                    // Toggle status langsung dari tabel
                    Tables\Actions\Action::make('toggleStatus')
                        ->label(fn (Driver $record) => $record->status === 'tersedia'
                            ? 'Set Sedang Bertugas'
                            : 'Set Tersedia'
                        )
                        ->icon(fn (Driver $record) => $record->status === 'tersedia'
                            ? 'heroicon-o-truck'
                            : 'heroicon-o-check-circle'
                        )
                        ->color(fn (Driver $record) => $record->status === 'tersedia'
                            ? 'warning'
                            : 'success'
                        )
                        ->action(function (Driver $record) {
                            $record->update([
                                'status' => $record->status === 'tersedia' ? 'mengemudi' : 'tersedia',
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Status diperbarui')
                                ->body("Status {$record->nama} berhasil diubah.")
                                ->success()
                                ->send();
                        })
                        ->visible(fn () => Auth::user()->hasAnyRole(['superadmin', 'admin'])),

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

            // Highlight staff yang sedang bertugas
            ->recordClasses(fn (Driver $record): string => match ($record->status) {
                'mengemudi' => 'opacity-70',
                default     => '',
            });
    }

    // ─────────────────────────────────────────
    //  PAGES & ACCESS
    // ─────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit'   => Pages\EditDriver::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool { return true; }

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
