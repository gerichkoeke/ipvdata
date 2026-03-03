<?php

namespace App\Filament\Admin\Resources\PartnerResource\Pages;

use App\Filament\Admin\Resources\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPartners extends ListRecords
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Novo parceiro'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos')
                ->icon('heroicon-m-briefcase'),

            'active' => Tab::make('Ativos')
                ->icon('heroicon-m-check-circle')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('is_active', true)),

            'inactive' => Tab::make('Inativos')
                ->icon('heroicon-m-x-circle')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('is_active', false)),

            'trashed' => Tab::make('Excluídos')
                ->icon('heroicon-m-trash')
                ->modifyQueryUsing(fn (Builder $q) => $q->onlyTrashed()),
        ];
    }
}
