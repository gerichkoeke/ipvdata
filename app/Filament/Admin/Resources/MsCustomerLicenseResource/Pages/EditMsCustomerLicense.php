<?php

namespace App\Filament\Admin\Resources\MsCustomerLicenseResource\Pages;

use App\Filament\Admin\Resources\MsCustomerLicenseResource;
use Filament\Resources\Pages\EditRecord;

class EditMsCustomerLicense extends EditRecord
{
    protected static string $resource = MsCustomerLicenseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
