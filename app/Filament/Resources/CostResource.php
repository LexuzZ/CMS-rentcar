<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CostResource\Pages;
use App\Filament\Resources\CostResource\RelationManagers;
use App\Models\Cost;
use App\Models\Pengeluaran;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CostResource extends Resource
{
    protected static ?string $model = Pengeluaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Laporan & Accounting';
    protected static ?string $label = 'Kas Keluar';
    protected static ?string $pluralLabel = 'Kas Keluar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Select::make('nama_pengeluaran')
                    ->label('Jenis Pengeluaran')
                    ->options([
                        'gaji' => 'Gaji Karyawan',
                        'pajak' => 'Pajak/STNK',
                        'perawatan' => 'Perawatan',
                        'operasional' => 'Operasional Kantor',
                    ])
                    ->required(),
                DatePicker::make('tanggal_pengeluaran')
                    ->label('Tanggal Pengeluaran')
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi Pengeluaran')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('pembayaran')
                    ->label('Nominal Pembayaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_pengeluaran')
                    ->label('Jenis Pengeluaran')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'gaji',
                        'warning' => 'pajak',
                        'danger' => 'perawatan',
                        'primary' => 'operasional',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'gaji' => 'Gaji Karyawan',
                        'pajak' => 'Pajak/STNK',
                        'perawatan' => 'Perawatan',
                        'operasional' => 'Operasional Kantor',
                        default => ucfirst($state),
                    }),
                TextColumn::make('description')->label('Deskripsi Pengeluaran'),
                TextColumn::make('tanggal_pengeluaran')->label('Tanggal Pengeluaran')->date('d M Y'),
                TextColumn::make('pembayaran')->label('Pembayaran')->money('IDR'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCosts::route('/'),
            'create' => Pages\CreateCost::route('/create'),
            'edit' => Pages\EditCost::route('/{record}/edit'),
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
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa mengedit
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDelete(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa menghapus
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDeleteAny(): bool
    {
        // Hanya superadmin dan admin yang bisa hapus massal
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

}
