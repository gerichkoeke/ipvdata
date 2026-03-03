<?php
namespace App\Filament\Distributor\Resources\PartnerResource\Pages;
use App\Filament\Distributor\Resources\PartnerResource;
use Filament\Resources\Pages\CreateRecord;
class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['distributor_id'] = auth()->user()->distributor_id;
        return $data;
    }
}
