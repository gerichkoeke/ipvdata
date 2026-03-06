<?php

namespace App\Filament\Distributor\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Database\Eloquent\Model;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.profile.personal_info'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('app.profile.phone'))
                            ->tel()
                            ->maxLength(20),
                    ]),

                Forms\Components\Section::make(__('app.profile.currency') . ' & ' . __('app.profile.language'))
                    ->description('Afeta a exibição de valores em toda a plataforma para seus parceiros')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('distributor.currency')
                                ->label(__('app.profile.currency'))
                                ->options([
                                    'BRL' => '🇧🇷 Real (R$)',
                                    'USD' => '🇺🇸 Dólar (US$)',
                                    'PYG' => '🇵🇾 Guarani (₲)',
                                ])
                                ->native(false)
                                ->required()
                                ->helperText('Moeda padrão para novos parceiros cadastrados por você.')
                                ->afterStateHydrated(function (Forms\Set $set) {
                                    $dist = auth()->user()->distributor;
                                    if ($dist) $set('distributor.currency', $dist->currency);
                                })
                                ->live(),

                            Forms\Components\Select::make('distributor.locale')
                                ->label(__('app.profile.language'))
                                ->options([
                                    'pt_BR' => '🇧🇷 Português (BR)',
                                    'en'    => '🇺🇸 English',
                                    'es'    => '🇪🇸 Español',
                                ])
                                ->native(false)
                                ->required()
                                ->afterStateHydrated(function (Forms\Set $set) {
                                    $dist = auth()->user()->distributor;
                                    if ($dist) $set('distributor.locale', $dist->locale);
                                })
                                ->live(),
                        ]),
                    ]),

                Forms\Components\Section::make(__('app.profile.change_password'))
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Salvar moeda/locale no distribuidor
        $dist = $record->distributor;
        if ($dist) {
            $currency = $data['distributor']['currency'] ?? null;
            $locale   = $data['distributor']['locale'] ?? null;

            if ($currency) $dist->currency = $currency;
            if ($locale)   $dist->locale = $locale;

            $dist->save();

            if ($locale && $record->locale !== $locale) {
                $record->locale = $locale;
            }

        }

        $record->fill($data)->save();
        return $record;
    }
}
