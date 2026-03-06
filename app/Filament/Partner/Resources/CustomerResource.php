<?php

namespace App\Filament\Partner\Resources;

use App\Filament\Partner\Resources\CustomerResource\Pages;
use App\Models\Customer;
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

class CustomerResource extends Resource
{
    protected static ?string $model            = Customer::class;
    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?int    $navigationSort   = 1;

    public static function getNavigationLabel(): string
    {
        return __('app.customers.title');
    }

    public static function getModelLabel(): string
    {
        return __('app.customers.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.customers.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->where('partner_id', auth()->user()?->partner_id)
            ->with(['projects']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Busca por CNPJ / CPF')
                ->icon('heroicon-o-magnifying-glass')
                ->schema([
                    Grid::make(4)->schema([
                        Forms\Components\Select::make('document_type')
                            ->label('Tipo')
                            ->options(['cnpj' => 'CNPJ', 'cpf' => 'CPF'])
                            ->default('cnpj')->required()->native(false)->live(),

                        Forms\Components\TextInput::make('document')
                            ->label('Documento')
                            ->mask(fn (Forms\Get $get) => $get('document_type') === 'cpf'
                                ? '999.999.999-99' : '99.999.999/9999-99')
                            ->maxLength(20)
                            ->suffixAction(
                                Action::make('buscar_doc')
                                    ->icon('heroicon-m-magnifying-glass')
                                    ->tooltip('Buscar dados')
                                    ->action(function (Forms\Set $set, Forms\Get $get) {
                                        $doc  = preg_replace('/\D/', '', $get('document') ?? '');
                                        $type = $get('document_type');
                                        if ($type === 'cnpj' && strlen($doc) === 14) {
                                            try {
                                                $ctx  = stream_context_create(['ssl' => ['verify_peer' => false], 'http' => ['timeout' => 10, 'ignore_errors' => true]]);
                                                $data = json_decode(@file_get_contents("https://receitaws.com.br/v1/cnpj/{$doc}", false, $ctx), true);
                                                if (!$data || ($data['status'] ?? '') === 'ERROR') {
                                                    $data = json_decode(@file_get_contents("https://brasilapi.com.br/api/cnpj/v1/{$doc}", false, $ctx), true);
                                                }
                                                if ($data && !isset($data['message']) && ($data['status'] ?? '') !== 'ERROR') {
                                                    $set('name',       $data['razao_social'] ?? $data['nome'] ?? '');
                                                    $set('trade_name', $data['nome_fantasia'] ?? $data['fantasia'] ?? '');
                                                    $set('email',      strtolower($data['email'] ?? ''));
                                                    $set('phone',      $data['ddd_telefone_1'] ?? $data['telefone'] ?? '');
                                                    $set('zipcode',    preg_replace('/\D/', '', $data['cep'] ?? ''));
                                                    $set('address',    trim(($data['logradouro'] ?? '') . ($data['numero'] ? ', ' . $data['numero'] : '')));
                                                    $set('city',       $data['municipio'] ?? '');
                                                    $set('state',      $data['uf'] ?? '');
                                                    Notification::make()->title('Dados preenchidos! ✅')->success()->send();
                                                } else {
                                                    Notification::make()->title('CNPJ não encontrado')->warning()->send();
                                                }
                                            } catch (\Exception $e) {
                                                Notification::make()->title('Erro ao consultar')->danger()->send();
                                            }
                                        }
                                    })
                            )
                            ->columnSpan(2),

                        Forms\Components\Placeholder::make('hint')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString(
                                '<p class="text-sm text-gray-500 mt-2">💡 Digite o CNPJ e clique em 🔍</p>'
                            )),
                    ]),
                ]),

            Section::make('Dados do Cliente')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Razão social / Nome')
                            ->required()->maxLength(255),
                        Forms\Components\TextInput::make('trade_name')
                            ->label('Nome fantasia')->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')->email()->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')->tel()->maxLength(20),
                        Forms\Components\TextInput::make('contact_name')
                            ->label('Nome do contato')->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Cliente ativo')->default(true)->inline(false),
                    ]),
                ]),

            Section::make('Endereço')->icon('heroicon-o-map-pin')->collapsed()->schema([
                Grid::make(3)->schema([
                    Forms\Components\TextInput::make('zipcode')
                        ->label('CEP')->mask('99999-999')->maxLength(10)
                        ->suffixAction(
                            Action::make('buscar_cep')->icon('heroicon-m-magnifying-glass')
                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                    $cep = preg_replace('/\D/', '', $get('zipcode') ?? '');
                                    if (strlen($cep) !== 8) return;
                                    $ctx  = stream_context_create(['ssl' => ['verify_peer' => false]]);
                                    $data = json_decode(@file_get_contents("https://viacep.com.br/ws/{$cep}/json/", false, $ctx), true);
                                    if ($data && !isset($data['erro'])) {
                                        $set('address', $data['logradouro']);
                                        $set('city',    $data['localidade']);
                                        $set('state',   $data['uf']);
                                    }
                                })
                        ),
                    Forms\Components\TextInput::make('city')->label('Cidade')->maxLength(255),
                    Forms\Components\Select::make('state')->label('UF')
                        ->options(['AC'=>'AC','AL'=>'AL','AM'=>'AM','AP'=>'AP','BA'=>'BA','CE'=>'CE','DF'=>'DF','ES'=>'ES','GO'=>'GO','MA'=>'MA','MG'=>'MG','MS'=>'MS','MT'=>'MT','PA'=>'PA','PB'=>'PB','PE'=>'PE','PI'=>'PI','PR'=>'PR','RJ'=>'RJ','RN'=>'RN','RO'=>'RO','RR'=>'RR','RS'=>'RS','SC'=>'SC','SE'=>'SE','SP'=>'SP','TO'=>'TO'])
                        ->searchable()->native(false),
                    Forms\Components\TextInput::make('address')->label('Endereço')->maxLength(255)->columnSpanFull(),
                ]),
            ]),

            Section::make('Observações')->icon('heroicon-o-chat-bubble-left-ellipsis')->collapsed()->schema([
                Forms\Components\Textarea::make('notes')->label('Notas')->rows(4)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Coluna principal — sem limite de largura, cresce
                Tables\Columns\TextColumn::make('name')
                    ->label(__('app.customers.singular'))
                    ->description(fn ($record) => $record->trade_name ?? '')
                    ->searchable(['name', 'trade_name'])
                    ->sortable()
                    ->weight('bold')
                    ->grow(),

                // Documento
                Tables\Columns\TextColumn::make('document_formatted')
                    ->label(__('app.customers.document'))
                    ->searchable('document')
                    ->copyable()
                    ->placeholder('-'),

                // Contato compacto
                Tables\Columns\TextColumn::make('email')
                    ->label(__('app.email'))
                    ->description(fn ($record) => $record->phone ?? '')
                    ->searchable()
                    ->placeholder('-'),

                // Localização
                Tables\Columns\TextColumn::make('city')
                    ->label(__('app.customers.city_state'))
                    ->formatStateUsing(fn ($record) => $record->city && $record->state
                        ? "{$record->city}/{$record->state}" : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                // MRR
                Tables\Columns\TextColumn::make('monthly_value')
                    ->label('MRR')
                    ->formatStateUsing(fn ($record) =>
                        'R$ ' . number_format($record->monthly_value, 2, ',', '.'))
                    ->badge()
                    ->color('success'),

                // Status
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.customers.is_active'))
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('app.status'))
                    ->trueLabel(__('app.active'))->falseLabel(__('app.inactive'))->native(false),
            ])
            ->recordUrl(fn ($record) => static::getUrl('dashboard', ['record' => $record]))
            ->actions([
                Tables\Actions\Action::make('edit_btn')
                    ->label(__('app.edit'))
                    ->tooltip(__('app.customers.edit'))
                    ->icon('heroicon-m-pencil-square')
                    ->color('gray')
                    ->url(fn ($record) => static::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make()
                    ->label('')->tooltip(__('app.delete'))
                    ->modalHeading(__('app.customers.deleted'))
                    ->modalSubmitActionLabel(__('app.confirm')),
                Tables\Actions\RestoreAction::make()
                    ->label('')->tooltip(__('app.refresh')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('app.delete')),
                    Tables\Actions\RestoreBulkAction::make()->label(__('app.refresh')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListCustomers::route('/'),
            'create'    => Pages\CreateCustomer::route('/create'),
            'edit'      => Pages\EditCustomer::route('/{record}/edit'),
            'dashboard' => Pages\CustomerDashboard::route('/{record}/dashboard'),
	    'infra'	=> Pages\CustomerInfra::route('/{record}/infra'),
        ];
    }
}
