<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo usuário'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-m-users'),

            'admin' => Tab::make('Admin')
                ->icon('heroicon-m-shield-check')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('panel', 'admin')),

            'partner' => Tab::make('Partner')
                ->icon('heroicon-m-briefcase')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('panel', 'partner')),

            'active' => Tab::make('Ativos')
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)),

            'inactive' => Tab::make('Inativos')
                ->icon('heroicon-m-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false)),
        ];
    }
}
