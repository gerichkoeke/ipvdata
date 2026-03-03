<?php

namespace App\Filament\Admin\Resources\PartnerResource\Pages;

use App\Filament\Admin\Resources\PartnerResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotifAction;
use Illuminate\Support\Str;

class EditPartner extends EditRecord
{
    protected static string $resource = PartnerResource::class;

    protected static string $view = 'filament.admin.resources.partner-resource.pages.edit-partner';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('manage_user')
                ->label(fn () => $this->getPartnerUser() ? '🔑 Resetar senha' : '👤 Criar usuário de acesso')
                ->icon(fn () => $this->getPartnerUser() ? 'heroicon-o-key' : 'heroicon-o-user-plus')
                ->color(fn () => $this->getPartnerUser() ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn () => $this->getPartnerUser() ? 'Resetar senha do usuário?' : 'Criar usuário de acesso?')
                ->modalDescription(fn () => $this->getPartnerUser()
                    ? 'Uma nova senha será gerada. As credenciais serão exibidas para copiar e enviar ao parceiro.'
                    : 'Será criado um usuário para o Portal do Parceiro. Copie as credenciais e envie ao parceiro.')
                ->modalSubmitActionLabel(fn () => $this->getPartnerUser() ? 'Sim, resetar senha' : 'Sim, criar usuário')
                ->action(function () {
                    $partner  = $this->record;
                    $user     = $this->getPartnerUser();
                    $password = Str::password(12, true, true, false);

                    if ($user) {
                        $user->update([
                            'password'   => bcrypt($password),
                            'is_active'  => $partner->is_active,
                            'partner_id' => $partner->id,
                        ]);
                    } else {
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
                    }

                    $this->dispatch('partner-user-created', [
                        'name'     => $user->name,
                        'email'    => $partner->email,
                        'password' => $password,
                        'url'      => url('/partner-panel'),
                    ]);
                }),

            Actions\DeleteAction::make()
                ->label('Excluir')
                ->modalHeading('Excluir parceiro')
                ->modalDescription('Tem certeza que deseja excluir este parceiro?')
                ->modalSubmitActionLabel('Sim, excluir'),

            Actions\RestoreAction::make()
                ->label('Restaurar')
                ->modalHeading('Restaurar parceiro')
                ->modalSubmitActionLabel('Sim, restaurar'),
        ];
    }

    protected function afterSave(): void
    {
        $partner = $this->record;
        $user    = $this->getPartnerUser();
        if ($user) {
            $user->update(['is_active' => $partner->is_active]);
        }
    }

    protected function getPartnerUser(): ?User
    {
        return User::where('partner_id', $this->record->id)
            ->orWhere(fn ($q) => $q->where('email', $this->record->email)->where('panel', 'partner'))
            ->first();
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('Salvar alterações'),
            $this->getCancelFormAction()->label('Cancelar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Parceiro atualizado com sucesso!';
    }
}
