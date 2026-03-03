<?php

namespace App\Filament\Partner\Pages\Auth;

use App\Services\MfaService;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\Support\Htmlable;

class EditProfile extends BaseEditProfile
{
    protected static string $view = 'filament.partner.pages.edit-profile';

    public ?array $mfaData  = [];
    public ?string $mfaQrCode = null;


    public ?array $data = [];

    public function mount(): void
    {
        $user = \App\Models\User::find(
            filament()->auth()->id() ?? auth()->id()
        );

        if ($user) {
            $this->form->fill([
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ]);
        }

        $this->mfaForm->fill([]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = \App\Models\User::find(
            filament()->auth()->id() ?? auth()->id()
        );

        if (!$user) {
            \Filament\Notifications\Notification::make()
                ->title('Erro ao salvar.')
                ->danger()
                ->send();
            return;
        }

        $user->update(array_filter([
            'name'  => $data['name']  ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ], fn ($v) => $v !== null));

        // Atualizar senha se fornecida
        if (!empty($data['password'])) {
            $user->update(['password' => \Illuminate\Support\Facades\Hash::make($data['password'])]);
        }

        \Filament\Notifications\Notification::make()
            ->title('Perfil atualizado com sucesso! ✅')
            ->success()
            ->send();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Meu Perfil';
    }

    // Preencher o form com os dados do usuário logado
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Usar $data que já vem de getUser()->attributesToArray()
        // chamado pelo parent::mount() via fillForm()
        return [
            'name'  => $data['name']  ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
        ];
    }

    // Salvar os dados de volta no usuário
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return array_filter([
            'name'  => $data['name']  ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ], fn ($v) => $v !== null);
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->getUser())
            ->statePath('data')
            ->schema([
                Section::make('Informações Pessoais')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nome completo')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('E-mail')
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ]),

                        TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('(00) 00000-0000'),
                    ]),

                Section::make('Alterar Senha')
                    ->icon('heroicon-o-lock-closed')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ]),
                    ]),
            ]);
    }

    public function mfaForm(Form $form): Form
    {
        $user      = auth()->user();
        $isEnabled = $user->mfa_enabled && $user->mfa_confirmed_at;

        return $form->schema([
            Section::make('Autenticação em Dois Fatores')
                ->icon('heroicon-o-shield-check')
                ->description($isEnabled
                    ? '🔒 MFA ativo — sua conta está protegida.'
                    : '⚠️ MFA desativado — recomendamos ativar.')
                ->schema($isEnabled ? [
                    Placeholder::make('mfa_status')
                        ->label('')
                        ->content(new HtmlString('
                            <div class="p-3 bg-green-900/20 rounded-lg border border-green-800 text-green-300 text-sm">
                                ✅ MFA está <strong>ativo</strong> na sua conta.
                            </div>
                        ')),
                ] : [
                    Placeholder::make('mfa_qr')
                        ->label('')
                        ->content(fn () => $this->mfaQrCode
                            ? new HtmlString("
                                <div class='text-center py-2'>
                                    <p class='text-sm text-gray-400 mb-3'>
                                        Escaneie com o <strong>Google Authenticator</strong> ou <strong>Authy</strong>
                                    </p>
                                    <img src='{$this->mfaQrCode}'
                                         class='mx-auto rounded-lg border-2 border-indigo-500 p-2 bg-white'
                                         style='width:190px;height:190px'
                                         alt='QR Code MFA' />
                                    <p class='mt-3 text-xs text-gray-500'>
                                        Depois de escanear, digite o código abaixo para confirmar
                                    </p>
                                </div>
                            ")
                            : new HtmlString('
                                <p class="text-sm text-gray-500 italic">
                                    Clique em "Configurar MFA" para gerar o QR Code.
                                </p>
                            ')
                        ),

                    TextInput::make('mfa_code')
                        ->label('Código de verificação (6 dígitos)')
                        ->placeholder('000000')
                        ->numeric()
                        ->minLength(6)
                        ->maxLength(6)
                        ->visible(fn () => $this->mfaQrCode !== null),
                ]),
        ])->statePath('mfaData');
    }

    protected function getForms(): array
    {
        return ['form', 'mfaForm'];
    }

    public function setupMfa(): void
    {
        $user          = auth()->user();
        $svc           = app(MfaService::class);
        $secret        = $svc->generateSecret();
        $this->mfaQrCode = $svc->getQrCodeInline($user, $secret);
        session(['mfa_temp_secret' => $secret]);

        Notification::make()->title('QR Code gerado! Escaneie com o app.')->info()->send();
    }

    public function confirmMfa(): void
    {
        $code   = $this->mfaData['mfa_code'] ?? '';
        $secret = session('mfa_temp_secret');

        if (!$secret) {
            Notification::make()->title('Sessão expirada. Clique em Configurar MFA.')->warning()->send();
            return;
        }

        $svc = app(MfaService::class);
        if ($svc->verifyCode($secret, $code)) {
            $svc->enable(auth()->user(), $secret);
            session()->forget('mfa_temp_secret');
            $this->mfaQrCode = null;
            Notification::make()->title('MFA ativado com sucesso! 🔒')->success()->send();
            $this->redirect(static::getUrl());
        } else {
            Notification::make()->title('Código inválido. Tente novamente.')->danger()->send();
        }
    }

    public function disableMfa(): void
    {
        app(MfaService::class)->disable(auth()->user());
        Notification::make()->title('MFA desativado.')->warning()->send();
        $this->redirect(static::getUrl());
    }
}
