<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlacklistResource\Pages;
use App\Models\Blacklist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BlacklistResource extends Resource
{
    protected static ?string $model = Blacklist::class;

    protected static ?string $navigationIcon = 'heroicon-o-no-symbol';
    protected static ?string $navigationGroup = 'Data';
    protected static ?int $navigationSort = 5;
    protected static ?string $label = 'Blacklist NIK';
    protected static ?string $pluralLabel = 'Daftar Blacklist NIK';

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Total NIK yang diblacklist';
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Data NIK')
                ->icon('heroicon-o-identification')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nik')
                        ->label('Nomor NIK / KTP')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(16)
                        ->minLength(16)
                        ->numeric()
                        ->prefixIcon('heroicon-o-identification')
                        ->validationMessages([
                            'unique' => 'NIK ini sudah ada di daftar blacklist.',
                            'min' => 'NIK harus 16 digit.',
                            'max' => 'NIK harus 16 digit.',
                        ])
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Penyewa')
                        ->placeholder('Nama sesuai KTP (opsional)')
                        ->prefixIcon('heroicon-o-user')
                        ->nullable(),

                    Forms\Components\TextInput::make('blacklisted_by')
                        ->label('Diblacklist Oleh')
                        ->default(fn() => Auth::user()?->name)
                        ->prefixIcon('heroicon-o-shield-check')
                        ->disabled()
                        ->dehydrated(),
                ]),

            Forms\Components\Section::make('Alasan & Catatan')
                ->icon('heroicon-o-exclamation-triangle')
                ->columns(1)
                ->schema([
                    Forms\Components\Select::make('alasan')
                        ->label('Alasan Blacklist')
                        ->options([
                            'tidak_bayar' => 'Tidak Membayar',
                            'merusak_mobil' => 'Merusak Kendaraan',
                            'kabur' => 'Melarikan Diri / Tidak Kembali',
                            'penipuan' => 'Penipuan / Identitas Palsu',
                            'bermasalah' => 'Penyewa Bermasalah',
                            'lainnya' => 'Lainnya',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Tambahan')
                        ->placeholder('Detail kejadian, nominal kerugian, dsb...')
                        ->rows(3)
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

                // NIK
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('NIK disalin!')
                    ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('danger'),

                // Nama
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->placeholder('—')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                // Alasan — badge
                Tables\Columns\TextColumn::make('alasan')
                    ->label('Alasan')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn(string $state): string => match ($state) {
                        'tidak_bayar' => 'heroicon-m-banknotes',
                        'merusak_mobil' => 'heroicon-m-wrench-screwdriver',
                        'kabur' => 'heroicon-m-arrow-right-on-rectangle',
                        'penipuan' => 'heroicon-m-exclamation-triangle',
                        'bermasalah' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-information-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'tidak_bayar' => 'warning',
                        'merusak_mobil' => 'danger',
                        'kabur' => 'danger',
                        'penipuan' => 'danger',
                        'bermasalah' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'tidak_bayar' => 'Tidak Bayar',
                        'merusak_mobil' => 'Merusak Kendaraan',
                        'kabur' => 'Melarikan Diri',
                        'penipuan' => 'Penipuan',
                        'bermasalah' => 'Penyewa Bermasalah',
                        'lainnya' => 'Lainnya',
                        default => ucfirst($state),
                    }),

                // Catatan (truncated)
                Tables\Columns\TextColumn::make('catatan')
                    ->label('Catatan')
                    ->limit(45)
                    ->tooltip(fn($state) => $state)
                    ->placeholder('—')
                    ->toggleable(),

                // Diblacklist oleh
                Tables\Columns\TextColumn::make('blacklisted_by')
                    ->label('Oleh')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—')
                    ->toggleable(),

                // Tanggal
                Tables\Columns\TextColumn::make('blacklisted_at')
                    ->label('Tgl. Blacklist')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->color('gray'),
            ])

            ->defaultSort('blacklisted_at', 'desc')

            ->filters([
                Tables\Filters\SelectFilter::make('alasan')
                    ->label('Alasan')
                    ->options([
                        'tidak_bayar' => 'Tidak Membayar',
                        'merusak_mobil' => 'Merusak Kendaraan',
                        'kabur' => 'Melarikan Diri',
                        'penipuan' => 'Penipuan',
                        'bermasalah' => 'Penyewa Bermasalah',
                        'lainnya' => 'Lainnya',
                    ]),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning'),

                    Tables\Actions\Action::make('unblacklist')
                        ->label('Hapus dari Blacklist')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus dari Blacklist?')
                        ->modalDescription(
                            fn(Blacklist $record) =>
                            "NIK {$record->nik}" .
                            ($record->nama ? " ({$record->nama})" : '') .
                            " akan dihapus dari blacklist dan dapat menyewa kembali."
                        )
                        ->modalSubmitActionLabel('Ya, Hapus dari Blacklist')
                        ->action(fn(Blacklist $record) => $record->delete())
                        ->successNotificationTitle('NIK berhasil dihapus dari blacklist'),

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

            // Semua baris dapat tint merah tipis
            ->recordClasses(fn() => 'bg-red-50/40 dark:bg-red-950/10');
    }

    // ─────────────────────────────────────────
    //  PAGES & ACCESS
    // ─────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlacklists::route('/'),
            'create' => Pages\CreateBlacklist::route('/create'),
            'edit' => Pages\EditBlacklist::route('/{record}/edit'),
        ];
    }

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
