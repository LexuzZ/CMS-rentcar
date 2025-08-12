<?php

namespace App\Filament\Pages;

use App\Models\Car;
use App\Models\CarModel;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class BookingCalendar extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $title = 'Kalender Booking';
    protected static ?string $navigationGroup = 'Transaksi';

    protected static string $view = 'filament.pages.booking-calendar';

    // Properti ini akan menyimpan nilai dari filter
    public ?array $filterData = [
        'mobil' => null,
        'nopol' => null,
    ];

    // Mengisi form dengan nilai awal saat halaman dimuat
    public function mount(): void
    {
        $this->form->fill();
    }

    // Mendefinisikan skema form untuk filter
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('mobil')
                        ->label('Filter Nama Mobil')
                        ->options(CarModel::pluck('name', 'name')) // Ambil data dari model
                        ->searchable()
                        ->live(), // Memicu refresh saat nilai berubah

                    Select::make('nopol')
                        ->label('Filter Nopol')
                        ->options(Car::pluck('nopol', 'nopol')) // Ambil data dari model
                        ->searchable()
                        ->live(), // Memicu refresh saat nilai berubah
                ]),
            ])
            ->statePath('filterData'); // Hubungkan form ke properti $filterData
    }

    // Method ini akan mengirim data filter ke JavaScript di file Blade
    protected function getFilterDataForJs(): array
    {
        return $this->form->getState();
    }
}
