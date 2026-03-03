<?php

namespace App\Filament\Partner\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PartnerStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user      = filament()->auth()->user();
        $partnerId = $user?->partner_id;

        $totalClients    = Customer::where('partner_id', $partnerId)->count();
        $activeClients   = Customer::where('partner_id', $partnerId)->where('is_active', true)->count();
        $inactiveClients = $totalClients - $activeClients;

        // MRR total dos projetos ativos dos clientes deste parceiro
        $mrr = \App\Models\Project::whereHas('customer', fn($q) => $q->where('partner_id', $partnerId))
            ->where('status', 'active')
            ->sum('monthly_value');

        return [
            Stat::make('Total de Clientes', $totalClients)
                ->description('Clientes vinculados')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make('Clientes Ativos', $activeClients)
                ->description('Com acesso ativo')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Clientes Inativos', $inactiveClients)
                ->description('Sem acesso')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($inactiveClients > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-x-circle'),

            Stat::make('MFA', $user?->mfa_enabled && $user?->mfa_confirmed_at ? '✅ Ativo' : '⚠️ Inativo')
                ->description($user?->mfa_enabled ? 'Conta protegida' : 'Recomendamos ativar')
                ->color($user?->mfa_enabled ? 'success' : 'warning')
                ->icon('heroicon-o-shield-check'),
        ];
    }
}
