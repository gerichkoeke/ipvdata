<?php

namespace App\Filament\Distributor\Pages;

use App\Models\Customer;
use App\Models\Partner;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Distributor\Widgets\DistributorStatsOverview::class,
        ];
    }
}
