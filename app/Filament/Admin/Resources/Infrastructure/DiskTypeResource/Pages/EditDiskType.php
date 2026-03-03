<?php
namespace App\Filament\Admin\Resources\Infrastructure\DiskTypeResource\Pages;
use App\Filament\Admin\Resources\Infrastructure\DiskTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditDiskType extends EditRecord
{
    protected static string $resource = DiskTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
