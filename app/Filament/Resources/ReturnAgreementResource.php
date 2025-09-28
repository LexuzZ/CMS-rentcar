<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturnAgreementResource\Pages;
use App\Filament\Resources\ReturnAgreementResource\RelationManagers;
use App\Models\Booking;
use App\Models\ReturnAgreement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReturnAgreementResource extends Resource
{
    protected static ?string $model = Booking::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
     protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Form Kembali';
    protected static ?string $navigationGroup = 'CheckList Garasi';
    protected static ?string $label = 'Form Kembali';
    protected static ?string $pluralLabel = 'Form Kembali';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Booking')
                ->schema([
                    Forms\Components\Placeholder::make('id')
                        ->label('Booking ID')
                        ->content(fn(?Booking $record): string => $record?->id ?? '-'),

                    Forms\Components\Placeholder::make('customer.nama')
                        ->label('Nama Customer')
                        ->content(fn(?Booking $record): string => $record?->customer?->nama ?? '-'),

                    Forms\Components\Placeholder::make('car.carModel.name')
                        ->label('Nama Mobil')
                        ->content(fn(?Booking $record): string => $record?->car?->carModel->name ?? '-'),

                    Forms\Components\Placeholder::make('car.nopol')
                        ->label('No. Polisi')
                        ->content(fn(?Booking $record): string => $record?->car?->nopol ?? '-'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Data Pengembalian')
                ->schema([
                    Forms\Components\DatePicker::make('tanggal_kembali_real')
                        ->label('Tanggal Kembali Real')
                        ->required(),

                    Forms\Components\TimePicker::make('waktu_kembali_real')
                        ->label('Waktu Kembali Real')
                        ->required(),

                    Forms\Components\View::make('filament.forms.camera-capture')
                        ->statePath('foto_kembali')
                        ->label('Foto Kondisi Mobil Saat Kembali'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Booking ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')->label('Penyewa')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('car.carModel.name')->label('Mobil')->sortable(),
                Tables\Columns\TextColumn::make('car.nopol')->label('Nopol'),
                Tables\Columns\TextColumn::make('tanggal_keluar')->date('d M Y')->label('Tanggal Keluar'),
                Tables\Columns\TextColumn::make('tanggal_kembali')->date('d M Y')->label('Tanggal Kembali Estimasi'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('Form Kembali')->icon('heroicon-o-pencil')->color('success'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('ttd');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReturnAgreements::route('/'),
            'create' => Pages\CreateReturnAgreement::route('/create'),
            'edit' => Pages\EditReturnAgreement::route('/{record}/edit'),
        ];
    }
}
