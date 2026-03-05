<?php

namespace App\Filament\Admin\Pages;

use App\Services\MfaService;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class Profile extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Meu Perfil';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $slug            = 'profile';
    protected static ?int    $navigationSort  = 99;
    protected static bool   $shouldRegisterNavigation = false;
    protected static string  $view            = 'filament.admin.pages.profile';

    public ?array $profileData = [];
    public ?array $mfaData     = [];
    public ?string $mfaQrCode  = null;
    public ?string $mfaTempSecret = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->profileData = [
            'name'     => $user->name,
            'email'    => $user->email,
            'phone'    => $user->phone,
            'locale'   => $user->locale ?? 'pt_BR',
            'currency' => $user->currency ?? 'BRL',
        ];
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações Pessoais')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')->label('Nome')->required(),
                            TextInput::make('email')->label('E-mail')->email()->required(),
                            TextInput::make('phone')->label('Telefone')->tel(),
                        ]),
                    ]),

                Section::make('Idioma e Moeda')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('locale')
                                ->label('Idioma')
                                ->options([
                                    'pt_BR' => '🇧🇷 Português (BR)',
                                    'en'    => '🇺🇸 English',
                                    'es'    => '🇪🇸 Español',
                                ])
                                ->native(false)
                                ->required(),

                            Select::make('currency')
                                ->label('Moeda')
                                ->options([
                                    'BRL' => '🇧🇷 Real (R$)',
                                    'USD' => '🇺🇸 Dólar (US$)',
                                    'EUR' => '🇪🇺 Euro (€)',
                                    'ARS' => '🇦🇷 Peso Argentino ($)',
                                    'PYG' => '🇵🇾 Guarani (₲)',
                                ])
                                ->native(false)
                                ->required(),
                        ]),
                    ]),

                Section::make('Alterar Senha')
                    ->icon('heroicon-o-lock-closed')
                    ->collapsed()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('current_password')
                                ->label('Senha atual')->password()->revealable(),
                            TextInput::make('new_password')
                                ->label('Nova senha')->password()->revealable()
                                ->minLength(8),
                            TextInput::make('new_password_confirmation')
                                ->label('Confirmar nova senha')->password()->revealable(),
                        ]),
                    ]),
            ])
            ->statePath('profileData');
    }

    public function mfaForm(Form $form): Form
    {
        $user       = auth()->user();
        $mfaService = app(MfaService::class);
        $isEnabled  = $user->mfa_enabled && $user->mfa_confirmed_at;

        return $form
            ->schema([
                Section::make('Autenticação em Dois Fatores (MFA)')
                    ->icon('heroicon-o-shield-check')
                    ->description($isEnabled
                        ? '✅ MFA está **ativado** na sua conta.'
                        : '⚠️ MFA está **desativado**. Recomendamos ativar para maior segurança.')
                    ->schema($isEnabled ? [
                        Placeholder::make('mfa_status')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                    <div class="text-green-600 dark:text-green-400 text-2xl">🔒</div>
                                    <div>
                                        <p class="font-semibold text-green-700 dark:text-green-300">MFA Ativo</p>
                                        <p class="text-sm text-green-600 dark:text-green-400">Sua conta está protegida com autenticação em dois fatores.</p>
                                    </div>
                                </div>
                            ')),
                    ] : [
                        Placeholder::make('mfa_setup')
                            ->label('')
                            ->content(fn () => $this->mfaQrCode
                                ? new HtmlString("
                                    <div class='text-center'>
                                        <p class='mb-3 text-sm text-gray-600'>Escaneie o QR Code com o <strong>Google Authenticator</strong> ou <strong>Authy</strong></p>
                                        <img src='{$this->mfaQrCode}' alt='QR Code MFA' class='mx-auto rounded-lg border p-2 bg-white' style='width:200px;height:200px' />
                                        <p class='mt-3 text-xs text-gray-500'>Após escanear, digite o código abaixo para confirmar.</p>
                                    </div>
                                ")
                                : new HtmlString('<p class="text-sm text-gray-500">Clique em "Configurar MFA" para gerar o QR Code.</p>')
                            ),

                        TextInput::make('mfa_code')
                            ->label('Código de verificação')
                            ->placeholder('000000')
                            ->numeric()
                            ->length(6)
                            ->visible(fn () => $this->mfaQrCode !== null)
                            ->helperText('Digite o código de 6 dígitos gerado pelo app'),
                    ]),
            ])
            ->statePath('mfaData');
    }

    protected function getForms(): array
    {
        return ['profileForm', 'mfaForm'];
    }

    public function saveProfile(): void
    {
        $data = $this->profileForm->getState();
        $user = auth()->user();

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;

        $user->locale   = $data['locale'];
        $user->currency = $data['currency'];
        app()->setLocale($data['locale']);
        session(['locale' => $data['locale']]);

        if (!empty($data['current_password']) && !empty($data['new_password'])) {
            if (!\Illuminate\Support\Facades\Hash::check($data['current_password'], $user->password)) {
                $this->addError('profileData.current_password', 'Senha atual incorreta.');
                return;
            }
            $user->password = bcrypt($data['new_password']);
        }

        $user->save();

        Notification::make()->title('Perfil atualizado!')->success()->send();
    }

    public function setupMfa(): void
    {
        $user           = auth()->user();
        $mfaService     = app(MfaService::class);
        $secret         = $mfaService->generateSecret();
        $this->mfaTempSecret = $secret;
        $this->mfaQrCode    = $mfaService->getQrCodeInline($user, $secret);
        session(['mfa_temp_secret' => $secret]);
    }

    public function confirmMfa(): void
    {
        $data   = $this->mfaForm->getState();
        $code   = $data['mfa_code'] ?? '';
        $secret = session('mfa_temp_secret');

        if (!$secret) {
            Notification::make()->title('Sessão expirada. Clique em Configurar MFA novamente.')->warning()->send();
            return;
        }

        $mfaService = app(MfaService::class);

        if ($mfaService->verifyCode($secret, $code)) {
            $mfaService->enable(auth()->user(), $secret);
            session()->forget('mfa_temp_secret');
            $this->mfaQrCode     = null;
            $this->mfaTempSecret = null;
            session(['mfa_verified' => true]);
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
