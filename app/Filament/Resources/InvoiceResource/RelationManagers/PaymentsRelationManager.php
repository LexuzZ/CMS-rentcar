<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
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
                    'tunai'          => 'Tunai',
                    'transfer'       => 'Transfer',
                    'qris'           => 'QRIS',
                    'tunai_transfer' => 'Tunai & Transfer',
                    'tunai_qris'     => 'Tunai & QRIS',
                    'transfer_qris'  => 'Transfer & QRIS',
                ])
                ->required(),

            FileUpload::make('proof')
                ->label('Bukti Pembayaran')
                ->image()
                ->directory('bukti_payment')
                ->disk('public')
                ->visibility('public')
                ->downloadable()
                ->openable()
                ->imagePreviewHeight('150'),
        ]);
    }

    /* =======================
     | INFOLIST (Detail View)
     ======================= */
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Detail Pembayaran')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('tanggal_pembayaran')
                            ->label('Tanggal Pembayaran')
                            ->date('d M Y')
                            ->icon('heroicon-o-calendar-days'),

                        TextEntry::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->icon('heroicon-o-credit-card')
                            ->badge()
                            ->colors([
                                'success' => 'tunai',
                                'info'    => 'transfer',
                                'gray'    => 'qris',
                                'warning' => ['tunai_transfer', 'tunai_qris', 'transfer_qris'],
                            ])
                            ->formatStateUsing(fn($state) => match ($state) {
                                'tunai'          => 'Tunai',
                                'transfer'       => 'Transfer',
                                'qris'           => 'QRIS',
                                'tunai_transfer' => 'Tunai & Transfer',
                                'tunai_qris'     => 'Tunai & QRIS',
                                'transfer_qris'  => 'Transfer & QRIS',
                                default          => ucfirst($state),
                            }),

                        TextEntry::make('pembayaran')
                            ->label('Jumlah Pembayaran')
                            ->icon('heroicon-o-currency-dollar')
                            ->money('IDR', true)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->color('success'),

                        TextEntry::make('created_at')
                            ->label('Dicatat Pada')
                            ->icon('heroicon-o-clock')
                            ->dateTime('d M Y, H:i')
                            ->since(),
                    ]),
                ]),

            Section::make('Bukti Pembayaran')
                ->icon('heroicon-o-photo')
                ->schema([
                    ImageEntry::make('proof')
                        ->label('')
                        ->disk('public')
                        ->height(280)
                        ->extraImgAttributes(['style' => 'border-radius:8px; object-fit:contain;']),
                ])
                ->visible(fn($record) => filled($record->proof))
                ->collapsible(),
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
                    ->date('d M Y')
                    ->icon('heroicon-o-calendar-days'),

                TextColumn::make('pembayaran')
                    ->label('Jumlah')
                    ->money('IDR', true)
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->wrap()
                    ->width(150)
                    ->alignCenter()
                    ->colors([
                        'success' => 'tunai',
                        'info'    => 'transfer',
                        'gray'    => 'qris',
                        'warning' => ['tunai_transfer', 'tunai_qris', 'transfer_qris'],
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'tunai'          => 'Tunai',
                        'transfer'       => 'Transfer',
                        'qris'           => 'QRIS',
                        'tunai_transfer' => 'Tunai & Transfer',
                        'tunai_qris'     => 'Tunai & QRIS',
                        'transfer_qris'  => 'Transfer & QRIS',
                        default          => ucfirst($state),
                    }),

                TextColumn::make('proof')
                    ->label('Bukti')
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => $state ? '✓ Ada' : '—')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(fn() => $this->getOwnerRecord()->recalculate()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Pembayaran')
                    ->modalWidth('lg'),

                Tables\Actions\EditAction::make()
                    ->after(fn() => $this->getOwnerRecord()->recalculate()),

                Tables\Actions\DeleteAction::make()
                    ->after(fn() => $this->getOwnerRecord()->recalculate()),
            ]);
    }
}
