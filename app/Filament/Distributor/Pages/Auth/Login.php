<?php

namespace App\Filament\Distributor\Pages\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament.distributor.pages.auth.login';

    public string $locale = 'pt_BR';

    public function mount(): void
    {
        parent::mount();
        $this->locale = session('locale', config('app.locale', 'pt_BR'));
    }

    public function setLocale(string $locale): void
    {
        if (!in_array($locale, ['pt_BR', 'en', 'es'])) {
            return;
        }
        $this->locale = $locale;
        session(['locale' => $locale]);
        app()->setLocale($locale);
        $this->dispatch('$refresh');
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

        $sessionLocale = session('locale');
        $chosenLocale = in_array($sessionLocale, ['pt_BR', 'en', 'es'], true)
            ? $sessionLocale
            : ($user->active_locale ?? (in_array($this->locale, ['pt_BR', 'en', 'es'], true) ? $this->locale : 'pt_BR'));

        auth()->login($user, $data['remember'] ?? false);
        session()->regenerate();

        session(['locale' => $chosenLocale]);

        if (in_array($sessionLocale, ['pt_BR', 'en', 'es'], true)) {
            $user->syncLocaleToProfile($chosenLocale);
        }

        app()->setLocale($chosenLocale);

        return app(LoginResponse::class);
    }
}
