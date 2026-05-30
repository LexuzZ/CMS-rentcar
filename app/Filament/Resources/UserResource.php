<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon   = 'heroicon-o-user-group';
    protected static ?string $navigationGroup  = 'Data';
    protected static ?string $navigationLabel  = 'Users';
    protected static ?string $pluralModelLabel = 'Users';

    // ─────────────────────────────────────────
    //  NAVIGATION BADGE
    // ─────────────────────────────────────────
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    // ─────────────────────────────────────────
    //  FORM
    // ─────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Identitas Pengguna')
                ->icon('heroicon-o-user-circle')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-user'),

                    Forms\Components\TextInput::make('email')
                        ->label('Alamat Email')
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->prefixIcon('heroicon-o-envelope'),

                    Select::make('role')
                        ->label('Role / Hak Akses')
                        ->options([
                            'superadmin' => 'Superadmin',
                            'admin'      => 'Admin',
                            'supervisor' => 'Supervisor',
                            'staff'      => 'Staff',
                        ])
                        ->default('staff')
                        ->required()
                        ->native(false)
                        ->prefixIcon('heroicon-o-shield-check'),

                    Forms\Components\TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->maxLength(255)
                        ->placeholder(fn (string $context) => $context === 'edit' ? 'Kosongkan jika tidak diubah' : null)
                        ->helperText(fn (string $context) => $context === 'edit' ? 'Isi hanya jika ingin mengganti password.' : null)
                        ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->prefixIcon('heroicon-o-lock-closed'),
                ]),
        ]);
    }

    // ─────────────────────────────────────────
    //  TABLE
    // ─────────────────────────────────────────
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                // Avatar inisial + nama + email
                Tables\Columns\TextColumn::make('name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold)
                    ->description(fn (User $record): string => $record->email),

                // Role badge berwarna
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->alignCenter()
                    ->icon(fn (string $state): string => match ($state) {
                        'superadmin' => 'heroicon-m-star',
                        'admin'      => 'heroicon-m-shield-check',
                        'supervisor' => 'heroicon-m-eye',
                        'staff'      => 'heroicon-m-user',
                        default      => 'heroicon-m-user-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'superadmin' => 'danger',
                        'admin'      => 'warning',
                        'supervisor' => 'info',
                        'staff'      => 'success',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                // Tanggal bergabung
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->since()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-m-calendar')
                    ->color('gray'),

                // Status email verified
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Terverifikasi')
                    ->boolean()
                    ->alignCenter()
                    ->trueIcon('heroicon-m-check-badge')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'superadmin' => 'Superadmin',
                        'admin'      => 'Admin',
                        'supervisor' => 'Supervisor',
                        'staff'      => 'Staff',
                    ]),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning'),

                    Tables\Actions\Action::make('resetPassword')
                        ->label('Reset Password')
                        ->icon('heroicon-o-lock-open')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Reset Password?')
                        ->modalDescription('Password akan direset menjadi "password123". Pengguna wajib mengganti setelah login.')
                        ->modalSubmitActionLabel('Ya, Reset')
                        ->action(function (User $record) {
                            $record->update(['password' => Hash::make('password123')]);
                            \Filament\Notifications\Notification::make()
                                ->title('Password Direset')
                                ->body("Password {$record->name} berhasil direset menjadi \"password123\".")
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
            ->paginated([10, 25, 50]);
    }

    // ─────────────────────────────────────────
    //  PAGES
    // ─────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index'  => UserResource\Pages\ListUsers::route('/'),
            'create' => UserResource\Pages\CreateUser::route('/create'),
            'edit'   => UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // ─────────────────────────────────────────
    //  ACCESS
    // ─────────────────────────────────────────
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
