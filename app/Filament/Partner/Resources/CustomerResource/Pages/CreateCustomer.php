<?php

namespace App\Filament\Partner\Resources\CustomerResource\Pages;

use App\Filament\Partner\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    // Injetar automaticamente o partner_id do usuário logado
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['partner_id'] = auth()->user()->partner_id;
        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Cadastrar cliente'),
            $this->getCreateAnotherFormAction()->label('Cadastrar e criar outro'),
            $this->getCancelFormAction()->label('Cancelar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cliente cadastrado com sucesso!';
    }
}
