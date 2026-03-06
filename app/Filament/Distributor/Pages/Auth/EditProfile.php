<?php

namespace App\Filament\Distributor\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Notifications\Notification;
use App\Models\Distributor;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Pessoais')
                    ->icon('heroicon-o-user')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),
                    ]),

                Forms\Components\Section::make('Moeda e Idioma')
                    ->description('Afeta a exibição de valores em toda a plataforma para seus parceiros')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('distributor.currency')
                                ->label('Moeda Padrão')
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
                                ->dehydrated(false)
                                ->live(),

                            Forms\Components\Select::make('distributor.locale')
                                ->label('Idioma Padrão')
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
                                ->dehydrated(false)
                                ->live(),
                        ]),
                    ]),

                Forms\Components\Section::make('Segurança')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
            ]);
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Salvar moeda/locale no distribuidor
        $dist = $record->distributor;
        if ($dist) {
            $formState = $this->form->getRawState();
            $currency  = $formState['distributor']['currency'] ?? null;
            $locale    = $formState['distributor']['locale']   ?? null;
            if ($currency) $dist->currency = $currency;
            if ($locale)   $dist->locale   = $locale;
            $dist->save();
        }

        $record->fill($data)->save();
        return $record;
    }
}
