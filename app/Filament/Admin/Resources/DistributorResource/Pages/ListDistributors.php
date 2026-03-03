<?php
namespace App\Filament\Admin\Resources\DistributorResource\Pages;
use App\Filament\Admin\Resources\DistributorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListDistributors extends ListRecords
{
    protected static string $resource = DistributorResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
