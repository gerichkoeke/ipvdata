<?php

namespace App\Filament\Partner\Pages\Auth;

use App\Services\MfaService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;

class MfaChallenge extends Page
{
    protected static string  $view                    = 'filament.pages.mfa-challenge';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $slug                    = 'mfa-challenge';

    public ?array $data = [];

    public function mount(): void
    {
        if (auth()->check()) {
            $this->redirect(filament()->getHomeUrl());
            return;
        }
        if (!session('mfa_user_id')) {
            $this->redirect(filament()->getLoginUrl());
            return;
        }
        $this->form->fill();
    }

    public function getTitle(): string|Htmlable
    {
        return __('app.auth.mfa_challenge_title');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('app.auth.mfa_challenge_section'))
                ->description(__('app.auth.mfa_challenge_description'))
                ->icon('heroicon-o-shield-check')
                ->schema([
                    TextInput::make('code')
                        ->label(__('app.auth.mfa_code'))
                        ->placeholder('000000')
                        ->numeric()->minLength(6)->maxLength(6)
                        ->required()->autofocus(),
                ]),
        ])->statePath('data');
    }

    public function verify(): void
    {
        $state      = $this->form->getState();
        $code       = $state['code'] ?? '';
        $userId     = session('mfa_user_id');
        $user       = \App\Models\User::find($userId);
        $mfaService = app(MfaService::class);

        if (!$user) {
            $this->addError('data.code', __('app.auth.mfa_session_expired'));
            return;
        }

        $secret = $mfaService->getDecryptedSecret($user);

        if ($mfaService->verifyCode($secret, $code)) {
            session()->forget(['mfa_user_id', 'mfa_remember', 'mfa_panel', 'mfa_panel_path']);
            session(['mfa_verified' => true]);
            auth()->login($user, session('mfa_remember', false));
            session()->regenerate();
            $this->redirect(filament()->getHomeUrl());
        } else {
            $this->addError('data.code', __('app.auth.mfa_invalid'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label(__('app.auth.mfa_verify'))
                ->submit('verify')
                ->color('primary'),
        ];
    }
}
