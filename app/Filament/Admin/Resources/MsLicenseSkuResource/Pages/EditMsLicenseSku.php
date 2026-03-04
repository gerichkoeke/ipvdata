<?php

namespace App\Filament\Admin\Resources\MsLicenseSkuResource\Pages;

use App\Filament\Admin\Resources\MsLicenseSkuResource;
use Filament\Resources\Pages\EditRecord;

class EditMsLicenseSku extends EditRecord
{
    protected static string $resource = MsLicenseSkuResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
