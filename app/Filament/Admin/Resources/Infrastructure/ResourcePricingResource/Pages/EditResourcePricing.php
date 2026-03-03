<?php
namespace App\Filament\Admin\Resources\Infrastructure\ResourcePricingResource\Pages;
use App\Filament\Admin\Resources\Infrastructure\ResourcePricingResource;
use Filament\Resources\Pages\EditRecord;

class EditResourcePricing extends EditRecord
{
    protected static string $resource = ResourcePricingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
