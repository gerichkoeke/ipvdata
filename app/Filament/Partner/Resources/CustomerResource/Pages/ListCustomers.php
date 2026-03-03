<?php

namespace App\Filament\Partner\Resources\CustomerResource\Pages;

use App\Filament\Partner\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
        $partnerId = auth()->user()?->partner_id;

        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-m-users')
                ->badge(Customer::withoutTrashed()->where('partner_id', $partnerId)->count()),

            'active' => Tab::make('Ativos')
                ->icon('heroicon-m-check-circle')
                ->badge(Customer::withoutTrashed()->where('partner_id', $partnerId)->where('is_active', true)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('is_active', true)),

            'inactive' => Tab::make('Inativos')
                ->icon('heroicon-m-x-circle')
                ->badge(Customer::withoutTrashed()->where('partner_id', $partnerId)->where('is_active', false)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('is_active', false)),

            'trashed' => Tab::make('Excluídos')
                ->icon('heroicon-m-trash')
                ->badge(Customer::onlyTrashed()->where('partner_id', $partnerId)->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $q) =>
                    $q->withoutGlobalScope(SoftDeletingScope::class)
                      ->whereNotNull('customers.deleted_at')
                ),
        ];
    }
}
