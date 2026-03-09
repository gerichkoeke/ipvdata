<?php

namespace App\Filament\Admin\Pages;

use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Storage;

class ManageCompany extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = null;
    protected static ?string $navigationLabel = null;
    protected static ?string $title           = null;
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.admin.pages.manage-company';

    public static function getNavigationGroup(): ?string
    {
        return __('app.settings.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.settings.company');
    }

    public function getTitle(): string
    {
        return __('app.settings.company');
    }

    public ?array $data = [];
    protected ?string $existingLogoPath = null;

    public function mount(): void
    {
        $company = Company::first();
        $initialData = $company?->toArray() ?? [];

        $this->existingLogoPath = $initialData['logo'] ?? null;

        if (!empty($initialData['logo']) && !Storage::disk('public')->exists($initialData['logo'])) {
            $initialData['logo'] = null;
        }

        $this->form->fill($initialData);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make(__('app.company.form.identity_section'))
                    ->description(__('app.company.form.identity_description'))
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label(__('app.company.form.logo'))
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('company/logo')
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'])
                            ->helperText(__('app.company.form.logo_helper'))
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label(__('app.company.form.legal_name'))
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('trade_name')
                                ->label(__('app.company.form.trade_name'))
                                ->maxLength(255),

                            Forms\Components\TextInput::make('cnpj')
                                ->label(__('app.company.form.tax_id'))
                                ->mask('99.999.999/9999-99')
                                ->maxLength(18),

                            Forms\Components\Toggle::make('is_active')
                                ->label(__('app.company.form.active'))
                                ->default(true)
                                ->inline(false),
                        ]),
                    ]),

                Section::make(__('app.company.form.contact'))
                    ->description(__('app.company.form.contact_description'))
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('email')
                                ->label(__('app.email'))
                                ->email()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('phone')
                                ->label(__('app.phone'))
                                ->tel()
                                ->mask('(99) 99999-9999')
                                ->maxLength(20),

                            Forms\Components\TextInput::make('website')
                                ->label(__('app.company.form.website'))
                                ->url()
                                ->prefix('https://')
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make(__('app.company.form.address_section'))
                    ->description(__('app.company.form.address_description'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('zipcode')
                                ->label(__('app.company.form.zipcode'))
                                ->mask('99999-999')
                                ->maxLength(10)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if (!$state || strlen(preg_replace('/\D/', '', $state)) < 8) return;
                                    $cep = preg_replace('/\D/', '', $state);
                                    try {
                                        $response = @file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
                                        if ($response) {
                                            $data = json_decode($response, true);
                                            if (!isset($data['erro'])) {
                                                $set('address', $data['logradouro'] . ($data['complemento'] ? ', ' . $data['complemento'] : ''));
                                                $set('city', $data['localidade']);
                                                $set('state', $data['uf']);
                                            }
                                        }
                                    } catch (\Exception $e) {}
                                }),

                            Forms\Components\TextInput::make('city')
                                ->label(__('app.company.form.city'))
                                ->maxLength(255),

                            Forms\Components\Select::make('state')
                                ->label(__('app.company.form.state'))
                                ->options([
                                    'AC' => 'AC — Acre',        'AL' => 'AL — Alagoas',
                                    'AP' => 'AP — Amapá',       'AM' => 'AM — Amazonas',
                                    'BA' => 'BA — Bahia',       'CE' => 'CE — Ceará',
                                    'DF' => 'DF — Distrito Federal',
                                    'ES' => 'ES — Espírito Santo',
                                    'GO' => 'GO — Goiás',       'MA' => 'MA — Maranhão',
                                    'MT' => 'MT — Mato Grosso', 'MS' => 'MS — Mato Grosso do Sul',
                                    'MG' => 'MG — Minas Gerais','PA' => 'PA — Pará',
                                    'PB' => 'PB — Paraíba',     'PR' => 'PR — Paraná',
                                    'PE' => 'PE — Pernambuco',  'PI' => 'PI — Piauí',
                                    'RJ' => 'RJ — Rio de Janeiro',
                                    'RN' => 'RN — Rio Grande do Norte',
                                    'RS' => 'RS — Rio Grande do Sul',
                                    'RO' => 'RO — Rondônia',    'RR' => 'RR — Roraima',
                                    'SC' => 'SC — Santa Catarina',
                                    'SP' => 'SP — São Paulo',   'SE' => 'SE — Sergipe',
                                    'TO' => 'TO — Tocantins',
                                ])
                                ->searchable()
                                ->native(false),

                            Forms\Components\TextInput::make('address')
                                ->label(__('app.company.form.full_address'))
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (empty($data['logo']) && !empty($this->existingLogoPath)) {
            $data['logo'] = $this->existingLogoPath;
        }

        // Remove o prefixo 'https://' se o usuário digitou completo
        if (isset($data['website']) && str_starts_with($data['website'], 'https://https://')) {
            $data['website'] = str_replace('https://https://', 'https://', $data['website']);
        }

        $company = Company::first();

        if ($company) {
            $company->update($data);
        } else {
            Company::create($data);
        }

        $this->existingLogoPath = $data['logo'] ?? $this->existingLogoPath;

        Notification::make()
            ->title(__('app.company.form.saved'))
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label(__('app.company.form.save'))
                ->icon('heroicon-m-check')
                ->submit('save'),
        ];
    }
}
