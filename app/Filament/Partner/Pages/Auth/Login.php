<?php

namespace App\Filament\Partner\Pages\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament.partner.pages.auth.login';

    public string $locale = 'pt_BR';

    public function mount(): void
    {
        parent::mount();
        $this->locale = session('locale', config('app.locale', 'pt_BR'));
    }

    public function updatedLocale(): void
    {
        session(['locale' => $this->locale]);
        app()->setLocale($this->locale);
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        if (!auth()->validate([
            'email'    => $data['email'],
            'password' => $data['password'],
        ])) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        $user = \App\Models\User::where('email', $data['email'])->first();

        if (!$user || !$user->canAccessPanel(filament()->getCurrentPanel())) {
            throw ValidationException::withMessages([
                'data.email' => 'Acesso não autorizado.',
            ]);
        }

        // MFA habilitado → challenge
        if ($user->mfa_enabled && $user->mfa_confirmed_at) {
            session([
                'mfa_user_id'    => $user->id,
                'mfa_remember'   => $data['remember'] ?? false,
                'mfa_panel'      => 'partner',
                'mfa_panel_path' => 'partner-panel',
            ]);
            $this->redirect(url('/partner-panel/mfa-challenge'), navigate: false);
            return null;
        }

        auth()->login($user, $data['remember'] ?? false);
        session()->regenerate();

        return app(LoginResponse::class);
    }
}
