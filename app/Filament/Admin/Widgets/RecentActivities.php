<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivities extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Usuários Recentes';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('panel')
                    ->label('Painel')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'admin'   => 'warning',
                        'partner' => 'success',
                        default   => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),

                Tables\Columns\IconColumn::make('mfa_enabled')
                    ->label('MFA')
                    ->boolean(),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Último login')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }
}
