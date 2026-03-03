<?php

namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Filament\Admin\Resources\CompanyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Criar empresa'),
            $this->getCreateAnotherFormAction()->label('Criar e criar outra'),
            $this->getCancelFormAction()->label('Cancelar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Empresa criada com sucesso!';
    }
}
