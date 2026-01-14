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

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Riwayat Pembayaran';

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
                    fn () => function ($attribute, $value, $fail) {
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
                    ->badge(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(fn () => $this->getOwnerRecord()->recalculate()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(fn () => $this->getOwnerRecord()->recalculate()),

                Tables\Actions\DeleteAction::make()
                    ->after(fn () => $this->getOwnerRecord()->recalculate()),
            ]);
    }
}
