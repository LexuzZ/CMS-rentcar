<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UserActivityStatus extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => '4',
        'lg' => '4',
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->orderBy('last_seen_at', 'desc')
            )
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->badge(),

                // Tables\Columns\TextColumn::make('role')
                //     ->label('Role')
                //     ->badge()
                //     ->alignCenter()
                //     ->formatStateUsing(fn($state) => strtoupper($state))
                //     ->colors([
                //         'primary' => 'superadmin',
                //         'success' => 'admin',
                //         'warning' => 'staff',
                //         'info' => 'supervisor',
                //     ]),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {

                        if (!$record->last_seen_at) {
                            return 'Offline';
                        }

                        $isOnline = $record->last_seen_at->gt(now()->subMinutes(5));

                        return $isOnline ? 'Online' : 'Offline';
                    })
                    ->badge()
                    ->colors([
                        'success' => fn($record) =>
                            $record->last_seen_at &&
                            $record->last_seen_at->gt(now()->subMinutes(5)),

                        'danger' => fn($record) =>
                            !$record->last_seen_at ||
                            $record->last_seen_at->lte(now()->subMinutes(5)),
                    ]),

                Tables\Columns\TextColumn::make('last_seen_at')
                    ->label('Terakhir Aktif')
                    ->since()
                    ->formatStateUsing(
                        fn($state) =>
                        \Carbon\Carbon::parse($state)->locale('id')->diffForHumans()
                    )
                    ->placeholder('Belum pernah login'),
            ])
            ->paginated([4, 5]);
    }

}
