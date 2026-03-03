<?php

namespace App\Filament\Admin\Resources\PartnerResource\Pages;

use App\Filament\Admin\Resources\PartnerResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;

    protected static string $view = 'filament.admin.resources.partner-resource.pages.create-partner';

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Cadastrar parceiro'),
            $this->getCreateAnotherFormAction()->label('Cadastrar e criar outro'),
            $this->getCancelFormAction()->label('Cancelar'),
        ];
    }

    protected function afterCreate(): void
    {
        $partner = $this->record;

        $existing = User::where('partner_id', $partner->id)
            ->orWhere(fn ($q) => $q->where('email', $partner->email)->where('panel', 'partner'))
            ->first();

        if ($existing) {
            if (!$existing->partner_id) {
                $existing->update(['partner_id' => $partner->id]);
            }
            return;
        }

        $password = Str::password(12, true, true, false);

        $user = User::create([
            'name'       => $partner->trade_name ?? $partner->company_name,
            'email'      => $partner->email,
            'password'   => bcrypt($password),
            'panel'      => 'partner',
            'partner_id' => $partner->id,
            'is_active'  => $partner->is_active,
            'locale'     => 'pt_BR',
        ]);

        $user->assignRole('partner_admin');

        $this->dispatch('partner-user-created', [
            'name'     => $user->name,
            'email'    => $partner->email,
            'password' => $password,
            'url'      => url('/partner-panel'),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        // Não redirecionar automaticamente — o modal cuida disso
        return static::getResource()::getUrl('create');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return null;
    }
}
