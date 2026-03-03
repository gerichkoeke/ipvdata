<?php
namespace App\Filament\Admin\Resources\DistributorResource\Pages;
use App\Filament\Admin\Resources\DistributorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditDistributor extends EditRecord
{
    protected static string $resource = DistributorResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
