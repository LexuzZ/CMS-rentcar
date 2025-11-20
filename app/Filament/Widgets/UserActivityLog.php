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
                UserActivity::with('user')->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User'),

                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->colors([
                        'success' => 'create',
                        'warning' => 'update',
                        'danger' => 'delete',
                    ]),

                Tables\Columns\TextColumn::make('module')
                    ->label('Modul')
                    ->formatStateUsing(fn($state) => strtoupper($state))
                    ->badge()
                    ->alignCenter()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Perubahan Terakhir')
                    ->sortable()
                    ->formatStateUsing(
                        fn($state) =>
                        \Carbon\Carbon::parse($state)->locale('id')->diffForHumans()
                    ),
            ])

            // ⬇️ FIX UTAMA DI SINI
            ->paginated([4, 5]);
    }



}
