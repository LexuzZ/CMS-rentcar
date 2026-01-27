<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Riwayat Pembayaran';
    public static function canCreateForRecord(Model $ownerRecord): bool
    {
        return true;
    }

    /* =======================
     | FORM
     ======================= */
    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('tanggal_pembayaran')
                ->label('Tanggal Pembayaran')
                ->default(now())
                ->required(),

            TextInput::make('pembayaran')
                ->label('Jumlah Pembayaran')
                ->prefix('Rp')
                ->numeric()
                ->required()
                ->rules([
                    fn() => function ($attribute, $value, $fail) {
                        $invoice = $this->getOwnerRecord();

                        if ($value > $invoice->sisa_pembayaran) {
                            $fail('Jumlah pembayaran melebihi sisa tagihan.');
                        }
                    },
                ]),

            Select::make('metode_pembayaran')
                ->label('Metode Pembayaran')
                ->options([
                    'tunai' => 'Tunai',
                    'transfer' => 'Transfer',
                    'qris' => 'QRIS',
                    'tunai_transfer' => 'Tunai & Transfer',
                    'tunai_qris' => 'Tunai & QRIS',
                    'transfer_qris' => 'Transfer & QRIS',
                ])
                ->required(),
        ]);
    }

    /* =======================
     | TABLE
     ======================= */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal_pembayaran')
                    ->label('Tanggal')
                    ->date('d M Y'),

                TextColumn::make('pembayaran')
                    ->label('Jumlah')
                    ->money('IDR', true),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->wrap()
                    ->width(150)
                    ->alignCenter()
                    ->colors([
                        'success' => 'tunai',
                        'info' => 'transfer',
                        'gray' => 'qris',
                        'warning' => ['tunai_transfer', 'tunai_qris', 'transfer_qris'],
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'tunai_transfer' => 'Tunai & Transfer',
                        'tunai_qris' => 'Tunai & QRIS',
                        'transfer_qris' => 'Transfer & QRIS',
                        default => ucfirst($state),
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(fn() => $this->getOwnerRecord()->recalculate()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(fn() => $this->getOwnerRecord()->recalculate()),

                Tables\Actions\DeleteAction::make()
                    ->after(fn() => $this->getOwnerRecord()->recalculate()),
            ]);
    }
}
