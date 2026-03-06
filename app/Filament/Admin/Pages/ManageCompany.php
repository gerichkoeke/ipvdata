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
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'Empresa';
    protected static ?string $title           = 'Dados da Empresa';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.admin.pages.manage-company';

    public ?array $data = [];

    public function mount(): void
    {
        $company = Company::first();
        $data = $company?->toArray() ?? [];

        if (!empty($data['logo']) && !Storage::disk('public')->exists($data['logo'])) {
            $data['logo'] = null;
        }

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Identidade da Empresa')
                    ->description('Informações principais da IPVDATA')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo da empresa')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('company/logo')
                            ->deletable()
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'])
                            ->helperText('PNG, JPG, SVG ou WEBP. Máx 4MB.')
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Razão social')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('trade_name')
                                ->label('Nome fantasia')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('cnpj')
                                ->label('CNPJ')
                                ->mask('99.999.999/9999-99')
                                ->maxLength(18),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Empresa ativa')
                                ->default(true)
                                ->inline(false),
                        ]),
                    ]),

                Section::make('Contato')
                    ->description('Informações de contato da empresa')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->email()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('phone')
                                ->label('Telefone')
                                ->tel()
                                ->mask('(99) 99999-9999')
                                ->maxLength(20),

                            Forms\Components\TextInput::make('website')
                                ->label('Website')
                                ->url()
                                ->prefix('https://')
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Endereço')
                    ->description('Localização da empresa')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('zipcode')
                                ->label('CEP')
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
                                ->label('Cidade')
                                ->maxLength(255),

                            Forms\Components\Select::make('state')
                                ->label('Estado (UF)')
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
                                ->label('Endereço completo')
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

        Notification::make()
            ->title('Dados da empresa salvos com sucesso!')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Salvar dados da empresa')
                ->icon('heroicon-m-check')
                ->submit('save'),
        ];
    }
}
