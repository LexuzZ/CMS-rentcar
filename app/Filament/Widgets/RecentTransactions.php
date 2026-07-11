<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentTransactions extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => '10',
        'lg' => '10',
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Transaksi Hari Ini')
            ->description('Pembayaran yang masuk pada ' . now()->locale('id')->isoFormat('dddd, D MMMM Y'))
            ->headerActions([
                Tables\Actions\Action::make('lihat_semua')
                    ->label('Lihat Semua')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->size('sm')
                    ->color('gray')
                    ->url(route('filament.admin.resources.payments.index'))
                    ->openUrlInNewTab(),
            ])
            ->query(function () {
                return Payment::query()
                    ->with([
                        'invoice:id,booking_id,status',
                        'invoice.booking:id,customer_id',
                        'invoice.booking.customer:id,nama',
                    ])
                    ->whereDate('created_at', today())
                    ->latest();
            })
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->time('H:i')
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->size(TextColumn\TextColumnSize::ExtraSmall)
                    ->color('gray'),

                TextColumn::make('invoice.booking.customer.nama')
                    ->label('Penyewa')
                    ->icon('heroicon-o-user')
                    ->iconColor('gray')
                    ->searchable()
                    ->wrap()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                TextColumn::make('pembayaran')
                    ->label('Nominal')
                    ->alignEnd()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('success'),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->alignCenter()
                    ->badge()
                    ->colors([
                        'success' => 'tunai',
                        'info'    => 'transfer',
                        'gray'    => 'qris',
                        'primary' => 'tunai_transfer',
                        'warning' => 'tunai_qris',
                        'danger'  => 'transfer_qris',
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
                    ->formatStateUsing(fn($state) => $state ? '✓' : '—')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn($state) => $state ? 'Bukti tersedia' : 'Tidak ada bukti'),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat_bukti')
                    ->label('Bukti')
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->size('sm')
                    ->visible(fn($record) => filled($record->proof))
                    ->modalHeading('Bukti Pembayaran')
                    ->modalContent(fn($record) => view('filament.modals.payment-proof', ['record' => $record]))
                    ->modalWidth('md')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->emptyStateIcon('heroicon-o-banknotes')
            ->emptyStateHeading('Belum ada transaksi')
            ->emptyStateDescription('Transaksi hari ini akan muncul di sini.')
            ->striped()
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->poll('60s');
    }
    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
