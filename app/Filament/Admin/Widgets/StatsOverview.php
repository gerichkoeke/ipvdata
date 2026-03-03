<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalPartners = User::where('panel', 'partner')->count();
        $activePartners = User::where('panel', 'partner')->where('is_active', true)->count();
        $totalAdmins = User::where('panel', 'admin')->count();
        $mfaEnabled = User::where('mfa_enabled', true)->whereNotNull('mfa_confirmed_at')->count();

        return [
            Stat::make('Total de Parceiros', $totalPartners)
                ->description($activePartners . ' ativos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-briefcase'),

            Stat::make('Administradores', $totalAdmins)
                ->description('Usuários admin ativos')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning')
                ->icon('heroicon-o-users'),

            Stat::make('MFA Habilitado', $mfaEnabled)
                ->description('de ' . ($totalPartners + $totalAdmins) . ' usuários')
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color('info')
                ->icon('heroicon-o-shield-check'),

            Stat::make('Parceiros Inativos', $totalPartners - $activePartners)
                ->description('Necessitam atenção')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalPartners - $activePartners > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
