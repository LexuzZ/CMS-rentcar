<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
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

    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Data';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $label           = 'Penyewa';
    protected static ?string $pluralLabel     = 'Data Penyewa';

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    // ─────────────────────────────────────────
    //  FORM
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
                        ->dehydrateStateUsing(fn (string $state): string => preg_replace('/[^0-9]/', '', $state))
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

                // Nama + no.HP
                TextColumn::make('nama')
                    ->label('Penyewa')
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->searchable()
                    ->description(fn (Customer $record): string => $record->no_telp ?? '—'),

                // KTP
                TextColumn::make('ktp')
                    ->label('No. KTP')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('NIK disalin!')
                    ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                    ->color('gray'),

                // Alamat
                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(),

                // Dokumen — ikon status
                Tables\Columns\IconColumn::make('identity_file')
                    ->label('KTP')
                    ->alignCenter()
                    ->boolean()
                    ->trueIcon('heroicon-m-check-badge')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn ($state) => $state ? 'KTP tersedia' : 'KTP belum diupload'),

                Tables\Columns\IconColumn::make('lisence_file')
                    ->label('SIM')
                    ->alignCenter()
                    ->boolean()
                    ->trueIcon('heroicon-m-check-badge')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn ($state) => $state ? 'SIM tersedia' : 'SIM belum diupload'),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\Filter::make('has_ktp')
                    ->label('KTP Lengkap')
                    ->toggle()
                    ->query(fn (Builder $q) => $q->whereNotNull('identity_file'))
                    ->indicateUsing(fn (array $data): ?string =>
                        $data['isActive'] ? '✓ KTP tersedia' : null
                    ),

                Tables\Filters\Filter::make('has_sim')
                    ->label('SIM Lengkap')
                    ->toggle()
                    ->query(fn (Builder $q) => $q->whereNotNull('lisence_file'))
                    ->indicateUsing(fn (array $data): ?string =>
                        $data['isActive'] ? '✓ SIM tersedia' : null
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

                    Tables\Actions\Action::make('downloadKtp')
                        ->label('Download KTP')
                        ->icon('heroicon-o-identification')
                        ->color('success')
                        ->url(fn ($record) => route('customers.download.ktp', $record), true)
                        ->openUrlInNewTab(false)
                        ->hidden(fn ($record) => !$record->identity_file),

                    Tables\Actions\Action::make('downloadSim')
                        ->label('Download SIM')
                        ->icon('heroicon-o-credit-card')
                        ->color('primary')
                        ->url(fn ($record) => route('customers.download.sim', $record), true)
                        ->openUrlInNewTab(false)
                        ->hidden(fn ($record) => !$record->lisence_file),

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
        return [
            RelationManagers\BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view'   => Pages\ViewCustomer::route('/{record}'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    // ─────────────────────────────────────────
    //  ACCESS
    // ─────────────────────────────────────────
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
