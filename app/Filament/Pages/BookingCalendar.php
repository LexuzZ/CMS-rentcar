<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Livewire\Livewire;

class BookingCalendar extends Page
{
protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static string $view = 'filament.pages.booking-calendar';
    protected static ?string $title = 'Kalender Booking';

}
