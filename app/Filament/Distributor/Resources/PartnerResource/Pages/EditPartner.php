<?php
namespace App\Filament\Distributor\Resources\PartnerResource\Pages;
use App\Filament\Distributor\Resources\PartnerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditPartner extends EditRecord
{
    protected static string $resource = PartnerResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
