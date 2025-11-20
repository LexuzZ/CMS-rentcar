<?php

namespace App\Filament\Widgets;

use App\Models\UserActivity;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UserActivityLog extends BaseWidget
{
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => '6',
        'lg' => '6',
    ];
    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserActivity::with('user')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->width('33%'),

                // ACTION
                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->colors([
                        'success' => 'create',
                        'warning' => 'update',
                        'danger' => 'delete',
                    ]),

                // MODEL / MODULE
                Tables\Columns\TextColumn::make('module')
                    ->label('Modul')
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->badge()
                    ->color('primary'),

                // DESCRIPTION
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->width('33%'),

                // WAKTU
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->formatStateUsing(
                        fn($state) =>
                        \Carbon\Carbon::parse($state)->locale('id')->diffForHumans()
                    )
                    ->sortable()
                    ->width('33%'),
            ]);
    }
}
