<?php

namespace App\Filament\Admin\Resources\MsLicensePoolResource\Pages;

use App\Filament\Admin\Resources\MsLicensePoolResource;
use Filament\Resources\Pages\EditRecord;

class EditMsLicensePool extends EditRecord
{
    protected static string $resource = MsLicensePoolResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
