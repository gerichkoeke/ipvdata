<?php

namespace App\Filament\Distributor\Resources\CustomerResource\Pages;

use App\Filament\Distributor\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo cliente'),
        ];
    }

    public function getTabs(): array
    {
        $distId = auth()->user()->distributor_id;

        $base = Customer::whereHas('partner', fn($q) => $q->where('distributor_id', $distId));

        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-m-users')
                ->badge($base->clone()->count()),

            'active' => Tab::make('Ativos')
                ->icon('heroicon-m-check-circle')
                ->badge($base->clone()->where('is_active', true)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('is_active', true)),

            'inactive' => Tab::make('Inativos')
                ->icon('heroicon-m-x-circle')
                ->badge($base->clone()->where('is_active', false)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('is_active', false)),
        ];
    }
}
