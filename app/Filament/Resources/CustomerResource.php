<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Data ';
    protected static ?string $label = 'Pelanggan';
    protected static ?string $pluralLabel = 'Data Pelanggan';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('nama')
                    ->label('Nama')
                    ->required(),
                TextInput::make('no_telp')
                    ->label('No HP / WhatsApp')
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')

                    // 3. Tetap membersihkan nomor sebelum disimpan ke database, ini sangat penting
                    ->dehydrateStateUsing(fn(string $state): string => preg_replace('/[^0-9]/', '', $state))
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('ktp')
                    ->label('No KTP')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('lisence')
                    ->label('no SIM')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->nullable(),
                FileUpload::make('identity_file')
                    ->label('Upload KTP')
                    ->disk('public')
                    ->directory('identity_docs')
                    ->image()
                    ->visibility('public')
                    ->nullable(),
                FileUpload::make('lisence_file')
                    ->label('Upload SIM')
                    ->disk('public')
                    ->directory('license_docs')
                    ->image()
                    ->visibility('public')
                    ->nullable(),



                Textarea::make('alamat')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull()
                    ->required(),
            ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([

                TextColumn::make('nama')->label('Nama')->searchable()->wrap() // <-- Tambahkan wrap agar teks turun
                    ->width(150),
                TextColumn::make('ktp')->label('No KTP / SIM'),
                TextColumn::make('lisence')->label('No KTP / SIM'),
                TextColumn::make('no_telp')->label('HP'),
                TextColumn::make('alamat')->label('Alamat')->limit(20)->wrap() // <-- Tambahkan wrap agar teks turun
                    ->width(150),
            ])
            ->defaultSort('nama')
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
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
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa mengedit
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDelete(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa menghapus
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDeleteAny(): bool
    {
        // Hanya superadmin dan admin yang bisa hapus massal
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }
}
