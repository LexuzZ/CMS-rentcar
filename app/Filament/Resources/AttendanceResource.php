<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $navigationGroup = 'SDM';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Absensi';
    protected static ?string $pluralModelLabel = 'Data Absensi';

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Absensi')->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(today()),

                Forms\Components\TimePicker::make('check_in_time')
                    ->label('Jam Masuk')
                    ->seconds(false),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'hadir'     => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin'      => 'Izin',
                        'alpha'     => 'Alpha',
                    ])
                    ->required()
                    ->default('hadir'),

                Forms\Components\TextInput::make('note')
                    ->label('Catatan')
                    ->maxLength(255),
            ])->columns(2),

            Forms\Components\Section::make('Lokasi GPS')->schema([
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->numeric(),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->numeric(),
                Forms\Components\TextInput::make('distance_meters')
                    ->label('Jarak dari Kantor (m)')
                    ->numeric()
                    ->suffix('meter'),
            ])->columns(3)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => match($state) {
                        'hadir'     => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin'      => 'Izin',
                        'alpha'     => 'Alpha',
                        default     => ucfirst($state),
                    })
                    ->colors([
                        'success' => 'hadir',
                        'warning' => 'terlambat',
                        'info'    => 'izin',
                        'danger'  => 'alpha',
                    ]),

                Tables\Columns\TextColumn::make('distance_meters')
                    ->label('Jarak')
                    ->formatStateUsing(fn($state) => $state ? number_format($state, 0) . ' m' : '—')
                    ->color(fn($state) => $state && $state > 50 ? 'danger' : 'success')
                    ->icon('heroicon-o-map-pin'),

                Tables\Columns\TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(30)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dicatat')
                    ->since()
                    ->color('gray')
                    ->size('sm'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'hadir'     => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin'      => 'Izin',
                        'alpha'     => 'Alpha',
                    ]),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('date')
                    ->label('Bulan Ini')
                    ->query(fn(Builder $q) => $q->whereMonth('date', now()->month)->whereYear('date', now()->year))
                    ->default(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-clock')
            ->emptyStateHeading('Belum ada data absensi')
            ->emptyStateDescription('Data absensi staff akan muncul di sini.');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit'   => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Attendance::whereDate('date', today())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
