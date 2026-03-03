<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Criar usuário'),
            $this->getCreateAnotherFormAction()->label('Criar e criar outro'),
            $this->getCancelFormAction()->label('Cancelar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Usuário criado com sucesso!';
    }
}
