<?php

namespace App\Filament\Partner\Resources\CustomerResource\Pages;

use App\Filament\Partner\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label(__('app.delete'))
                ->modalHeading(__('app.customers.deleted'))
                ->modalSubmitActionLabel(__('app.confirm')),
            Actions\RestoreAction::make()->label(__('app.refresh')),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label(__('app.save')),
            $this->getCancelFormAction()->label(__('app.cancel')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('app.customers.saved');
    }
}
