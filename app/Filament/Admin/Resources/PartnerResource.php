<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PartnerResource\Pages;
use App\Models\Distributor;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerResource extends Resource
{
    protected static ?string $model            = Partner::class;
    protected static ?string $navigationIcon   = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup  = 'Parceiros';
    protected static ?string $navigationLabel  = 'Parceiros';
    protected static ?string $modelLabel       = 'Parceiro';
    protected static ?string $pluralModelLabel = 'Parceiros';
    protected static ?int    $navigationSort   = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Distribuidor')
                ->description('Vincule este parceiro a um distribuidor (opcional)')
                ->icon('heroicon-o-building-storefront')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\Select::make('distributor_id')
                            ->label('Distribuidor')
                            ->options(Distributor::where('is_active', true)->orderBy('trade_name')->pluck('trade_name', 'id'))
                            ->native(false)
                            ->searchable()
                            ->nullable()
                            ->placeholder('Sem distribuidor (direto ao admin)')
                            ->helperText('Se vinculado, este parceiro ficará sob gestão do distribuidor selecionado.')
                            ->live(),

                        Forms\Components\Placeholder::make('distributor_info')
                            ->label('Comissão do Distribuidor')
                            ->content(function (Forms\Get $get) {
                                $distId = $get('distributor_id');
                                if (!$distId) {
                                    return new \Illuminate\Support\HtmlString('<span class="text-sm text-gray-400">Nenhum distribuidor selecionado.</span>');
                                }
                                $dist = Distributor::find($distId);
                                if (!$dist) return '-';
                                return new \Illuminate\Support\HtmlString(
                                    "<span class='text-sm'>Distribuidor: <strong>{$dist->display_name}</strong> — Comissão: <strong>{$dist->commission_pct}%</strong></span>"
                                );
                            }),
                    ]),
                ]),

            Section::make('Busca por CNPJ')
                ->description('Digite o CNPJ para preencher os dados automaticamente via ReceitaWS')
                ->icon('heroicon-o-magnifying-glass')
                ->schema([
                    Grid::make(3)->schema([
                        Forms\Components\TextInput::make('cnpj')
                            ->label('CNPJ')
                            ->mask('99.999.999/9999-99')
                            ->unique(Partner::class, 'cnpj', ignoreRecord: true)
                            ->maxLength(18)
                            ->live(onBlur: true)
                            ->suffixAction(
                                Action::make('buscar_cnpj')
                                    ->icon('heroicon-m-magnifying-glass')
                                    ->tooltip('Buscar dados do CNPJ')
                                    ->action(function (Forms\Set $set, Forms\Get $get) {
                                        $cnpj = preg_replace('/\D/', '', $get('cnpj'));
                                        if (strlen($cnpj) !== 14) {
                                            Notification::make()->title('CNPJ inválido')->warning()->send();
                                            return;
                                        }
                                        try {
                                            $ctx = stream_context_create([
                                                'http' => ['timeout' => 10, 'user_agent' => 'IPV-ERP/1.0', 'ignore_errors' => true],
                                                'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
                                            ]);
                                            $data = json_decode(@file_get_contents("https://receitaws.com.br/v1/cnpj/{$cnpj}", false, $ctx), true);

                                            if (!$data || ($data['status'] ?? '') === 'ERROR') {
                                                $data2 = json_decode(@file_get_contents("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}", false, $ctx), true);
                                                if ($data2 && !isset($data2['message'])) {
                                                    $set('company_name', $data2['razao_social'] ?? '');
                                                    $set('trade_name',   $data2['nome_fantasia'] ?? '');
                                                    $set('email',        strtolower($data2['email'] ?? ''));
                                                    $set('phone',        self::formatPhone($data2['ddd_telefone_1'] ?? ''));
                                                    $set('zipcode',      preg_replace('/\D/', '', $data2['cep'] ?? ''));
                                                    $set('address',      trim(($data2['logradouro'] ?? '') . ' ' . ($data2['numero'] ?? '')));
                                                    $set('city',         $data2['municipio'] ?? '');
                                                    $set('state',        $data2['uf'] ?? '');
                                                    Notification::make()->title('CNPJ encontrado! ✅')->body('Dados preenchidos via BrasilAPI.')->success()->send();
                                                    return;
                                                }
                                                Notification::make()->title('CNPJ não encontrado')->warning()->send();
                                                return;
                                            }

                                            $set('company_name', $data['nome'] ?? '');
                                            $set('trade_name',   $data['fantasia'] ?? '');
                                            $set('email',        strtolower($data['email'] ?? ''));
                                            $set('phone',        self::formatPhone($data['telefone'] ?? ''));
                                            if (!empty($data['website'])) {
                                                $set('website', rtrim(str_replace(['http://', 'https://'], '', $data['website']), '/'));
                                            }
                                            $address = trim(($data['logradouro'] ?? '') . ($data['numero'] ? ', ' . $data['numero'] : '') . ($data['complemento'] ? ' - ' . $data['complemento'] : ''));
                                            $set('address', $address);
                                            $set('city',    $data['municipio'] ?? '');
                                            $set('state',   $data['uf'] ?? '');
                                            $set('zipcode', preg_replace('/\D/', '', $data['cep'] ?? ''));
                                            Notification::make()->title('CNPJ encontrado! ✅')->body('Dados preenchidos. Revise as informações.')->success()->send();
                                        } catch (\Exception $e) {
                                            Notification::make()->title('Erro ao consultar CNPJ')->danger()->send();
                                        }
                                    })
                            ),

                        Forms\Components\Placeholder::make('cnpj_info')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<p class="text-sm text-gray-500 mt-2">💡 Digite o CNPJ e clique em 🔍 para buscar automaticamente.<br>Fontes: <strong>ReceitaWS</strong> e <strong>BrasilAPI</strong></p>'
                            ))
                            ->columnSpan(2),
                    ]),
                ]),

            Section::make('Dados da Empresa')
                ->description('Informações cadastrais do parceiro')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Forms\Components\FileUpload::make('logo')
                        ->label('Logo')
                        ->image()
                        ->imageEditor()
                        ->circleCropper()
                        ->directory('partners/logos')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'])
                        ->helperText('PNG, JPG, WEBP. Máx 2MB.')
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Razão social')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('trade_name')
                            ->label('Nome fantasia')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(Partner::class, 'email', ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Parceiro ativo')
                            ->default(true)
                            ->inline(false),
                    ]),
                ]),

            Section::make('Endereço')
                ->description('Localização do parceiro')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Grid::make(3)->schema([
                        Forms\Components\TextInput::make('zipcode')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->maxLength(10)
                            ->live(onBlur: true)
                            ->suffixAction(
                                Action::make('buscar_cep')
                                    ->icon('heroicon-m-magnifying-glass')
                                    ->tooltip('Buscar endereço pelo CEP')
                                    ->action(function (Forms\Set $set, Forms\Get $get) {
                                        $cep = preg_replace('/\D/', '', $get('zipcode') ?? '');
                                        if (strlen($cep) !== 8) return;
                                        try {
                                            $ctx  = stream_context_create(['ssl' => ['verify_peer' => false]]);
                                            $data = json_decode(@file_get_contents("https://viacep.com.br/ws/{$cep}/json/", false, $ctx), true);
                                            if ($data && !isset($data['erro'])) {
                                                $set('address', $data['logradouro'] . ($data['complemento'] ? ', ' . $data['complemento'] : ''));
                                                $set('city',    $data['localidade']);
                                                $set('state',   $data['uf']);
                                            }
                                        } catch (\Exception $e) {}
                                    })
                            ),

                        Forms\Components\TextInput::make('city')
                            ->label('Cidade')
                            ->maxLength(255),

                        Forms\Components\Select::make('state')
                            ->label('Estado (UF)')
                            ->options([
                                'AC'=>'AC — Acre','AL'=>'AL — Alagoas','AP'=>'AP ��� Amapá',
                                'AM'=>'AM — Amazonas','BA'=>'BA — Bahia','CE'=>'CE — Ceará',
                                'DF'=>'DF — Distrito Federal','ES'=>'ES — Espírito Santo',
                                'GO'=>'GO — Goiás','MA'=>'MA — Maranhão','MT'=>'MT — Mato Grosso',
                                'MS'=>'MS — Mato Grosso do Sul','MG'=>'MG — Minas Gerais',
                                'PA'=>'PA — Pará','PB'=>'PB — Paraíba','PR'=>'PR — Paraná',
                                'PE'=>'PE — Pernambuco','PI'=>'PI — Piauí',
                                'RJ'=>'RJ — Rio de Janeiro','RN'=>'RN — Rio Grande do Norte',
                                'RS'=>'RS — Rio Grande do Sul','RO'=>'RO — Rondônia',
                                'RR'=>'RR — Roraima','SC'=>'SC — Santa Catarina',
                                'SP'=>'SP — São Paulo','SE'=>'SE — Sergipe','TO'=>'TO — Tocantins',
                            ])
                            ->searchable()
                            ->native(false),

                        Forms\Components\TextInput::make('address')
                            ->label('Endereço completo')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                ]),

            Section::make('Configurações de Proposta')
                ->description('Personalização das propostas comerciais')
                ->icon('heroicon-o-document-text')
                ->collapsed()
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\ColorPicker::make('proposal_header_color')
                            ->label('Cor do cabeçalho')
                            ->default('#1e40af'),

                        Forms\Components\TextInput::make('proposal_footer_text')
                            ->label('Texto do rodapé')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('proposal_terms')
                            ->label('Termos e condições')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                ]),

            Section::make('Moeda e Idioma')
                ->description('Define a moeda usada em todos os projetos e clientes deste parceiro')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\Select::make('currency')
                            ->label('Moeda')
                            ->options([
                                'BRL' => '🇧🇷 Real (R$)',
                                'USD' => '🇺🇸 Dólar (US$)',
                                'PYG' => '🇵🇾 Guarani (₲)',
                            ])
                            ->default('BRL')
                            ->native(false)
                            ->required()
                            ->live()
                            ->helperText('Todos os valores deste parceiro e seus clientes serão exibidos nesta moeda.'),

                        Forms\Components\Select::make('locale')
                            ->label('Idioma')
                            ->options([
                                'pt_BR' => '🇧🇷 Português (BR)',
                                'en_US' => '🇺🇸 English (US)',
                                'es'    => '🇪🇸 Español',
                            ])
                            ->default('pt_BR')
                            ->native(false)
                            ->required()
                            ->helperText('Idioma padrão para propostas e interface do parceiro.'),
                    ]),
                ]),

            Section::make('Comissão')
                ->description('Regras de comissionamento do parceiro')
                ->icon('heroicon-o-banknotes')
                ->collapsed()
                ->schema([
                    Grid::make(1)->schema([
                        Forms\Components\Radio::make('commission_model')
                            ->label('Modelo de comissão')
                            ->options([
                                'fixed'    => '🔒 Fixa — percentual definido pela IPVDATA',
                                'variable' => '🔓 Variável — parceiro define o percentual na proposta',
                            ])
                            ->default('fixed')
                            ->required()
                            ->live()
                            ->descriptions([
                                'fixed'    => 'O parceiro sempre recebe o percentual definido abaixo.',
                                'variable' => 'O parceiro pode escolher o percentual dentro do intervalo permitido.',
                            ])
                            ->columnSpanFull(),
                    ]),

                    Grid::make(3)->schema([
                        Forms\Components\TextInput::make('commission_rate')
                            ->label('Percentual fixo (%)')
                            ->numeric()
                            ->default(20.00)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Percentual que o parceiro receberá em todas as propostas.')
                            ->visible(fn (Forms\Get $get) => $get('commission_model') !== 'variable')
                            ->required(fn (Forms\Get $get) => $get('commission_model') !== 'variable'),

                        Forms\Components\TextInput::make('commission_min')
                            ->label('Percentual mínimo (%)')
                            ->numeric()
                            ->default(0.00)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Menor percentual que o parceiro pode definir.')
                            ->visible(fn (Forms\Get $get) => $get('commission_model') === 'variable')
                            ->required(fn (Forms\Get $get) => $get('commission_model') === 'variable'),

                        Forms\Components\TextInput::make('commission_max')
                            ->label('Percentual máximo (%)')
                            ->numeric()
                            ->default(100.00)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Maior percentual que o parceiro pode definir.')
                            ->visible(fn (Forms\Get $get) => $get('commission_model') === 'variable')
                            ->required(fn (Forms\Get $get) => $get('commission_model') === 'variable'),

                        Forms\Components\Placeholder::make('commission_preview')
                            ->label('Resumo')
                            ->content(function (Forms\Get $get) {
                                if ($get('commission_model') === 'variable') {
                                    $min = number_format((float)($get('commission_min') ?? 0), 1);
                                    $max = number_format((float)($get('commission_max') ?? 100), 1);
                                    return new \Illuminate\Support\HtmlString(
                                        "<span class='text-sm'>O parceiro poderá definir entre <strong>{$min}%</strong> e <strong>{$max}%</strong>.</span>"
                                    );
                                }
                                $rate = number_format((float)($get('commission_rate') ?? 20), 1);
                                return new \Illuminate\Support\HtmlString(
                                    "<span class='text-sm'>O parceiro receberá sempre <strong>{$rate}%</strong>.</span>"
                                );
                            })
                            ->columnSpanFull(),
                    ]),
                ]),

            Section::make('Informações do Sistema')
                ->icon('heroicon-o-information-circle')
                ->collapsed()
                ->schema([
                    Grid::make(3)->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Criado em')
                            ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i') ?? '-'),
                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Atualizado em')
                            ->content(fn ($record) => $record?->updated_at?->format('d/m/Y H:i') ?? '-'),
                        Forms\Components\Placeholder::make('deleted_at')
                            ->label('Inativado em')
                            ->content(fn ($record) => $record?->deleted_at?->format('d/m/Y H:i') ?? '-'),
                    ]),
                ])
                ->visibleOn('edit'),
        ]);
    }

    protected static function formatPhone(string $phone): string
    {
        $p = preg_replace('/\D/', '', $phone);
        if (strlen($p) === 10) return '(' . substr($p,0,2) . ') ' . substr($p,2,4) . '-' . substr($p,6);
        if (strlen($p) === 11) return '(' . substr($p,0,2) . ') ' . substr($p,2,5) . '-' . substr($p,7);
        return $phone;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(fn ($record) => $record->logo_url),

                Tables\Columns\TextColumn::make('company_name')
                    ->label('Empresa')
                    ->description(fn ($record) => $record->trade_name ?? '')
                    ->searchable(['company_name', 'trade_name'])
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('distributor.trade_name')
                    ->label('Distribuidor')
                    ->badge()
                    ->color('info')
                    ->placeholder('Direto')
                    ->searchable(),

                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('CNPJ copiado!')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email')
                    ->label('Contato')
                    ->description(fn ($record) => $record->phone ?? '')
                    ->searchable()
                    ->copyable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('city')
                    ->label('Localização')
                    ->formatStateUsing(fn ($record) => $record->city && $record->state
                        ? "{$record->city} / {$record->state}"
                        : ($record->city ?? '-'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('currency')
                    ->label('Moeda')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'USD' => 'success',
                        'PYG' => 'warning',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('commission_label')
                    ->label('Comissão')
                    ->badge()
                    ->color(fn ($record) => $record->commission_model === 'fixed' ? 'success' : 'warning')
                    ->tooltip(fn ($record) => $record->commission_model === 'fixed'
                        ? 'Comissão fixa definida pela IPVDATA'
                        : 'Parceiro define o percentual na proposta'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Usuários')
                    ->counts('users')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Apenas ativos')
                    ->falseLabel('Apenas inativos')
                    ->native(false),

                Tables\Filters\SelectFilter::make('distributor_id')
                    ->label('Distribuidor')
                    ->options(Distributor::where('is_active', true)->orderBy('trade_name')->pluck('trade_name', 'id'))
                    ->native(false)
                    ->placeholder('Todos'),

                Tables\Filters\SelectFilter::make('currency')
                    ->label('Moeda')
                    ->options(['BRL' => 'Real (R$)', 'USD' => 'Dólar (US$)', 'PYG' => 'Guarani (₲)'])
                    ->native(false),

                Tables\Filters\SelectFilter::make('state')
                    ->label('Estado')
                    ->options([
                        'SP'=>'São Paulo','RJ'=>'Rio de Janeiro','MG'=>'Minas Gerais',
                        'RS'=>'Rio Grande do Sul','PR'=>'Paraná','SC'=>'Santa Catarina',
                        'BA'=>'Bahia','GO'=>'Goiás','DF'=>'Distrito Federal','CE'=>'Ceará',
                    ]),

                Tables\Filters\SelectFilter::make('commission_model')
                    ->label('Tipo de comissão')
                    ->options(['fixed' => 'Fixa', 'variable' => 'Variável']),

                Tables\Filters\TrashedFilter::make()->label('Registros excluídos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Editar parceiro')
                    ->icon('heroicon-m-pencil-square'),

                Tables\Actions\Action::make('toggle_active')
                    ->label('')
                    ->tooltip(fn ($record) => $record->is_active ? 'Desativar parceiro' : 'Ativar parceiro')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                    ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->is_active ? 'Desativar parceiro?' : 'Ativar parceiro?')
                    ->modalDescription(fn ($record) => $record->is_active
                        ? 'O parceiro perderá acesso ao sistema.'
                        : 'O parceiro terá acesso restaurado.')
                    ->modalSubmitActionLabel('Confirmar')
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),

                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Excluir parceiro')
                    ->modalHeading('Excluir parceiro')
                    ->modalDescription('Tem certeza? Esta ação pode ser desfeita (soft delete).')
                    ->modalSubmitActionLabel('Sim, excluir'),

                Tables\Actions\RestoreAction::make()
                    ->label('')
                    ->tooltip('Restaurar parceiro')
                    ->modalHeading('Restaurar parceiro')
                    ->modalSubmitActionLabel('Sim, restaurar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados')
                        ->modalHeading('Excluir parceiros selecionados')
                        ->modalDescription('Tem certeza que deseja excluir os selecionados?')
                        ->modalSubmitActionLabel('Sim, excluir'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurar selecionados')
                        ->modalHeading('Restaurar parceiros selecionados')
                        ->modalSubmitActionLabel('Sim, restaurar'),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Ativar selecionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desativar selecionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit'   => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
