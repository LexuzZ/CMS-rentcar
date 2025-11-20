<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UserActivityStatus extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => '6',
        'lg' => '6',
    ];
    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->orderBy('last_seen_at', 'desc')
            )
            ->columns([
                // TextColumn::make('name')->label('User Name'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->colors([
                        'primary' => 'superadmin',
                        'success' => 'admin',
                        'warning' => 'staff',
                        'info' => 'supervisor',
                    ])
                    ->label('Role'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function ($record) {

                        $isOnline = false;

                        if ($record->last_seen_at) {
                            $isOnline = Carbon::parse($record->last_seen_at)
                                ->greaterThan(now()->subMinutes(5)); // online jika aktivitas < 5 menit
                        }

                        return $isOnline
                            ? '🟢 Online'
                            : '🔴 Offline';
                    })
                    ->badge()
                    ->colors([
                        'success' => fn($record) => $record->last_seen_at && Carbon::parse($record->last_seen_at)->greaterThan(now()->subMinutes(5)),
                        'danger' => fn($record) => !$record->last_seen_at || Carbon::parse($record->last_seen_at)->lt(now()->subMinutes(5)),
                    ]),

                Tables\Columns\TextColumn::make('last_seen_at')
                    ->label('Terakhir Aktif')
                    ->since()
                    ->placeholder('Belum pernah login'),
            ]);
    }
}
