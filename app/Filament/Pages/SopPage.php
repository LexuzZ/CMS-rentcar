<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SopPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string $view = 'filament.pages.sop-page';
    protected static ?int $navigationSort = 8;



    protected static ?string $title = 'Standar Operasional Prosedur (SOP)';

    protected static ?string $navigationLabel = 'SOP Kerja';
}
