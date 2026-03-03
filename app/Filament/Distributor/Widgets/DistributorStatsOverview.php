<?php

namespace App\Filament\Distributor\Widgets;

use App\Models\Customer;
use App\Models\Partner;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DistributorStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $distId = auth()->user()->distributor_id;

        $totalPartners  = Partner::where('distributor_id', $distId)->count();
        $activePartners = Partner::where('distributor_id', $distId)->where('is_active', true)->count();
        $inactivePartners = $totalPartners - $activePartners;

        $totalCustomers = Customer::whereHas('partner', function ($q) use ($distId) {
            $q->where('distributor_id', $distId);
        })->count();

        $activeCustomers = Customer::whereHas('partner', function ($q) use ($distId) {
            $q->where('distributor_id', $distId);
        })->where('is_active', true)->count();

        $dist = auth()->user()->distributor;
        $currency = $dist ? $dist->currency : 'BRL';
        $symbol = match($currency) { 'USD' => 'US$', 'PYG' => '₲', default => 'R$' };

        return [
            Stat::make('Parceiros', $totalPartners)
                ->description($activePartners . ' ativos · ' . $inactivePartners . ' inativos')
                ->icon('heroicon-o-building-office-2')
                ->color('primary'),

            Stat::make('Clientes', $totalCustomers)
                ->description($activeCustomers . ' ativos na rede')
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Moeda Padrão', $symbol . ' ' . $currency)
                ->description('Configurado no perfil')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),
        ];
    }
}
