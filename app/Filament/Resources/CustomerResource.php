<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Blacklist;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Data';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Penyewa';
    protected static ?string $pluralLabel = 'Data Penyewa';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    // ─────────────────────────────────────────
    //  FORM  (tidak diubah)
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Data Pribadi')
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
                        ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                        ->dehydrateStateUsing(fn(string $state): string => preg_replace('/[^0-9]/', '', $state))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->prefixIcon('heroicon-o-phone'),

                    TextInput::make('ktp')
                        ->label('Nomor KTP')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->prefixIcon('heroicon-o-identification'),

                    TextInput::make('lisence')
                        ->label('Nomor SIM')
                        ->unique(ignoreRecord: true)
                        ->nullable()
                        ->prefixIcon('heroicon-o-credit-card'),

                    Textarea::make('alamat')
                        ->label('Alamat Lengkap')
                        ->rows(3)
                        ->required()
                        ->columnSpanFull()
                        ->placeholder('Jalan, kelurahan, kecamatan, kota…'),
                ]),

            Forms\Components\Section::make('Dokumen Identitas')
                ->icon('heroicon-o-document-text')
                ->description('Upload foto KTP dan SIM untuk verifikasi penyewa.')
                ->columns(2)
                ->schema([
                    FileUpload::make('identity_file')
                        ->label('Foto KTP')
                        ->disk('public')
                        ->directory('identity_docs')
                        ->image()
                        ->visibility('public')
                        ->nullable()
                        ->imagePreviewHeight('160')
                        ->panelLayout('integrated'),

                    FileUpload::make('lisence_file')
                        ->label('Foto SIM')
                        ->disk('public')
                        ->directory('license_docs')
                        ->image()
                        ->visibility('public')
                        ->nullable()
                        ->imagePreviewHeight('160')
                        ->panelLayout('integrated'),
                ]),
        ]);
    }

    // ─────────────────────────────────────────
    //  INFOLIST
    // ─────────────────────────────────────────
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            // ── Banner blacklist (muncul hanya jika NIK terblacklist) ──
            Infolists\Components\Section::make('⛔ Penyewa Ini Diblacklist')
                ->icon('heroicon-o-no-symbol')
                ->visible(fn(Customer $record) => Blacklist::isBlacklisted($record->ktp))
                ->schema([
                    Infolists\Components\TextEntry::make('ktp')
                        ->label('NIK Terblacklist')
                        ->state(function (Customer $record) {
                            $bl = Blacklist::findByNik($record->ktp);
                            if (!$bl)
                                return '—';
                            return "NIK: {$record->ktp}" .
                                "\nAlasan: " . ucfirst(str_replace('_', ' ', $bl->alasan)) .
                                ($bl->catatan ? "\nCatatan: {$bl->catatan}" : '') .
                                "\nDiblacklist oleh: " . ($bl->blacklisted_by ?? '—') .
                                " pada " . $bl->blacklisted_at?->format('d M Y');
                        })
                        ->columnSpanFull()
                        ->color('danger')
                        ->weight(\Filament\Support\Enums\FontWeight::SemiBold),
                ])
                ->extraAttributes(['style' => 'border: 2px solid #ef4444; background: #fff1f2;']),

            Infolists\Components\Section::make('Informasi Pribadi')
                ->icon('heroicon-o-user')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('nama')
                        ->label('Nama Lengkap')
                        ->weight(\Filament\Support\Enums\FontWeight::Bold)
                        ->icon('heroicon-m-user')
                        ->columnSpanFull(),

                    Infolists\Components\TextEntry::make('no_telp')
                        ->label('No. HP / WhatsApp')
                        ->icon('heroicon-m-phone')
                        ->copyable()
                        ->copyMessage('Nomor disalin!'),

                    Infolists\Components\TextEntry::make('ktp')
                        ->label('Nomor KTP')
                        ->icon('heroicon-m-identification')
                        ->copyable()
                        ->copyMessage('NIK disalin!')
                        ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                    Infolists\Components\TextEntry::make('lisence')
                        ->label('Nomor SIM')
                        ->icon('heroicon-m-credit-card')
                        ->placeholder('—'),

                    Infolists\Components\TextEntry::make('alamat')
                        ->label('Alamat')
                        ->icon('heroicon-m-map-pin')
                        ->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Dokumen Identitas')
                ->icon('heroicon-o-document-text')
                ->columns(2)
                ->schema([
                    Infolists\Components\ImageEntry::make('identity_file')
                        ->label('Scan KTP')
                        ->disk('public')
                        ->height(180)
                        ->extraImgAttributes(['class' => 'rounded-lg object-cover']),

                    Infolists\Components\ImageEntry::make('lisence_file')
                        ->label('Scan SIM')
                        ->disk('public')
                        ->height(180)
                        ->extraImgAttributes(['class' => 'rounded-lg object-cover']),
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

                TextColumn::make('nama')
                    ->label('Penyewa')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->searchable()
                    ->description(fn(Customer $record): string => $record->no_telp ?? '—')
                    ->wrap()
                    ->width(150),

                TextColumn::make('ktp')
                    ->label('No. KTP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('NIK disalin!')
                    ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                    ->color('gray'),

                // ── Status Blacklist ──
                Tables\Columns\IconColumn::make('is_blacklisted')
                    ->label('Blacklist')
                    ->alignCenter()
                    ->state(fn(Customer $record) => Blacklist::isBlacklisted($record->ktp))
                    ->boolean()
                    ->trueIcon('heroicon-m-no-symbol')
                    ->falseIcon('heroicon-m-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->tooltip(
                        fn(Customer $record) => Blacklist::isBlacklisted($record->ktp)
                        ? '⛔ NIK ini diblacklist'
                        : '✓ NIK aman'
                    ),

                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(30)
                    ->tooltip(fn($state) => $state)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('identity_file')
                    ->label('KTP')
                    ->alignCenter()
                    ->boolean()
                    ->trueIcon('heroicon-m-check-badge')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn($state) => $state ? 'KTP tersedia' : 'KTP belum diupload'),

                Tables\Columns\IconColumn::make('lisence_file')
                    ->label('SIM')
                    ->alignCenter()
                    ->boolean()
                    ->trueIcon('heroicon-m-check-badge')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn($state) => $state ? 'SIM tersedia' : 'SIM belum diupload'),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\Filter::make('is_blacklisted')
                    ->label('Hanya Yang Diblacklist')
                    ->toggle()
                    ->query(fn(Builder $q) => $q->whereIn(
                        'ktp',
                        Blacklist::pluck('nik')
                    ))
                    ->indicateUsing(
                        fn(array $data): ?string =>
                        ($data['isActive'] ?? false) ? '⛔ Hanya NIK blacklisted' : null
                    ),

                Tables\Filters\Filter::make('has_ktp')
                    ->label('KTP Lengkap')
                    ->toggle()
                    ->query(fn(Builder $q) => $q->whereNotNull('identity_file'))
                    ->indicateUsing(
                        fn(array $data): ?string =>
                        ($data['isActive'] ?? false) ? '✓ KTP tersedia' : null
                    ),

                Tables\Filters\Filter::make('has_sim')
                    ->label('SIM Lengkap')
                    ->toggle()
                    ->query(fn(Builder $q) => $q->whereNotNull('lisence_file'))
                    ->indicateUsing(
                        fn(array $data): ?string =>
                        ($data['isActive'] ?? false) ? '✓ SIM tersedia' : null
                    ),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Detail')
                        ->icon('heroicon-o-eye')
                        ->color('info'),

                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning'),

                    // ── TOMBOL BLACKLIST ──
                    Tables\Actions\Action::make('blacklist')
                        ->label('Blacklist NIK Ini')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->visible(
                            fn(Customer $record) =>
                            !Blacklist::isBlacklisted($record->ktp) &&
                            Auth::user()->hasAnyRole(['superadmin', 'admin'])
                        )
                        ->form([
                            Forms\Components\Placeholder::make('nik_info')
                                ->label('NIK yang akan diblacklist')
                                ->content(fn(Customer $record) => $record->ktp . ' — ' . $record->nama),

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
                                ->label('Catatan Detail')
                                ->placeholder('Ceritakan kejadian, nominal kerugian, dll...')
                                ->rows(3),
                        ])
                        ->action(function (Customer $record, array $data) {
                            Blacklist::create([
                                'nik' => $record->ktp,
                                'nama' => $record->nama,
                                'alasan' => $data['alasan'],
                                'catatan' => $data['catatan'] ?? null,
                                'blacklisted_by' => Auth::user()->name,
                                'blacklisted_at' => now(),
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('NIK Diblacklist')
                                ->body("NIK {$record->ktp} ({$record->nama}) berhasil dimasukkan ke daftar blacklist.")
                                ->danger()
                                ->send();
                        })
                        ->requiresConfirmation(false), // Form sudah cukup sebagai konfirmasi

                    // ── TOMBOL UNBLACKLIST ──
                    Tables\Actions\Action::make('unblacklist')
                        ->label('Hapus dari Blacklist')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(
                            fn(Customer $record) =>
                            Blacklist::isBlacklisted($record->ktp) &&
                            Auth::user()->hasAnyRole(['superadmin', 'admin'])
                        )
                        ->requiresConfirmation()
                        ->modalHeading('Hapus dari Blacklist?')
                        ->modalDescription(
                            fn(Customer $record) =>
                            "NIK {$record->ktp} ({$record->nama}) akan dihapus dari blacklist dan dapat menyewa kembali."
                        )
                        ->modalSubmitActionLabel('Ya, Hapus dari Blacklist')
                        ->action(function (Customer $record) {
                            Blacklist::where('nik', $record->ktp)->delete();

                            \Filament\Notifications\Notification::make()
                                ->title('Dihapus dari Blacklist')
                                ->body("{$record->nama} berhasil dihapus dari daftar blacklist.")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('downloadKtp')
                        ->label('Download KTP')
                        ->icon('heroicon-o-identification')
                        ->color('success')
                        ->url(fn($record) => route('customers.download.ktp', $record), true)
                        ->hidden(fn($record) => !$record->identity_file),

                    Tables\Actions\Action::make('downloadSim')
                        ->label('Download SIM')
                        ->icon('heroicon-o-credit-card')
                        ->color('primary')
                        ->url(fn($record) => route('customers.download.sim', $record), true)
                        ->hidden(fn($record) => !$record->lisence_file),

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

            // Baris blacklisted diberi tint merah
            ->recordClasses(
                fn(Customer $record): string =>
                Blacklist::isBlacklisted($record->ktp)
                ? 'bg-red-50 dark:bg-red-950/20 opacity-80'
                : ''
            );
    }

    public static function getRelations(): array
    {
        return [RelationManagers\BookingsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
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
