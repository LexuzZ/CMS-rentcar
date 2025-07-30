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
                    ->required(),

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
                ImageColumn::make('lisence_file')->label('Foto')->width(80)
                    ->height(60),
                ImageColumn::make('identity_file')->label('Foto')->width(80)
                    ->height(60),
                TextColumn::make('nama')->label('Nama')->searchable()->sortable(),
                TextColumn::make('ktp')->label('No KTP / SIM'),
                TextColumn::make('lisence')->label('No KTP / SIM'),
                TextColumn::make('no_telp')->label('HP'),
                TextColumn::make('alamat')->label('Alamat')->limit(20),
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
}
