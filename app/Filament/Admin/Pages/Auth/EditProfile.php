<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Services\MfaService;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

class EditProfile extends BaseEditProfile
{
    protected static string $view = 'filament.admin.pages.auth.edit-profile';

    public ?array $mfaData = [];
    public ?string $mfaQrCode = null;
    public ?array $data = [];

    public function getTitle(): string|Htmlable
    {
        return __('app.profile.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->model($this->getUser())
            ->statePath('data')
            ->schema([
                Section::make(__('app.profile.personal_info'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            $this->getNameFormComponent(),
                            $this->getEmailFormComponent(),
                            TextInput::make('phone')
                                ->label(__('app.profile.phone'))
                                ->tel()
                                ->maxLength(20),
                        ]),
                    ]),

                Section::make(__('app.profile.language') . ' & ' . __('app.profile.currency'))
                    ->icon('heroicon-o-language')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('locale')
                                ->label(__('app.profile.language'))
                                ->options([
                                    'pt_BR' => '🇧🇷 Português (BR)',
                                    'en' => '🇺🇸 English',
                                    'es' => '🇪🇸 Español',
                                ])
                                ->native(false)
                                ->required(),

                            Select::make('currency')
                                ->label(__('app.profile.currency'))
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

                Section::make(__('app.profile.change_password'))
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
        $user = auth()->user();
        $isEnabled = $user->mfa_enabled && $user->mfa_confirmed_at;

        return $form->schema([
            Section::make(__('app.profile.mfa_title'))
                ->icon('heroicon-o-shield-check')
                ->description($isEnabled
                    ? __('app.profile.mfa_description_enabled')
                    : __('app.profile.mfa_description_disabled'))
                ->schema($isEnabled ? [
                    Placeholder::make('mfa_status')
                        ->label('')
                        ->content(new HtmlString(
                            '<div class="p-3 bg-green-900/20 rounded-lg border border-green-800 text-green-300 text-sm">'
                            . __('app.profile.mfa_active_html') .
                            '</div>'
                        )),
                ] : [
                    Placeholder::make('mfa_qr')
                        ->label('')
                        ->content(fn () => $this->mfaQrCode
                            ? new HtmlString(
                                "<div class='text-center py-2'>"
                                . "<p class='text-sm text-gray-400 mb-3'>" . __('app.profile.mfa_scan_hint') . "</p>"
                                . "<img src='{$this->mfaQrCode}' class='mx-auto rounded-lg border-2 border-indigo-500 p-2 bg-white' style='width:190px;height:190px' alt='QR Code MFA' />"
                                . "<p class='mt-3 text-xs text-gray-500'>" . __('app.profile.mfa_after_scan') . "</p>"
                                . "</div>"
                            )
                            : new HtmlString('<p class="text-sm text-gray-500 italic">' . __('app.profile.mfa_setup_hint') . '</p>')
                        ),

                    TextInput::make('mfa_code')
                        ->label(__('app.profile.mfa_code_label'))
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['locale'] = $data['locale'] ?? 'pt_BR';
        $data['currency'] = $data['currency'] ?? 'BRL';

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill([
            'name' => $data['name'] ?? $record->name,
            'email' => $data['email'] ?? $record->email,
            'phone' => $data['phone'] ?? null,
            'locale' => $data['locale'] ?? $record->locale ?? 'pt_BR',
            'currency' => $data['currency'] ?? $record->currency ?? 'BRL',
        ]);

        if (!empty($data['password'])) {
            $record->password = Hash::make($data['password']);
        }

        $record->save();

        $record->syncLocaleToProfile($record->locale ?? 'pt_BR');

        app()->setLocale($record->locale ?? 'pt_BR');
        session(['locale' => $record->locale ?? 'pt_BR']);

        return $record;
    }

    public function setupMfa(): void
    {
        $user = auth()->user();
        $svc = app(MfaService::class);
        $secret = $svc->generateSecret();
        $this->mfaQrCode = $svc->getQrCodeInline($user, $secret);
        session(['mfa_temp_secret' => $secret]);

        Notification::make()->title(__('app.profile.mfa_qr_generated'))->info()->send();
    }

    public function confirmMfa(): void
    {
        $code = $this->mfaData['mfa_code'] ?? '';
        $secret = session('mfa_temp_secret');

        if (!$secret) {
            Notification::make()->title(__('app.profile.mfa_session_expired'))->warning()->send();
            return;
        }

        $svc = app(MfaService::class);

        if ($svc->verifyCode($secret, $code)) {
            $svc->enable(auth()->user(), $secret);
            session()->forget('mfa_temp_secret');
            $this->mfaQrCode = null;
            Notification::make()->title(__('app.profile.mfa_enabled_success'))->success()->send();
            $this->redirect(static::getUrl());
        } else {
            Notification::make()->title(__('app.profile.mfa_invalid_code'))->danger()->send();
        }
    }

    public function disableMfa(): void
    {
        app(MfaService::class)->disable(auth()->user());
        Notification::make()->title(__('app.profile.mfa_disabled_success'))->warning()->send();
        $this->redirect(static::getUrl());
    }
}
